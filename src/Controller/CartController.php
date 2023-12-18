<?php

namespace App\Controller;

use App\Entity\Appointements;
use App\Form\AvailabilityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Service\GoogleCalendarService;
use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\JsonResponse;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class CartController extends AbstractController
{
    private $entityManager;
    private $security;
    private $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService, EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->googleCalendarService = $googleCalendarService;
    }


    #[Route('/cart', name: 'app_cart')]
    public function showCart(SessionInterface $session, ServicesRepository $serviceRepository, GoogleCalendarService $googleCalendarService): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // Ajoutez le var_dump ici pour afficher l'heure actuelle
        // $dateTime = new DateTime('now', new DateTimeZone('Europe/Paris'));
        // var_dump($dateTime->format('H:i')); // Cela affichera l'heure actuelle au format 'H:i'
        $cart = $session->get('cart', []);
        $servicesWithForms = [];
        $creneauxParService = []; // Structure pour stocker les créneaux par service

        foreach ($cart as $id) {
            $service = $serviceRepository->find($id);
            if ($service) {
                $servicesWithForms[] = ['service' => $service];
                $googleCalendarSlots = $googleCalendarService->getAvailableSlotsGoogle($id);

                foreach ($googleCalendarSlots as $slot) {
                    // Chaque 'slot' est maintenant un tableau associatif avec 'start' et 'end'
                    $date = (new \DateTime($slot['start']))->format('Y-m-d');
                    $heureDebut = (new \DateTime($slot['start']))->format('H:i:s');
                    $heureFin = (new \DateTime($slot['end']))->format('H:i:s');

                    $creneauxParService[$id][$date][] = ['start' => $heureDebut, 'end' => $heureFin];
                }
            }
        }
        return $this->render('page/cart.html.twig', [
            'servicesWithForms' => $servicesWithForms,
            'creneauxParService' => $creneauxParService,
            'cartItemCount' => count($cart),
        ]);
    }





    #[Route('/load-slots', name: 'load_slots')]
    public function loadSlots(Request $request, GoogleCalendarService $googleCalendarService): JsonResponse
    {
        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 7;

        // Modifier cette fonction pour accepter les paramètres de pagination
        $googleCalendarSlots = $googleCalendarService->getAvailableSlotsGoogle($currentPage, $itemsPerPage);

        return $this->json(['slots' => $googleCalendarSlots]);
    }


    #[Route('/cart/submit', name: 'cart_submit', methods: ['POST'])]
    public function handleCartSubmission(Request $request, SessionInterface $session, ServicesRepository $serviceRepository, EntityManagerInterface $entityManager, GoogleCalendarService $googleCalendarService): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not authenticated'], 403);
        }
        /** @var \App\Entity\Users $user */
        $userName = $user->getFirstName() . ' ' . $user->getLastName();
        $userPhone = $user->getPhone();
        $selectedTimeSlotData = $request->request->all('selectedTimeSlot');
        $cart = $session->get('cart', []);
        $datesSelected = false;

        foreach ($selectedTimeSlotData as $serviceId => $slots) {
            $service = $serviceRepository->find($serviceId);
            if (!$service) {
                continue;
            }

            $serviceDuration = $service->getDuration();
            $googleCalendarSlots = $googleCalendarService->getAvailableSlotsGoogle($serviceId);

            foreach ($slots as $index => $dateTimeString) {
                $dateTime = new \DateTime(trim($dateTimeString));

                // Utilisez une variable pour suivre si un créneau valide a été trouvé pour ce dateTime
                $creneauValideTrouve = false;

                // Vérifiez si le dateTime est inclus dans les créneaux disponibles
                foreach ($googleCalendarSlots as $slot) {
                    $googleCalendarStart = new \DateTime($slot['start']);
                    $googleCalendarEnd = new \DateTime($slot['end']);

                    if ($dateTime >= $googleCalendarStart && $dateTime < $googleCalendarEnd) {
                        $creneauValideTrouve = true;
                        break; // Sortez de la boucle dès qu'un créneau valide est trouvé
                    }
                }
                if ($creneauValideTrouve) {
                    // Le créneau est valide, continuez avec le reste du code
                    // Après la création de l'objet $appointment
                    $appointment = new Appointements();
                    $appointment->setStatus('confirmed');
                    $appointment->setUsers($user);
                    $appointment->setDateTime($dateTime);
                    // Associer le service au rendez-vous
                    $appointment->addService($service);
                    // Maintenant, persister l'objet $appointment
                    try {
                        $entityManager->persist($appointment);
                        $entityManager->flush();
                    } catch (\Exception $e) {
                        // Gérer l'exception
                        continue;
                    }
                    if ($appointment->getId()) {
                        if (($key = array_search($serviceId, $cart)) !== false) {
                            unset($cart[$key]);
                        }
                        $session->set('cart', array_values($cart));
                        $this->addFlash('success', "Rendez-vous confirmé et retiré du panier.");
                    }
                    try {
                        // Mettre à jour les créneaux disponibles après la réservation
                        $googleCalendarSlots = $googleCalendarService->updateAvailableSlots($serviceId, $dateTime);

                        $googleCalendarService->createEvent(
                            $dateTime,
                            $service->getName(),
                            $userName,
                            $userPhone,
                            'description',
                            $serviceDuration
                        );
                        $this->addFlash('success', "Appointment confirmed for " . $dateTime->format('Y-m-d H:i:s'));
                    } catch (\Exception $e) {
                        $this->addFlash('error', "Failed to add event to Google Calendar for " . $service->getName());
                    }
                } else {
                    $this->addFlash('error', "Selected date and time are not available.");
                }
            }
        }
        if (!$datesSelected) {
            $this->addFlash('error', "Sélectionnez au moins une date avant de soumettre le formulaire.");
        }

        return $this->redirectToRoute('app_cart');
    }


    //route for add service 
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get('cart', []);
        if (!in_array($id, $cart)) {
            $cart[] = $id;
            $session->set('cart', $cart); // Update the cart before counting items
            $session->set('cartItemCount', count($cart)); // Update the item count
        } else {
            // For regular request, redirect
            $this->addFlash('success', 'Service ajouté au panier avec succès!');
            return $this->redirect($request->headers->get('referer'));
        }
        if ($request->isXmlHttpRequest()) {
            // For AJAX request, return JSON response
            return $this->json([
                'success' => true,
                'message' => 'Service ajouté au panier avec succès!',
                'cartItemCount' => count($cart) // Send the updated count
            ]);
        }
    }


    // route for delete service
    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get('cart', []);
        if (($key = array_search($id, $cart)) !== false) {
            unset($cart[$key]);
            $session->set('cart', $cart); // Update the cart before counting items
            $session->set('cartItemCount', count($cart)); // Update the item count
        }
        if ($request->isXmlHttpRequest()) {
            // For AJAX request, return JSON response
            return $this->json([
                'success' => true,
                'message' => 'Service retiré du panier avec succès!',
                'cartItemCount' => count($cart) // Send the updated count
            ]);
        } else {
            // For regular request, redirect
            $this->addFlash('success', 'Service retiré du panier avec succès!');
            return $this->redirect($request->headers->get('referer'));
        }
    }
}
