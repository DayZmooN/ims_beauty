<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class ContactController extends AbstractController
{
    #[Route('/contact/send', name: 'contact_send', methods: "POST")]
    public function sendContactEmail(Request $request, MailerInterface $mailer, ValidatorInterface $validator, CsrfTokenManagerInterface $csrfTokenManager)
    {
        // Étape 1: Vérifiez le token CSRF
        $csrfToken = new CsrfToken('contact_form', $request->request->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_about_us');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $senderEmail = $request->request->get('email');
            $phone = $request->request->get('phone');
            $message = $request->request->get('contact-message');

            // Étape 2: Validez les données
            $errors = $validator->validate([
                'name' => $name,
                'email' => $senderEmail,
                'phone' => $phone,
                'message' => $message,
            ]);

            // Étape 3: Vérifiez s'il y a des erreurs de validation
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->redirectToRoute('app_about_us');
            }

            // Étape 4: Créez et envoyez l'e-mail
            try {
                $email = (new Email())
                    ->from($senderEmail)
                    ->to('gremlins.coders@gmail.com')// Email d'IMS Beauty à ajouter plus tard
                    ->subject('Formulaire Contact')
                    ->html("<p>Name: $name</p><p>Email: $senderEmail</p><p>Phone: $phone</p><p>Message: $message</p>");

                $mailer->send($email);
                $this->addFlash('success', 'Votre message a été envoyé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi du message: ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_about_us');
        }
    }
}