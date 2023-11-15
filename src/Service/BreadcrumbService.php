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
        $breadcrumbs[] = [
            'label' => 'Accueil',
            'url' => $this->router->generate('app_page'),
        ];
    
        // Add breadcrumb for the current page
        if ($route === 'app_about_us') {
            $breadcrumbs[] = [
                'label' => 'À Propos',
                'url' => '', // Add the URL for this page
            ];
        } elseif ($route === 'app_categories') {
            $breadcrumbs[] = [
                'label' => 'Nos Soins',
                'url' => '', // Add the URL for this page
            ];
        } elseif ($route === 'app_our_prices') {
            $breadcrumbs[] = [
                'label' => 'Nos Tarifs',
                'url' => '', // Add the URL for this page
            ];
        } elseif ($route === 'app_services' && isset($routeParams['categoryId'])) {
            $categoryName = $this->getCategoryName($routeParams['categoryId']);
            $breadcrumbs[] = [
                'label' => 'Nos Soins',
                'url' => $this->router->generate('app_categories'), // URL for the "Nos Soins" page
            ];
            $breadcrumbs[] = [
                'label' => $categoryName,
                'url' => '', // Add the URL for this page
            ];
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
