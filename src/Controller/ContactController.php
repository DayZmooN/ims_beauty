<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact/send', name: 'contact_send', methods: "POST")]
    public function sendContactEmail(Request $request, MailerInterface $mailer)
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $senderEmail = $request->request->get('email');
            $phone = $request->request->get('phone');
            $message = $request->request->get('contact-message');            
            // Check if senderEmail is not null

            if ($senderEmail === null) {
                $this->addFlash('error', 'L\'adresse e-mail est obligatoire.');
                return $this->redirectToRoute('app_about_us');
            }

            try {
                // Create and send the email
                $email = (new Email())
                ->from($senderEmail)
                ->to('gremlins.coders@gmail.com') // Replace with IMS Beauty's email when live
                ->subject('Contact Form Submission')
                ->html("<p>Name: $name</p><p>Email: $senderEmail</p><p>Phone: $phone</p><p>Message: $message</p>");

                $mailer->send($email);

                // Add a flash message for success
                $this->addFlash('success', 'Votre message a été envoyé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi du message: ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_about_us');
        }
    }
}
