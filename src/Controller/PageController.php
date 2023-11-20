<?php

namespace App\Controller;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BreadcrumbService;


class PageController extends AbstractController
{
    private $categoriesRepository;
    private $entityManager;

    public function __construct(CategoriesRepository $categoriesRepository, EntityManagerInterface $entityManager)
    {
        $this->categoriesRepository = $categoriesRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_page')]
    public function homepage(): Response
    {
        $categories = $this->categoriesRepository->findAll();

        return $this->render('page/homepage.html.twig', [
            'controller_name' => 'PageController',
            'categories' => $categories,
        ]);
    }

    #[Route('/mention-legales', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render('static/mention-légales.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/a-propos', name: 'app_about_us')]
    public function aboutUs(BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbs = $breadcrumbService->getBreadcrumbs();

        return $this->render('/static/a-propos.html.twig', [
            'controller_name' => 'PageController',
            'breadcrumbs' => $breadcrumbs,
            'page_name' => 'À Propos',
        ]);
    }

    #[Route('/nos-soins', name: 'app_categories')]
    public function soins(CategoriesRepository $categoriesRepository, BreadcrumbService $breadcrumbService): Response
    {
        $categories = $categoriesRepository->findAll();
        $breadcrumbs = $breadcrumbService->getBreadcrumbs();

        return $this->render('page/nos-soins.html.twig', [
            'categories' => $categories,
            'breadcrumbs' => $breadcrumbs,
            'page_name' => 'Nos Soins',
        ]);
    }

    #[Route('/nos-tarifs', name: 'app_our_prices')]
    public function tarifs(BreadcrumbService $breadcrumbService, CategoriesRepository $categoryRepository,ServicesRepository $servicesRepository): Response
    {
        $breadcrumbs = $breadcrumbService->getBreadcrumbs();
        $categories = $categoryRepository->findAll();
        $services = $servicesRepository->findBy(['category' => $categories]);

        return $this->render('page/nos-tarifs.html.twig', [
            'controller_name' => 'OurPricesController',
            'breadcrumbs' => $breadcrumbs,
            'page_name' => 'Nos Tarifs',
            'categories' => $categories,
            'services' => $services,
        ]);
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

        return $this->render('page/services-details.html.twig', [
            'controller_name' => 'ServicesController',
            'breadcrumbs' => $breadcrumbs,
            'page_name' => $categoryName, // Dynamic page name based on category
            'category' => $category,
            'services' => $services, // Pass the services variable to the template
        ]);
    }
}
