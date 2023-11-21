<?php

namespace App\Controller;

use App\Entity\Users; // Import the Users entity
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();

        return $this->render('page/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $user,
        ]);
    }

    #[Route('/update-user-data', name: 'update_user_data')]
    public function updateUserData(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Get the currently logged-in user
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Handle the form submission if it's a POST request
        // Handle the form submission if it's a POST request
        if ($request->isMethod('POST')) {
            // Retrieve and update the user's data based on the submitted form data
            $user->setFirstName($request->request->get('first_name'));
            $user->setLastName($request->request->get('last_name'));
            $user->setPhone($request->request->get('phone'));
            $user->setEmail($request->request->get('email'));
            $user->setDateOfBith(new \DateTime($request->request->get('date_of_birth')));
            // Update other user data fields as needed

            // Persist the changes to the database
            $entityManager->flush();

            // Add success flash message
            $this->addFlash('success', 'Vos informations ont été changer avec succès !');

            // Return a JSON response with the updated user data
            $userData = [
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                // Include other user data fields as needed
            ];

            return new JsonResponse(['message' => 'User data updated successfully', 'user_data' => $userData], JsonResponse::HTTP_OK);
        }



        // Return a JSON response indicating that it's not a valid request
        return new JsonResponse(['message' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
    }
}
