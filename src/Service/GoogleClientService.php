<?php

namespace App\Service;

use Google_Client;

class GoogleClientService
{
    private $client;

    public function __construct(string $credentialsPath, string $apiKey)
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig($credentialsPath);
        $this->client->setScopes(['https://www.googleapis.com/auth/calendar']);

        // Ajoutez cette ligne pour inclure la clé API
        $this->client->setDeveloperKey($apiKey);
    }

    public function getClient(): Google_Client
    {
        return $this->client;
    }
}
