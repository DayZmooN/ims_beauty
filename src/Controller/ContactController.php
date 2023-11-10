<?php

namespace App\Controller;
// src/Controller/ContactController.php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\ContactFormType;

class ContactController extends AbstractController
{
    /**
     * @Route("/about_Us", name="about_us") // Update the route name
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            // Send an email
            $email = (new Email())
                ->from($formData['email'])
                ->to('anje.jiro@gmail.com') // Salon's email address
                ->subject('New Form Submission from Website')
                ->html('First Name: ' . $formData['firstname'] . '<br>' .
                       'Email: ' . $formData['email'] . '<br>' .
                       'Number: ' . $formData['number'] . '<br>' .
                       'Message: ' . $formData['contact_message']);

            $mailer->send($email);

            // Redirect or return a response (e.g., JSON) as needed
            return $this->json(['message' => 'Form submitted successfully']);
        }

        // Return a response for validation errors if needed
        return $this->json(['message' => 'Form submission failed'], 400);
    }
}