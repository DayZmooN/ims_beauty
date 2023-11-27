<?php

namespace App\Service;

use Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;

class GoogleClientService
{
    private $client;

    public function __construct(string $credentialsPath)
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig($credentialsPath);
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
        $this->client->setAccessType('offline');
    }


    public function getClient(): Google_Client
    {
        return $this->client;
    }
}
