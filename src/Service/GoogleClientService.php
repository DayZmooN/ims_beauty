<?php

namespace App\Service;

use Google_Client;
use App\Service\GoogleCalendarService;
use Google_Service_Calendar;


class GoogleClientService
{
    private $client;

    public function __construct(string $credentialsPath)
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig($credentialsPath);
        $this->client->setScopes(['https://www.googleapis.com/auth/calendar']);
    }

    public function getClient(): Google_Client
    {
        return $this->client;
    }
}
