<?php

namespace App\Service;

use App\Service\GoogleClientService;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Client as Google_Client;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

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
        $this->googleApiKey = $googleApiKey; // Utilisez la clé API passée en paramètre

        $this->client = $this->googleClientService->getClient();

        // Configurez le client Guzzle avec l'option RequestOptions::VERIFY pour désactiver la vérification SSL
        $this->client->setHttpClient(new Client([
            RequestOptions::VERIFY => false, // Désactive la vérification SSL
        ]));
    }

    public function getClient(): Google_Client
    {
        return $this->client;
    }

    public function getAvailableSlots()
    {
        // Utilisez la classe importée pour créer une instance de Google_Service_Calendar
        $service = new Google_Service_Calendar($this->client);
        $calendarId = $this->calendarId;

        // Définissez la plage de temps pour laquelle vous voulez vérifier les disponibilités
        // Par exemple, les 7 prochains jours
        $optParams = [
            'timeMin' => date('c'), // maintenant
            'timeMax' => date('c', strtotime('+7 days')), // dans 7 jours
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        // Obtenez les événements actuels
        $events = $service->events->listEvents($calendarId, $optParams);
        $availableSlots = []; // Initialisez le tableau des créneaux disponibles

        $busyTimes = [];
        foreach ($events->getItems() as $event) {
            $start = $event->start->dateTime ?: $event->start->date;
            $end = $event->end->dateTime ?: $event->end->date;
            // Ajouter les heures de début et de fin à un tableau des heures occupées
            $busyTimes[] = ['start' => $start, 'end' => $end];
        }

        // ... Logique pour calculer les créneaux disponibles basés sur les heures occupées

        return $availableSlots;
    }
}
