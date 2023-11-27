<?php

namespace App\Service;

use App\Repository\ServicesRepository;

use App\Service\GoogleClientService;
use DateTime;
use DateTimeZone;
use Exception;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Client as Google_Client;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Google\Service\Calendar\Event as Google_Service_Calendar_Event;

class GoogleCalendarService
{
    private $client;
    private $calendarId;
    private $googleClientService;
    private $serviceRepository; // Ajoutez cette ligne

    // private $googleApiKey;

    public function __construct(GoogleClientService $googleClientService, string $calendarId, ServicesRepository $serviceRepository)
    {
        $this->googleClientService = $googleClientService;
        $this->client = $this->googleClientService->getClient();
        $this->calendarId = $calendarId;
        $this->serviceRepository = $serviceRepository;


        // Configurez le client Guzzle avec l'option RequestOptions::VERIFY pour désactiver la vérification SSL
        $this->client->setHttpClient(new Client([
            RequestOptions::VERIFY => false,
        ]));
    }

    public function getClient(): Google_Client
    {
        return $this->client;
    }
    public function getAvailableSlotsGoogle($serviceId)
    {
        $serviceDuration = $this->serviceRepository->findDurationById($serviceId);

        $service = new Google_Service_Calendar($this->client);
        $optParams = [
            'timeMin' => date('c'),  // Moment actuel
            'timeMax' => date('c', strtotime('+5 days')),  // Les 5 prochains jours
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        try {
            $events = $service->events->listEvents($this->calendarId, $optParams);
            $availableSlots = $this->calculateAvailableSlots($events, $serviceDuration);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return []; // Gestion de l'erreur
        }

        $availableSlotsFormatted = [];
        foreach ($availableSlots as $slot) {
            $startDateTime = new DateTime($slot['start']);
            $endDateTime = new DateTime($slot['end']);

            $availableSlotsFormatted[] = [
                'start' => $startDateTime->format('Y-m-d H:i:s'),
                'end' => $endDateTime->format('Y-m-d H:i:s')
            ];
        }

        return $availableSlotsFormatted;
    }




    public function getAuthUrl()
    {
        $client = $this->googleClientService->getClient();
        return $client->createAuthUrl();
    }

    private function calculateAvailableSlots($events, $serviceDuration)
    {
        $startDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $endDate = new \DateTime('+5 days');
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($startDate, $interval, $endDate);

        $busyTimes = [];
        foreach ($events->getItems() as $event) {
            $start = new \DateTime($event->start->dateTime);
            $end = new \DateTime($event->end->dateTime);
            $busyTimes[] = ['start' => $start, 'end' => $end];
        }

        $availableSlots = [];
        foreach ($period as $date) {
            // Génération de créneaux de 9h à 17h avec une pause de 11h à 14h
            for ($hour = 9; $hour <= 17; $hour++) {
                // Vérifie si l'heure actuelle est dans la plage de pause
                if ($hour >= 11 && $hour < 14) {
                    continue; // Saute la plage horaire de pause
                }

                for ($minute = 0; $minute < 60; $minute += $serviceDuration) {
                    $this->addSlotIfAvailable($date, $hour, $minute, $busyTimes, $availableSlots, $serviceDuration);
                }
            }
        }

        return $availableSlots;
    }



    private function addSlotIfAvailable($date, $hour, $minute, $busyTimes, &$availableSlots, $serviceDuration)
    {
        $slotStart = clone $date;
        $slotStart->setTime($hour, $minute);
        $slotEnd = (clone $slotStart)->modify('+' . $serviceDuration . ' minutes');

        $isAvailable = true;
        foreach ($busyTimes as $busyTime) {
            if ($slotStart < $busyTime['end'] && $slotEnd > $busyTime['start']) {
                $isAvailable = false;
                break;
            }
        }

        if ($isAvailable) {
            $availableSlots[] = [
                'start' => $slotStart->format('Y-m-d H:i:s'),
                'end' => $slotEnd->format('Y-m-d H:i:s')
            ];
        }
    }

    public function updateAvailableSlots($serviceId, $reservedDateTime)
    {
        // Récupérer la liste des créneaux disponibles actuelle
        $availableSlots = $this->getAvailableSlotsGoogle($serviceId);

        // Supprimer le créneau réservé de la liste des créneaux disponibles
        $formattedReservedDateTime = $reservedDateTime->format('Y-m-d H:i:s');
        $availableSlots = array_filter($availableSlots, function ($slot) use ($formattedReservedDateTime) {
            return $slot['start'] !== $formattedReservedDateTime;
        });


        return $availableSlots;
    }




    public function createEvent(DateTime $dateTime, string $serviceName, string $userName, string $userPhone, string $description, int $serviceDuration)
    {
        $service = new Google_Service_Calendar($this->client);
        $calendarId = $this->calendarId;
        $dateTime->setTimezone(new DateTimeZone('Europe/Paris'));
        $eventStart = clone $dateTime; // Cloner pour garder l'heure de début originale
        $eventEnd = clone $dateTime;
        $eventEnd->modify('+' . $serviceDuration . ' minutes');
        $eventEnd->setTimezone(new DateTimeZone('Europe/Paris'));

        // Création d'un nouvel événement
        $event = new Google_Service_Calendar_Event([
            'summary' => 'Rendez-vous: ' . $serviceName,
            'description' => 'Description: ' . $description . "\n" .
                'Utilisateur: ' . $userName . "\n" .
                'Téléphone: ' . $userPhone,
            'start' => [
                'dateTime' => $eventStart->format(DateTime::RFC3339),
                'timeZone' => 'Europe/Paris',
            ],
            'end' => [
                'dateTime' => $eventEnd->format(DateTime::RFC3339),
                'timeZone' => 'Europe/Paris',
            ],
        ]);

        try {
            $createdEvent = $service->events->insert($calendarId, $event);
            if (!$createdEvent) {
                throw new Exception("Échec de la création de l'événement dans Google Calendar.");
            }
            return $createdEvent; // Retourne l'événement créé
        } catch (Exception $e) {
            // Gérer ici l'erreur, par exemple en journalisant ou en affichant un message
            // Vous pouvez aussi choisir de propager l'exception ou de retourner false
            throw $e; // ou return false;
        }
    }
}
