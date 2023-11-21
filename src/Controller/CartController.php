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
    public function index(Request $request, SessionInterface $session, ServicesRepository $serviceRepository, EntityManagerInterface $entityManager, GoogleCalendarService $googleCalendarService): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        /** @var \App\Entity\Users $user */

        $userName = $user->getFirstName() . ' ' . $user->getLastName();
        $userPhone = $user->getPhone();
        $cart = $session->get('cart', []);

        // Récupération des créneaux disponibles dans Google Agenda
        $googleCalendarSlots = $googleCalendarService->getAvailableSlotsGoogle();

        // Traitement du formulaire lorsqu'il est soumis
        if ($request->isMethod('POST')) {
            $selectedTimeSlotData = $request->request->all('selectedTimeSlot');
            $datesSelected = false;

            // Vérification si au moins une date est sélectionnée
            foreach ($selectedTimeSlotData as $dateTimeString) {
                $dateTime = new \DateTime($dateTimeString);
                if (in_array($dateTime->format('Y-m-d H:i:s'), $googleCalendarSlots)) {
                    $datesSelected = true;
                    break;
                }
            }

            if ($datesSelected) {
                foreach ($selectedTimeSlotData as $serviceId => $dateTimeString) {
                    $service = $serviceRepository->find($serviceId);
                    $dateTime = new \DateTime($dateTimeString);

                    // Vérifier la disponibilité du créneau dans Google Agenda
                    if (in_array($dateTime->format('Y-m-d H:i:s'), $googleCalendarSlots)) {
                        $appointment = new Appointements();
                        $appointment->setStatus('confirmed');
                        $appointment->setUsers($user);
                        $appointment->setDateTime($dateTime);

                        try {
                            $entityManager->persist($appointment);
                            $entityManager->flush();
                            error_log("Appointment saved to database with ID: " . $appointment->getId());
                        } catch (\Exception $e) {
                            error_log("Error saving appointment: " . $e->getMessage());
                            continue;
                        }

                        if ($appointment->getId()) {
                            // Retirer le service du panier si le rendez-vous est enregistré
                            if (($key = array_search($serviceId, $cart)) !== false) {
                                unset($cart[$key]);
                            }
                            $session->set('cart', array_values($cart));
                            $this->addFlash('success', "Rendez-vous confirmé et retiré du panier.");
                        }

                        // Tenter d'ajouter l'événement à Google Agenda
                        try {
                            $this->googleCalendarService->createEvent($dateTime, $service->getName(), $userName, $userPhone, 'description');
                            $this->addFlash('success', "Appointment confirmed for " . $dateTime->format('Y-m-d H:i'));
                        } catch (\Exception $e) {
                            $this->addFlash('error', "Failed to add event to Google Calendar for " . $service->getName());
                        }
                    } else {
                        $this->addFlash('error', "Selected date and time are not available.");
                    }
                }
            } else {
                $this->addFlash('error', "Sélectionnez au moins une date avant de soumettre le formulaire.");
            }
        }

        // Préparer la liste des services pour la vue
        $servicesWithForms = [];
        foreach ($cart as $id) {
            $service = $serviceRepository->find($id);
            if ($service) {
                $servicesWithForms[] = ['service' => $service];
            }
        }

        // Rendre la vue avec les données mises à jour
        return $this->render('page/cart.html.twig', [
            'servicesWithForms' => $servicesWithForms,
            'googleCalendarSlots' => $googleCalendarSlots,
            'cartItemCount' => count($cart), // Add this line
        ]);
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
        }

        if ($request->isXmlHttpRequest()) {
            // For AJAX request, return JSON response
            return $this->json([
                'success' => true,
                'message' => 'Service ajouté au panier avec succès!',
                'cartItemCount' => count($cart) // Send the updated count
            ]);
        } else {
            // For regular request, redirect
            $this->addFlash('success', 'Service ajouté au panier avec succès!');
            return $this->redirect($request->headers->get('referer'));
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
                'message' => 'Service ajouté au panier avec succès!',
                'cartItemCount' => count($cart) // Send the updated count
            ]);
        } else {
            // For regular request, redirect
            $this->addFlash('success', 'Service ajouté au panier avec succès!');
            return $this->redirect($request->headers->get('referer'));
        }
    }
}
