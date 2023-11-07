<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactUsController extends AbstractController
{
    #[Route('/contact_us', name: 'app_contact_us')]
    public function index(): Response
    {
        return $this->render('page/contactUs.html.twig', [
            'controller_name' => 'ContactUsController',
        ]);
    }
}
