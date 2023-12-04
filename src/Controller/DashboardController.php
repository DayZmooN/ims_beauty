<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Repository\AppointementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


class DashboardController extends AbstractController
{
    private $appointementsRepository;
    private $csrfTokenManager;

    public function __construct(AppointementsRepository $appointementsRepository, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->appointementsRepository = $appointementsRepository;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        // Récupérez l'utilisateur actuellement connecté
        /** @var Users $user */
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
        // Récupérez les rendez-vous à venir et passés pour cet utilisateur
        $upcomingAppointments = $this->appointementsRepository->findUpcomingByUser($user);       
        $pastAppointments = $this->appointementsRepository->findPastByUser($user);
        // Renvoyez la vue du tableau de bord avec les données de l'utilisateur et les rendez-vous
        return $this->render('page/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $user,
            'upcomingAppointments' => $upcomingAppointments,
            'pastAppointments' => $pastAppointments,
        ]);
    }

    #[Route('/update-user-data', name: 'update_user_data')]
    public function updateUserData(Request $request, EntityManagerInterface $entityManager,CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        // Récupérez l'utilisateur actuellement connecté
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();
        
        if (!$user) {
            // Si non; redirige vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Vérifiez le jeton CSRF
        $token = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('update_user_data', $token))) {
            return new JsonResponse(['message' => 'Invalid CSRF token'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Gérez la soumission du formulaire s'il s'agit d'une requête POST
        if ($request->isMethod('POST')) {
            // Récupérez et mettez à jour les données de l'utilisateur en fonction des données du formulaire soumis
            $user->setFirstName($request->request->get('first_name'));
            $user->setLastName($request->request->get('last_name'));
            $user->setPhone($request->request->get('phone'));
            $user->setEmail($request->request->get('email'));
            $user->setDateOfBith(new \DateTime($request->request->get('date_of_birth')));
            // Si d'autres champs sont ajoutés à l'avenir; ajouter ici.

            // Envoyer les modifications dans la base de données
            $entityManager->flush();
            // Ajoutez un message flash de réussite
            $this->addFlash('success', 'Vos informations ont été changer avec succès !');
            // Renvoyez une réponse JSON avec les données de l'utilisateur mises à jour
            $userData = [
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'phone' => $user->getPhone(),
                'email' => $user->getEmail(), 
                'date_of_birth' => $user->getDateOfBith()->format('Y-m-d')
                // Si d'autres champs sont ajoutés à l'avenir; ajouter ici.
            ];
            return new JsonResponse(['message' => 'Vos informations ont été changer avec succès !', 'user_data' => $userData], JsonResponse::HTTP_OK);
        }
        // Renvoyez une réponse JSON indiquant qu'il ne s'agit pas d'une requête valide
        return new JsonResponse(['message' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
    }
}
