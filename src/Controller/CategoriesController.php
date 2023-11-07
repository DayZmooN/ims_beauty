<?php

// src/Controller/CategoriesController.php
namespace App\Controller;

use App\Repository\CategoriesRepository; // Utilisez le nom de classe correct ici
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findAll();

        return $this->render('page/categories.html.twig', [
            'categories' => $categories,
        ]);
    }
}
