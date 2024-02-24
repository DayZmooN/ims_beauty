<?php

namespace App\Service;

use App\Repository\ServicesRepository;

use App\Service\GoogleClientService;
use DateInterval;
use DatePeriod;
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
            'timeMax' => date('c', strtotime('+7 days')),  // Les 5 prochains jours
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        try {
            $events = $service->events->listEvents($this->calendarId, $optParams);
            $availableSlots = $this->calculateAvailableSlots($events, $serviceDuration);

            // Filtrer les samedis et dimanches
            $availableSlots = array_filter($availableSlots, function ($slot) {
                $startDateTime = new DateTime($slot['start']);
                $dayOfWeek = (int)$startDateTime->format('N'); // 1 (for Monday) through 7 (for Sunday)

                // Exclure samedi (6) et dimanche (7)
                return !in_array($dayOfWeek, [6, 7]);
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            return []; // Gestion de l'erreur
        }

   $availableSlotsFormatted = [];
foreach ($availableSlots as $slot) {
    // Vérifier si le tableau $slot contient les clés nécessaires
    if (isset($slot['start']) && isset($slot['end'])) {
        $startDateTime = new DateTime($slot['start']);
        $endDateTime = new DateTime($slot['end']);

        $availableSlotsFormatted[] = [
            'start' => $startDateTime->format('Y-m-d H:i'),
            'end' => $endDateTime->format('Y-m-d H:i')
        ];
    } else {
        // Gérer le cas où les clés 'start' et 'end' ne sont pas présentes dans le tableau $slot
        // Vous pouvez ajouter une logique de gestion des erreurs ou ignorer l'élément problématique
        error_log('Le tableau $slot ne contient pas les clés nécessaires.');
    }
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
        $startDate = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $endDate = new DateTime('+7 day');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $busyTimes = [];
        foreach ($events->getItems() as $event) {
            $busyTimes[] = [
                'start' => new DateTime($event->start->dateTime),
                'end' => new DateTime($event->end->dateTime)
            ];
        }

        $availableSlots = [];
        foreach ($period as $date) {
            for ($hour = 9; $hour <= 17; $hour++) {
                if ($hour >= 11 && $hour < 14) continue;
                for ($minute = 0; $minute < 60; $minute += $serviceDuration) {
                    $slotStart = (clone $date)->setTime($hour, $minute);
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
                            'start' => $slotStart->format('Y-m-d H:i'),
                            'end' => $slotEnd->format('Y-m-d H:i')
                        ];
                    }
                }
            }
        }

        return $availableSlots;
    }


    public function updateAvailableSlots($serviceId, $reservedDateTime)
    {
        $service = new Google_Service_Calendar($this->client);
        $optParams = [
            'timeMin' => $reservedDateTime->format('c'),
            'timeMax' => (clone $reservedDateTime)->modify('+1 day')->format('c'),
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        try {
            $events = $service->events->listEvents($this->calendarId, $optParams);

            foreach ($events->getItems() as $event) {
                $eventStart = new DateTime($event->start->dateTime);
                $eventEnd = new DateTime($event->end->dateTime);

                if ($eventStart == $reservedDateTime) {
                    // Supprimer l'événement ou le marquer comme occupé
                    $service->events->delete($this->calendarId, $event->getId());
                
                    break; 
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            // Gérer l'exception
        }

        // Recharger et retourner les créneaux disponibles mis à jour
        return $this->getAvailableSlotsGoogle($serviceId);
    }



//version complete pas terminer mais qui rasemble getavalide et calcul
// public function getAvailableSlotsGoogle($serviceId)
// {
//     $serviceDuration = $this->serviceRepository->findDurationById($serviceId);

//     $startDate = new DateTime('now', new DateTimeZone('Europe/Paris'));
//     $endDate = new DateTime('+7 day');
//     $interval = new DateInterval('P1D');
//     $period = new DatePeriod($startDate, $interval, $endDate);

//     $optParams = [
//         'timeMin' => $startDate->format('c'),  // Moment actuel
//         'timeMax' => $endDate->format('c'),  // 30 jours à partir d'aujourd'hui
//         'singleEvents' => true,
//         'orderBy' => 'startTime',
//     ];

//     $service = new Google_Service_Calendar($this->client);

//     $availableSlotsFormatted = [];
    
//     try {
//         $events = $service->events->listEvents($this->calendarId, $optParams);
//     } catch (Exception $e) {
//         error_log($e->getMessage());
//         return $availableSlotsFormatted; // Gestion de l'erreur
//     }

//     foreach ($period as $date) {
//         $busyTimes = [];
//         foreach ($events->getItems() as $event) {
//             $busyTimes[] = [
//                 'start' => new DateTime($event->start->dateTime),
//                 'end' => new DateTime($event->end->dateTime)
//             ];
//         }

//         $availableSlots = [];

//         for ($hour = 9; $hour <= 17; $hour++) {
//             if ($hour >= 11 && $hour < 14) continue;
//             for ($minute = 0; $minute < 60; $minute += $serviceDuration) {
//                 $slotStart = (clone $date)->setTime($hour, $minute);
//                 $slotEnd = (clone $slotStart)->modify('+' . $serviceDuration . ' minutes');

//                 $isAvailable = true;
//                 foreach ($busyTimes as $busyTime) {
//                     if ($slotStart < $busyTime['end'] && $slotEnd > $busyTime['start']) {
//                         $isAvailable = false;
//                         break;
//                     }
//                 }

//                 if ($isAvailable) {
//                     $availableSlots[] = [
//                         'start' => $slotStart->format('Y-m-d H:i:s'),
//                         'end' => $slotEnd->format('Y-m-d H:i:s')
//                     ];
//                 }
//             }
//         }

//         foreach ($availableSlots as $slot) {
//             $startDateTime = new DateTime($slot['start']);
//             $endDateTime = new DateTime($slot['end']);

//             $availableSlotsFormatted[] = [
//                 'start' => $startDateTime->format('Y-m-d H:i:s'),
//                 'end' => $endDateTime->format('Y-m-d H:i:s')
//             ];
//         }
//     }

//     return $availableSlotsFormatted;
// }


    public function createEvent(DateTime $dateTime, string $serviceName, string $userName, string $userPhone, string $description, int $serviceDuration)
    {
        $service = new Google_Service_Calendar($this->client);
        $calendarId = $this->calendarId;

        // Cloner pour garder l'heure de début originale
        $eventStart = clone $dateTime;
        $eventStart->setTimezone(new DateTimeZone('Europe/Paris'));

        // Cloner pour garder l'heure de fin originale
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

            // Ajoutez des logs pour suivre les dates
            error_log('Event Start (Europe/Paris): ' . $eventStart->format(DateTime::RFC3339));
            error_log('Event End (Europe/Paris): ' . $eventEnd->format(DateTime::RFC3339));

            return $createdEvent; // Retourne l'événement créé
        } catch (Exception $e) {
            // Gérer ici l'erreur, par exemple en journalisant ou en affichant un message
            // Vous pouvez aussi choisir de propager l'exception ou de retourner false
            throw $e; // ou return false;
        }
    }
}
