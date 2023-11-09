<?php
// src/Controller/GoogleOAuthController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Google_Client;

class GoogleOAuthController extends AbstractController
{
    private $client;

    public function __construct(Google_Client $client)
    {
        $this->client = $client;
    }

    #[Route('/google/login', name: 'google_login')]
    public function login(): RedirectResponse
    {
        $authUrl = $this->client->createAuthUrl();

        return $this->redirect($authUrl);
    }

    #[Route('/google/oauth2callback', name: 'google_oauth2callback')]
    public function oauth2callback(Request $request): RedirectResponse
    {
        $code = $request->query->get('code');

        if ($code) {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($accessToken);

            // Stockez le token d'accès et le token de rafraîchissement si vous en avez un dans la session ou dans la base de données

            // Redirection vers la page souhaitée après l'authentification réussie
            return $this->redirectToRoute('app_page');
        }

        // Gérez le cas où il n'y a pas de code ou s'il y a une erreur
        $this->addFlash('error', 'Erreur lors de la connexion à Google.');
        return $this->redirectToRoute('your_failure_route');
    }

    #[Route('/google/logout', name: 'google_logout')]
    public function logout(): RedirectResponse
    {
        // Nettoyez la session ou la base de données où vous avez stocké les informations d'accès

        return $this->redirectToRoute('your_home_route');
    }
}
