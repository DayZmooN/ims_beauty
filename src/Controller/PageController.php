<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private $categoriesRepository;

    public function __construct(CategoriesRepository $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    #[Route('/', name: 'app_page')]
    public function index(): Response
    {
        $categories = $this->categoriesRepository->findAll();

        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
            'categories' => $categories,
        ]);
    }
    #[Route('/category/{id}/services', name: 'category_list_services')]
    public function listServices(Categories $categories, ServicesRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findBy(['category' => $categories]);

        return $this->render('page/CategoryListeServices.html.twig', [
            'category' => $categories,
            'services' => $services,
        ]);
    }
}
