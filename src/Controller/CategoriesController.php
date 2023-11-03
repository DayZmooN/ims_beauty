<?php

// src/Controller/CategoriesController.php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Services;
use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    // #[Route('/category/{id}', name: 'category_services')]
    // public function showCategory(Categories  $category, ServicesRepository $serviceRepository): Response
    // {
    //     // Récupère tous les services pour la catégorie donnée
    //     $services = $serviceRepository->findBy(['category' => $category]);

    //     // Assurez-vous que le template existe et reflète la structure que vous souhaitez afficher
    //     return $this->render('page/services.html.twig', [
    //         'category' => $category,
    //         'services' => $services,
    //     ]);
    // }

    #[Route('/service/{id}', name: 'service_detail')]
    public function showService(Services $service): Response
    {
        // Assurez-vous que votre entité Service est configurée correctement pour permettre cette opération
        return $this->render('page/services.html.twig', [
            'service' => $service,
        ]);
    }
}
