<?php

// src/Controller/CategoriesController.php
namespace App\Controller;

use App\Repository\CategoriesRepository; // Utilisez le nom de classe correct ici
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BreadcrumbService;


class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(CategoriesRepository $categoriesRepository, BreadcrumbService $breadcrumbService): Response
    {
        $categories = $categoriesRepository->findAll();
        $breadcrumbs = $breadcrumbService->getBreadcrumbs();

        return $this->render('page/categories.html.twig', [
            'categories' => $categories,
            'breadcrumbs' => $breadcrumbs,
            'page_name' => 'Nos Soins',
        ]);
    }
}
