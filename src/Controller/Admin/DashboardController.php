<?php

namespace App\Controller\Admin;

use App\Entity\Appointements;
use App\Entity\Categories;
use App\Entity\Notifications;
use App\Entity\Promotions;
use App\Entity\Services;
use App\Entity\Users;
use App\Entity\AboutUs;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Ims Beauty');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Accueil', 'fas fa-home', 'app_page');
        yield MenuItem::linkToCrud('A propos', 'fas fa-address-card', AboutUs::class);
        yield MenuItem::linkToCrud('Reservations', 'fas fa-calendar-day', Appointements::class);
        yield MenuItem::linkToCrud('Services', 'fas fa-cart-shopping', Services::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-folder', Categories::class);
        yield MenuItem::linkToCrud('Notifications', 'fas fa-envelope', Notifications::class);
        yield MenuItem::linkToCrud('Promotions', 'fa-brands fa-shopify', Promotions::class);
        yield MenuItem::linkToCrud('Clients', 'fas fa-users', Users::class);
    }
    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    //avatar user admin
    // public function configureUserMenu(UserInterface $user): UserMenu
    // {
    //     return parent::configureUserMenu($user)
    //         ->setAvatarUrl($user->getAvatarUrl());
    // }

    // configaration asset
    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addCssFile('style.css');
        // ->addJsFile() si on veut rajouter un js
    }
}
