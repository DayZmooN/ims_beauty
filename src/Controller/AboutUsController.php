<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BreadcrumbService;


class AboutUsController extends AbstractController
{
    #[Route('/about_Us', name: 'app_about_us')]
    public function index(BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbs = $breadcrumbService->getBreadcrumbs();

        return $this->render('page/aboutUs.html.twig', [
            'controller_name' => 'AboutUsController',
            'breadcrumbs' => $breadcrumbs,
            'page_name' => 'À Propos',
        ]);
    }
}
