<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OurPricesController extends AbstractController
{
    #[Route('/Nos_tarifs', name: 'app_our_prices')]
    public function index(): Response
    {
        return $this->render('page/our_price.html.twig', [
            'controller_name' => 'OurPricesController',
        ]);
    }
}
