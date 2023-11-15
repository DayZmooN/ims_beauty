<?php

namespace App\Service;

use App\Service\GoogleClientService;
use DateTime;
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
    private $googleApiKey;

    public function __construct(GoogleClientService $googleClientService, string $calendarId, string $googleApiKey)
    {
        $this->googleClientService = $googleClientService;
        $this->calendarId = $calendarId;
        $this->googleApiKey = $googleApiKey;

        $this->client = $this->googleClientService->getClient();

        // Configurez le client Guzzle avec l'option RequestOptions::VERIFY pour désactiver la vérification SSL
        $this->client->setHttpClient(new Client([
            RequestOptions::VERIFY => false,
        ]));
    }

    public function getClient(): Google_Client
    {
        return $this->client;
    }

    public function getAvailableSlotsGoogle()
    {

        $service = new Google_Service_Calendar($this->client);
        $optParams = [
            'timeMin' => date('c'),
            'timeMax' => date('c', strtotime('+5 days')),
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        $events = $service->events->listEvents($this->calendarId, $optParams);
        $availableSlots = $this->calculateAvailableSlots($events);

        // Dans la méthode `getAvailableSlotsGoogle` :
        $availableSlotsFormatted = [];
        foreach ($availableSlots as $slot) {
            $slotDateTime = new DateTime($slot);
            $availableSlotsFormatted[] = $slotDateTime->format('Y-m-d H:i:s');
        }
        return $availableSlotsFormatted;


        return $availableSlots;
    }

    private function calculateAvailableSlots($events)
    {
        $startDate = new \DateTime();
        $endDate = new \DateTime('+3 days');
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
            // Vérifier que la date est ultérieure à la date actuelle
            if ($date >= new \DateTime()) {
                foreach (range(8, 17) as $hour) {
                    $slotStart = clone $date;
                    $slotStart->setTime($hour, 0);
                    $slotEnd = clone $slotStart;
                    $slotEnd->setTime($hour + 1, 0);

                    $isAvailable = true;
                    foreach ($busyTimes as $busyTime) {
                        if ($slotStart < $busyTime['end'] && $slotEnd > $busyTime['start']) {
                            $isAvailable = false;
                            break;
                        }
                    }

                    if ($isAvailable) {
                        $availableSlots[] = $slotStart->format('Y-m-d H:i:s');
                    }
                }
            }
        }
        return $availableSlots;
    }

    public function createEvent(DateTime $dateTime, string $serviceName, string $userName, string $userPhone, string $description)
    {
        $service = new Google_Service_Calendar($this->client);
        $calendarId = $this->calendarId;

        // Création d'un nouvel événement
        $event = new Google_Service_Calendar_Event([
            'summary' => 'Rendez-vous: ' . $serviceName,
            'description' => 'Description: ' . $description . "\n" .
                'Utilisateur: ' . $userName . "\n" .
                'Téléphone: ' . $userPhone,
            'start' => [
                'dateTime' => $dateTime->format(DateTime::RFC3339),
                'timeZone' => 'Europe/Paris',
            ],
            'end' => [
                // Définissez ici l'heure de fin. Exemple : 1 heure après le début
                'dateTime' => $dateTime->modify('+1 hour')->format(DateTime::RFC3339),
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
