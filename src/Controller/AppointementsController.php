<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppointementsController extends AbstractController
{
    #[Route('/appointements', name: 'app_appointements')]
    public function index(): Response
    {
        return $this->render('appointements/index.html.twig', [
            'controller_name' => 'AppointementsController',
        ]);
    }
}
