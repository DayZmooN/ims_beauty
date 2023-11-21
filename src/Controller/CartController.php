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
        ]);
    }






    // private function getAvailableTimeSlots($selectedMonth = null)
    // {
    //     $googleCalendarSlots = $this->googleCalendarService->getAvailableSlotsGoogle();
    //     $formattedSlots = [];
    //     foreach ($googleCalendarSlots as $slot) {
    //         $dateTime = new DateTime($slot);
    //         $date = $dateTime->format('Y-m-d');
    //         $time = $dateTime->format('H:i');
    //         $formattedSlots[$date][] = $time;
    //     }
    //     return $formattedSlots;
    // }







    // private function isDateTimeAvailable($dateTime, $googleCalendarSlots)
    // {
    //     $currentDateTime = new DateTime();
    //     $currentHour = $currentDateTime->format('H:i');
    //     $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

    //     if (in_array($formattedDateTime, $googleCalendarSlots)) {
    //         return false; // Le créneau est occupé dans Google Agenda
    //     }

    //     if ($dateTime->format('H:i') <= $currentHour) {
    //         return false; // Le créneau est déjà passé
    //     }

    //     $dateTimeWithBuffer = clone $dateTime;
    //     $dateTimeWithBuffer->modify('-1 hour');
    //     if ($dateTimeWithBuffer <= $currentDateTime) {
    //         return false; // L'heure actuelle est moins d'une heure avant le rendez-vous
    //     }

    //     return true; // Le créneau est disponible
    // }

    // private function isDateTimeAvailable($dateTime, $googleCalendarSlots)
    // {
    //     $currentDateTime = new DateTime();
    //     $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

    //     if (in_array($formattedDateTime, $googleCalendarSlots)) {
    //         return false; // Le créneau est occupé dans Google Agenda
    //     }

    //     // Vérifiez si le créneau est dans le futur
    //     return $dateTime > $currentDateTime;
    // }


    // private function getLocalAvailableTimeSlots($googleCalendarSlots, $selectedMonth = null)
    // {
    //     $datesWithTimeSlots = [];
    //     $currentYear = date('Y');

    //     if ($selectedMonth) {
    //         $startDate = new \DateTime($currentYear . '-' . $selectedMonth . '-01');
    //         $endDate = (clone $startDate)->modify('+1 month');
    //     } else {
    //         $startDate = new \DateTime(); // Aujourd'hui
    //         $endDate = (clone $startDate)->modify('+5 days'); // 5 jours à partir d'aujourd'hui
    //     }

    //     for ($date = clone $startDate; $date < $endDate; $date->modify('+1 day')) {
    //         $timeSlotsForDate = [];
    //         foreach (range(8, 17) as $hour) {
    //             $dateTime = (clone $date)->setTime($hour, 0); // Chaque heure pleine

    //             if ($this->isDateTimeAvailable($dateTime, $googleCalendarSlots)) {
    //                 $timeSlotsForDate[] = $dateTime->format('H:i');
    //             }
    //         }
    //         if (!empty($timeSlotsForDate)) {
    //             $datesWithTimeSlots[$date->format('Y-m-d')] = $timeSlotsForDate;
    //         }
    //     }

    //     return $datesWithTimeSlots;
    // }


    // private function mergeTimeSlots($dbSlots, $googleCalendarSlots)
    // {
    //     $mergedSlots = [];

    //     // Convertir les créneaux de Google Calendar en un format comparable
    //     $formattedGoogleSlots = [];
    //     foreach ($googleCalendarSlots as $slot) {
    //         $dateTime = new DateTime($slot);
    //         $formattedGoogleSlots[] = $dateTime->format('Y-m-d H:i:s');
    //     }

    //     // Parcourir et fusionner les créneaux de la BDD avec ceux de Google Calendar
    //     foreach ($dbSlots as $date => $timeSlots) {
    //         foreach ($timeSlots as $timeSlot) {
    //             $dateTimeString = $date . ' ' . $timeSlot;

    //             // Ajouter le créneau si ce n'est pas en conflit avec ceux de Google Calendar
    //             if (!in_array($dateTimeString, $formattedGoogleSlots)) {
    //                 if (!isset($mergedSlots[$date])) {
    //                     $mergedSlots[$date] = [];
    //                 }
    //                 $mergedSlots[$date][] = $timeSlot;
    //             }
    //         }
    //     }

    //     return $mergedSlots;
    // }


    //route for add service 
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get('cart', []);

        if (!in_array($id, $cart)) {
            $cart[] = $id;
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Service ajouté au panier avec succès!');

        return $this->redirect($request->headers->get('referer'));
    }

    // #[Route('/cart/checkout', name: 'cart_checkout')]
    // public function checkout(Request $request, SessionInterface $session, ServicesRepository $servicesRepository): Response
    // {
    //     // Créez et gérez le formulaire de disponibilité
    //     $form = $this->createForm(AvailabilityType::class);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Ici, vous traiterez la prise de rendez-vous après soumission du formulaire
    //         // Par exemple, enregistrez les données en base de données et videz le panier
    //     }

    //     // Récupérez les services du panier pour les afficher sur la page de paiement
    //     $cart = $session->get('cart', []);
    //     $servicesWithDetails = [];
    //     foreach ($cart as $id) {
    //         $service = $servicesRepository->find($id);
    //         if ($service) {
    //             $servicesWithDetails[] = $service; // On suppose qu'il s'agit d'un tableau d'objets Service
    //         }
    //     }

    //     // Retournez la vue avec le panier et le formulaire
    //     return $this->render('cart/index.html.twig', [
    //         'services' => $servicesWithDetails,
    //         'availabilityForm' => $form->createView(),
    //     ]);
    // }

    // route for delete service
    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, SessionInterface $session): Response
    {
        // Récupérez le panier de la session
        $cart = $session->get('cart', []);

        // Trouvez l'index de l'élément dans le tableau
        if (($key = array_search($id, $cart)) !== false) {
            // Si l'article est dans le panier, retirez-le
            unset($cart[$key]);
        }

        // Ré-indexez le tableau après la suppression
        $cart = array_values($cart);

        // Enregistrez le panier mis à jour dans la session
        $session->set('cart', $cart);

        // Ajoutez un message flash pour confirmer la suppression
        $this->addFlash('success', 'Service retiré du panier avec succès.');

        // Redirigez l'utilisateur vers la vue du panier
        return $this->redirectToRoute('app_cart');
    }
}
