<?php


namespace App\Controller;

use App\Entity\Services;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    #[Route('/service/{id}', name: 'service_detail')]
    public function showService(Services $service): Response
    {
        // Assurez-vous que votre entité Service est configurée correctement pour permettre cette opération
        return $this->render('page/services.html.twig', [
            'service' => $service,

        ]);
    }
}
