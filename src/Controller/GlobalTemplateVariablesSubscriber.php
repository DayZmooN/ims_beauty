<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\AboutUsRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class GlobalTemplateVariablesSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $categoriesRepository;
    private $aboutUsRepository;

    public function __construct(Environment $twig, CategoriesRepository $categoriesRepository, AboutUsRepository $aboutUsRepository)
    {
        $this->twig = $twig;
        $this->aboutUsRepository = $aboutUsRepository;
        $this->categoriesRepository = $categoriesRepository;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // Ajoutez vos variables globales ici
        $categories = $this->categoriesRepository->findAll();
        $aboutUs = $this->aboutUsRepository->findOneBy([]);
        $this->twig->addGlobal('categories', $categories);
        $this->twig->addGlobal('aboutUs', $aboutUs);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
