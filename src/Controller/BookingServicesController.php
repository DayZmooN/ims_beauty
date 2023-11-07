<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingServicesController extends AbstractController
{
    #[Route('/booking/services', name: 'app_booking_services')]
    public function index(): Response
    {
        return $this->render('booking_services/booking_services.html.twig', [
            'controller_name' => 'BookingServicesController',
        ]);
    }
    #[Route('/category/{id}/services', name: 'category_list_services')]
    public function listServices(Categories $categories, ServicesRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findBy(['category' => $categories]);

        return $this->render('page/booking_services.html.twig', [
            'category' => $categories,
            'services' => $services,
        ]);
    }
}
