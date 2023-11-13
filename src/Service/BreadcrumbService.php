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
        
        $breadcrumbs = [];
    
        // Always include the homepage link
        $breadcrumbs['Accueil'] = $this->router->generate('app_page');
    
        // Add breadcrumb for the current page
        if ($route === 'app_about_us') {
            $breadcrumbs['À Propos'] = null; // No link for the current page
        } elseif ($route === 'app_categories') {
            $breadcrumbs['Nos Soins'] = null; // No link for the current page
        } elseif ($route === 'app_our_prices') {
            $breadcrumbs['Nos Tarifs'] = null; // No link for the current page
        } elseif ($route === 'app_services' && isset($routeParams['categoryId'])) {
            $categoryName = $this->getCategoryName($routeParams['categoryId']);
            $breadcrumbs['Nos Soins'] = $this->router->generate('app_categories'); // Link to categories/soins page
            $breadcrumbs[$categoryName] = null; // No link for the current category
        }
    
        return $breadcrumbs;
    }
    
    
    public function getCategoryName($categoryId)
    {
        // Fetch the category entity based on the provided categoryId
        $category = $this->entityManager->getRepository(Categories::class)->find($categoryId);

        // Return the name of the category if found, otherwise return a default value
        return $category ? $category->getName() : 'Unknown Category';
    }
}
