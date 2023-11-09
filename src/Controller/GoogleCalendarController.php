<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Google_Client;
use App\Service\GoogleCalendarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GoogleCalendarController extends AbstractController
{
    #[Route('/google/calendar', name: 'google_calendar')]
    public function index(): Response
    {
        $client = new Google_Client();
        $client->setAuthConfig('../credentials/client_secret_207164739117-trp0er85ho9ggk8dqnpd1e3jjl1pk5hb.apps.googleusercontent.com.json');
        $client->setScopes(['https://www.googleapis.com/auth/calendar']); // Utilisez $client au lieu de $this->client
        return $this->redirectToRoute('some_route_after_success');
    }


    #[Route('/google/auth', name: 'google_auth')]
    public function googleAuth(Google_Client $googleClient): Response
    {
        // Après avoir configuré le client avec les scopes et les URI nécessaires
        $authUrl = $googleClient->createAuthUrl();

        // Redirection vers l'URL d'authentification de Google
        return $this->redirect($authUrl);
    }

    #[Route('/google/oauth2callback', name: 'google_oauth2callback')]
    public function googleOauth2Callback(Request $request, Google_Client $googleClient): Response
    {
        $code = $request->query->get('code');
        if ($code) {
            // Échangez le code contre un token
            $accessToken = $googleClient->fetchAccessTokenWithAuthCode($code);

            // Stockez le token d'accès et le token de rafraîchissement si vous en avez un
            // ...

            // Redirection vers la page souhaitée
            return $this->redirectToRoute('some_route_after_success');
        }

        // Gérez le cas où il n'y a pas de code ou s'il y a une erreur
        // ...

        return $this->redirectToRoute('error_route');
    }
}
