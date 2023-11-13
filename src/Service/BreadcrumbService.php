<?php 

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categories;

class BreadcrumbService
{
    private $router;
    private $requestStack;
    private $entityManager;

    public function __construct(RouterInterface $router, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function getBreadcrumbs()
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params');
    
        $breadcrumbs = [
            'accueil' => 'Accueil',
            'app_about_us' => 'Accueil / À Propos',
            'app_categories' => 'Accueil / Nos Soins',
            'app_our_prices' => 'Accueil / Nos Tarifs',
            // ... add other routes here
        ];
    
        if (isset($breadcrumbs[$route])) {
            return $breadcrumbs[$route];
        }
    
        // Adjusted to match the route structure you provided
        if ($route === 'app_services' && isset($routeParams['categoryId'])) {
            $categoryName = $this->getCategoryName($routeParams['categoryId']);
            return 'Accueil / Nos Soins / ' . $categoryName;
        }
    
        return 'Accueil';
    }
    
    public function getCategoryName($categoryId)
    {
        // Fetch the category entity based on the provided categoryId
        $category = $this->entityManager->getRepository(Categories::class)->find($categoryId);

        // Return the name of the category if found, otherwise return a default value
        return $category ? $category->getName() : 'Unknown Category';
    }
}
