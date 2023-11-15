<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BreadcrumbService;
use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ServicesRepository; // Import the ServicesRepository

class ServicesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/category/{categoryId}/services', name: 'app_services')]
    public function index($categoryId, BreadcrumbService $breadcrumbService, ServicesRepository $servicesRepository): Response
    {
        $breadcrumbs = $breadcrumbService->getBreadcrumbs();
        $categoryName = $breadcrumbService->getCategoryName($categoryId);

        // Fetch the category entity based on categoryId
        $category = $this->entityManager->getRepository(Categories::class)->find($categoryId);

        // Fetch services related to the category
        $services = $servicesRepository->findBy(['category' => $category]);

        return $this->render('page/booking_services.html.twig', [
            'controller_name' => 'ServicesController',
            'breadcrumbs' => $breadcrumbs,
            'page_name' => $categoryName, // Dynamic page name based on category
            'category' => $category,
            'services' => $services, // Pass the services variable to the template
        ]);
    }
}