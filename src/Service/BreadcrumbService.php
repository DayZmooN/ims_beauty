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
        // Obtient la requête actuelle à partir du RequestStack
        $request = $this->requestStack->getCurrentRequest();
        // Obtient le nom de la route actuelle
        $route = $request->attributes->get('_route');
        // Obtient les paramètres de la route actuelle
        $routeParams = $request->attributes->get('_route_params');
        
        $breadcrumbs = [];
    
        // Toujours inclure le lien vers la page d'accueil
        $breadcrumbs[] = [
            'label' => 'Accueil',
            'url' => $this->router->generate('app_page'),
        ];
    
        // Ajoute le breadcrumb pour la page actuelle en fonction de la route
        if ($route === 'app_about_us') {
            $breadcrumbs[] = [
                'label' => 'À Propos',
                'url' => '', // Cette page n'a pas de pages antérieures ; donc pas de liens !
            ];
        } elseif ($route === 'app_categories') {
            $breadcrumbs[] = [
                'label' => 'Nos Soins',
                'url' => '', // Cette page n'a pas de pages antérieures ; donc pas de liens !
            ];
        } elseif ($route === 'app_our_prices') {
            $breadcrumbs[] = [
                'label' => 'Nos Tarifs',
                'url' => '', // Cette page n'a pas de pages antérieures ; donc pas de liens !
            ];
        } elseif ($route === 'app_services' && isset($routeParams['categoryId'])) {
            // Obtient le nom de la catégorie en fonction de l'ID de la catégorie
            $categoryName = $this->getCategoryName($routeParams['categoryId']);
            $breadcrumbs[] = [
                'label' => 'Nos Soins',
                'url' => $this->router->generate('app_categories'), // La page service nécessite de passer par la page "Soins" avant, donc on ajoute le lien vers la page "Soins"
            ];
            $breadcrumbs[] = [
                'label' => $categoryName,
                'url' => '', // Cette page n'a pas de pages antérieures ; donc pas de liens !
            ];
        }
        return $breadcrumbs;
    }
    
    // Fonction pour obtenir le nom de la catégorie en fonction de l'ID de la catégorie
    public function getCategoryName($categoryId)
    {
        // Récupère l'entité de catégorie en fonction de l'ID de catégorie fourni
        $category = $this->entityManager->getRepository(Categories::class)->find($categoryId);
        // Retourne le nom de la catégorie s'il est trouvé, sinon retourne une valeur par défaut
        return $category ? $category->getName() : 'Catégorie Inconnue';
    }
}