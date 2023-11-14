<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BreadcrumbService;
use App\Repository\CategoriesRepository;

class OurPricesController extends AbstractController
{
    #[Route('/Nos_tarifs', name: 'app_our_prices')]
    public function index(BreadcrumbService $breadcrumbService, CategoriesRepository $categoryRepository): Response
    {
        $breadcrumbs = $breadcrumbService->getBreadcrumbs();
        $categories = $categoryRepository->findAll();

        return $this->render('page/our_price.html.twig', [
            'controller_name' => 'OurPricesController',
            'breadcrumbs' => $breadcrumbs,
            'page_name' => 'Nos Tarifs',
            'categories' => $categories,
        ]);
    }
}
