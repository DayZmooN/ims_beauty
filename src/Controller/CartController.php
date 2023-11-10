<?php

namespace App\Controller;

use App\Entity\Appointements;
use App\Entity\Users;
use App\Form\AvailabilityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CartController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    // src/Controller/CartController.php

    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request, SessionInterface $session, ServicesRepository $serviceRepository, EntityManagerInterface $entityManager): Response
    {
        $cart = $session->get('cart', []);
        $servicesWithForms = [];
        $availableTimeSlots = $this->getAvailableTimeSlots($entityManager);

        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            foreach ($cart as $serviceId) {
                $timeSlotFieldName = 'selectedTimeSlot_' . $serviceId;
                $selectedTimeSlot = $request->request->get($timeSlotFieldName);

                if ($selectedTimeSlot) {
                    $dateTime = new \DateTime($selectedTimeSlot);
                    $service = $serviceRepository->find($serviceId);

                    if ($this->isDateTimeAvailable($dateTime, $entityManager) && $service) {
                        $appointment = new Appointements();
                        $appointment->setStatus('confirmé');
                        $appointment->setUsers($user);
                        $appointment->setDateTime($dateTime);
                        $appointment->addService($service);

                        $entityManager->persist($appointment);

                        if (($key = array_search($serviceId, $cart)) !== false) {
                            unset($cart[$key]);
                        }
                    } else {
                        $this->addFlash('error', "Ce créneau horaire n'est plus disponible ou le service n'est pas valide pour " . $dateTime->format('Y-m-d H:i'));
                    }
                }
            }

            $entityManager->flush();
            $session->set('cart', $cart);
            $this->addFlash('success', "Rendez-vous confirmés");
        }

        foreach ($cart as $id) {
            $service = $serviceRepository->find($id);
            if ($service) {
                $servicesWithForms[] = [
                    'service' => $service,
                    'availableTimeSlots' => $availableTimeSlots,
                ];
            }
        }

        return $this->render('cart/index.html.twig', [
            'servicesWithForms' => $servicesWithForms,
        ]);
    }

    // ... autres méthodes, y compris getAvailableTimeSlots et isDateTimeAvailable ...


    // src/Controller/CartController.php

    private function getAvailableTimeSlots(EntityManagerInterface $entityManager)
    {
        $datesWithTimeSlots = [];
        $startDate = new \DateTime(); // Date d'aujourd'hui
        $endDate = (clone $startDate)->modify('+5 days'); // 5 jours à partir d'aujourd'hui

        for ($date = clone $startDate; $date < $endDate; $date->modify('+1 day')) {
            $timeSlotsForDate = [];
            foreach (range(8, 17) as $hour) {
                $dateTime = (clone $date)->setTime($hour, 0); // Chaque heure pleine

                if ($this->isDateTimeAvailable($dateTime, $entityManager)) {
                    $timeSlotsForDate[] = $dateTime->format('H:i');
                }
            }
            if (!empty($timeSlotsForDate)) {
                $datesWithTimeSlots[$date->format('Y-m-d')] = $timeSlotsForDate;
            }
        }

        return $datesWithTimeSlots;
    }


    private function isDateTimeAvailable($dateTime, EntityManagerInterface $entityManager)
    {
        // Vérifier si un rendez-vous avec la même date et heure existe déjà
        $existingAppointment = $entityManager->getRepository(Appointements::class)->findOneBy(['DateTime' => $dateTime]);

        // Si aucun rendez-vous n'est trouvé pour cette date et heure, le créneau est disponible
        return $existingAppointment === null;
    }

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
