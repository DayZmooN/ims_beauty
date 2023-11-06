<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class GlobalTemplateVariablesSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $categoriesRepository;

    public function __construct(Environment $twig, CategoriesRepository $categoriesRepository)
    {
        $this->twig = $twig;
        $this->categoriesRepository = $categoriesRepository;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // Ajoutez vos variables globales ici
        $categories = $this->categoriesRepository->findAll();
        $this->twig->addGlobal('categories', $categories);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
