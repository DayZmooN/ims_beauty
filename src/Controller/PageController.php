<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private $CategoriesRepository;

    public function __construct(CategoriesRepository $CategoriesRepository)
    {
        $this->CategoriesRepository = $CategoriesRepository;
    }

    #[Route('/', name: 'app_page')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $CategoriesRepository = $entityManager->getRepository(Categories::class);
        $Categories = $CategoriesRepository->findAll();

        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
            'Categories' => $Categories,
        ]);
    }
    #[Route('/category/{id}', name: 'category_items')]
    public function categoryItems(int $id): Response
    {
        $category = $this->CategoriesRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie demandée n\'existe pas.');
        }

        return $this->render('product/detail.html.twig', [
            'category' => $category,
        ]);
    }
}
