<?php

namespace App\Controller;

use App\Form\AvailabilityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServicesRepository;


class CartController extends AbstractController
{

    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, ServicesRepository $serviceRepository): Response
    {
        // Récupérez les services du panier
        $cart = $session->get('cart', []);
        $services = [];

        foreach ($cart as $id) {
            $service = $serviceRepository->find($id);
            if ($service) {
                $services[] = $service;
            }
        }

        // Pas besoin de calculer un total ici puisque ce sont des rendez-vous.

        return $this->render('cart/index.html.twig', [
            'services' => $services,
        ]);
    }




    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get('cart', []);

        if (!in_array($id, $cart)) {
            $cart[] = $id; // Ajoutez le service s'il n'est pas déjà dans le panier.
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Service ajouté au panier avec succès!');

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/checkout', name: 'cart_checkout')]
    public function checkout(Request $request, SessionInterface $session, ServicesRepository $servicesRepository): Response
    {
        // Créez et gérez le formulaire de disponibilité
        $form = $this->createForm(AvailabilityType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ici, vous traiterez la prise de rendez-vous après soumission du formulaire
            // Par exemple, enregistrez les données en base de données et videz le panier
        }

        // Récupérez les services du panier pour les afficher sur la page de paiement
        $cart = $session->get('cart', []);
        $servicesWithDetails = [];
        foreach ($cart as $id) {
            $service = $servicesRepository->find($id);
            if ($service) {
                $servicesWithDetails[] = $service; // On suppose qu'il s'agit d'un tableau d'objets Service
            }
        }

        // Retournez la vue avec le panier et le formulaire
        return $this->render('cart/index.html.twig', [
            'services' => $servicesWithDetails,
            'availabilityForm' => $form->createView(),
        ]);
    }


    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, SessionInterface $session): Response
    {
        // Récupérez le panier de la session
        $cart = $session->get('cart', []);

        // Trouvez l'index de l'élément dans le tableau
        if (($key = array_search($id, $cart)) !== false) {
            // Si l'article est dans le panier, retirez-le
            unset($cart[$key]);
        }

        // Ré-indexez le tableau après la suppression
        $cart = array_values($cart);

        // Enregistrez le panier mis à jour dans la session
        $session->set('cart', $cart);

        // Ajoutez un message flash pour confirmer la suppression
        $this->addFlash('success', 'Service retiré du panier avec succès.');

        // Redirigez l'utilisateur vers la vue du panier
        return $this->redirectToRoute('app_cart');
    }
}
