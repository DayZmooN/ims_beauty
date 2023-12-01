<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\PasswordResetFormType;
use App\Form\PasswordResetRequestFormType;
use App\Form\PasswordResetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class PasswordResetController extends AbstractController
{
    #[Route("/reset-password-request", name: "app_reset_password_request")]
    public function request(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PasswordResetRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data->getEmail();
            $user = $entityManager->getRepository(Users::class)->findOneByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);

                // Calculez la date d'expiration en ajoutant 30 minutes à la date actuelle.
                $expirationTime = new \DateTimeImmutable();
                $expirationTime->add(new \DateInterval('PT30M'));
                $user->setResetTokenExpiration($expirationTime);

                $entityManager->flush();

                $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $email = (new Email())
                    ->from('noreply@example.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html("Pour réinitialiser votre mot de passe, veuillez cliquer sur ce lien : <a href=\"$url\">$url</a>");

                $mailer->send($email);
                $this->addFlash('success', 'Un e-mail de réinitialisation de mot de passe a été envoyé à votre adresse.');
            } else {
                // Gérer le cas où l'e-mail n'est pas trouvé
                $this->addFlash('error', 'Adresse e-mail introuvable.');
            }
        }

        return $this->render('password_reset/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }


    #[Route("/reset-password/{token}", name: "app_reset_password")]
    public function reset(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, string $token): Response
    {
        $user = $entityManager->getRepository(Users::class)->findOneByResetToken($token);

        if (!$user) {
            throw $this->createNotFoundException('Ce token de réinitialisation n\'est pas valide.');
        }

        // Ajout de la validation de l'expiration du token (vous devez implémenter cette vérification)
        if ($user->isPasswordResetTokenExpired()) {
            throw $this->createNotFoundException('Le token de réinitialisation a expiré.');
        }

        $form = $this->createForm(PasswordResetFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setResetToken(null);
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $entityManager->flush();

            // Ajout d'un message flash de confirmation
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');

            // Redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        return $this->render('password_reset/request_password_reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    // #[Route("/send", name: "send")]
    // public function sendEmailTest(MailerInterface $mailer): Response
    // {
    //     $email = (new Email())
    //         ->from('hello@example.com')
    //         ->to('recipient@example.com')
    //         ->subject('Test Mail from Symfony')
    //         ->text('Sending emails is fun again!');

    //     $mailer->send($email);

    //     return new Response('Email sent!');
    // }
}
