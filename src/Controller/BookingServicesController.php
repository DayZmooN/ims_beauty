<?php

namespace App\Controller;

use App\Entity\Appointements;
use App\Entity\Categories;
use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\AvailabilityType;
use App\Service\GoogleCalendarService;
use App\Service\GoogleClientService;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;




class BookingServicesController extends AbstractController
{
    private $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    #[Route('/category/{id}/services', name: 'category_list_services')]
    public function listServices(Categories $categories, ServicesRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findBy(['category' => $categories]);

        return $this->render('page/booking_services.html.twig', [
            'category' => $categories,
            'services' => $services,
        ]);
    }


    // public function createEvent(): Response
    // {
    //     // Ici, vous utiliseriez des informations de la requête ou du formulaire pour créer l'événement.
    //     // Par exemple, disons que vous avez déjà les détails de l'événement dans une variable $eventDetails.
    //     $eventDetails = [
    //         'summary' => 'Rendez-vous Beauté',
    //         'start' => '2023-11-06T10:00:00+01:00',
    //         'end' => '2023-11-06T11:00:00+01:00',
    //         // Ajoutez d'autres détails nécessaires ici...
    //     ];

    //     // Utilisez le service pour ajouter l'événement.
    //     $this->calendarService->addEvent($eventDetails);

    //     // Retournez une réponse, par exemple une redirection ou un message de succès.
    //     return $this->redirectToRoute('app_page');
    // }
    // Dans votre contrôleur

    // public function showAvailability(): Response
    // {
    //     $availableSlots = $this->calendarService->getAvailableSlots();

    //     return $this->render('page/availability.html.twig', [
    //         'availableSlots' => $availableSlots,
    //     ]);
    // }


    //pour google agenda 
    // #[Route('/category/{id}/services', name: 'category_list_services')]
    // public function listServices(Request $request, Categories $category, ServicesRepository $serviceRepository, GoogleClientService $googleClientService): Response
    // {
    //     // Assurez-vous que $googleClientService et $calendarId sont définis
    //     $calendarId = 'eb4ac0e920b14d60786c94f71092fdcbd873e34be3814d00d3fbcd8cd737f844@group.calendar.google.com'; // Remplacez par votre véritable ID de calendrier
    //     $googleApiKey = 'AIzaSyAJOE5ji9Sz-bj7ksBG8kWV9BXc1y_wk7E';
    //     $services = $serviceRepository->findBy(['category' => $category]);

    //     // Create the appointment form
    //     $appointmentForm = $this->createForm(AvailabilityType::class);
    //     $appointmentForm->handleRequest($request);

    //     if ($appointmentForm->isSubmitted() && $appointmentForm->isValid()) {

    //         $this->addFlash('success', 'Votre rendez-vous a été enregistré avec succès.');

    //         return $this->redirectToRoute('category_list_services', ['id' => $category->getId()]);
    //     }

    //     // Instanciez GoogleCalendarService avec le bon type de client HTTP
    //     $googleCalendarService = new GoogleCalendarService($googleClientService, $calendarId, $googleApiKey);
    //     $googleCalendarService->getClient()->setHttpClient(new Client([
    //         RequestOptions::VERIFY => false, // Désactivez la vérification SSL
    //     ]));

    //     $availableSlots = $googleCalendarService->getAvailableSlots();

    //     // Pass the form to the template
    //     return $this->render('page/booking_services.html.twig', [
    //         'category' => $category,
    //         'services' => $services,
    //         'appointmentForm' => $appointmentForm->createView(),
    //         'availableSlots' => $availableSlots,
    //     ]);
    // }

    // #[Route('/book/appointment', name: 'book_appointment')]
    // public function bookAppointment(Request $request): Response
    // {
    //     $form = $this->createForm(AvailabilityType::class);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Traitement des données du formulaire
    //         $data = $form->getData();

    //         if ($form->isSubmitted() && $form->isValid()) {
    //             $data = $form->getData();
    //             $appointmentTime = $data['date_time'];

    //             // Création de l'événement dans Google Agenda
    //             $googleCalendarService->addEvent($appointmentTime);

    //             // Enregistrement dans la base de données
    //             $appointment = new Appointements();
    //             $appointment->setTime($appointmentTime);
    //             // Settez d'autres propriétés si nécessaire

    //             $entityManager = $this->getDoctrine()->getManager();
    //             $entityManager->persist($appointment);
    //             $entityManager->flush();

    //             $this->addFlash('success', 'Votre rendez-vous a été enregistré avec succès.');
    //             return $this->redirectToRoute('appointment_success');
    //         }

    //         $this->addFlash('success', 'Votre rendez-vous a été enregistré avec succès.');
    //         return $this->redirectToRoute('app_page');
    //     }

    //     return $this->render('booking/book_appointment.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

}
