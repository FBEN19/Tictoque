<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UtilisateurRepository;
use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    #[Route("/connexion", name: "app_login")]
    public function login(AuthenticationUtils $authenticationUtils, LoggerInterface $logger): Response
    {
        if ($this->getUser()) {
            $logger->info("Connexion réussie de l'utilisateur : " . $this->getUser()->getUserIdentifier());
            return $this->redirectToRoute('app_home');
        }
    
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        if ($error) {
            $logger->warning("Échec de connexion pour : $lastUsername");
            $this->addFlash('error', 'Adresse e-mail ou mot de passe incorrect.');
        } else {
            $logger->info("Page de connexion affichée. Dernier utilisateur saisi : $lastUsername");
        }
    
        return $this->render('connexion.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    

    #[Route("/deconnexion", name: "app_logout")]
    public function logout()
    {
        // Symfony s'occupe de la déconnexion
    }

    #[Route('/mot-de-passe-oublie', name: 'mot_de_passe_oublie')]
    public function motDePasseOublie(Request $request, UtilisateurRepository $repo, MailerInterface $mailer, EntityManagerInterface $em, LoggerInterface $logger): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $logger->info("Demande de mot de passe oublié pour : $email");

            $utilisateur = $repo->findOneBy(['email' => $email]);

            if ($utilisateur) {
                $jeton = Uuid::v4()->toRfc4122();
                $utilisateur->setJetonReinitialisation($jeton);
                $em->flush();

                $lien = $this->generateUrl('reinitialiser_mot_de_passe', ['token' => $jeton], UrlGeneratorInterface::ABSOLUTE_URL);

                $emailMessage = (new Email())
                    ->from('no-reply@tictoque.bmxbox.fr')
                    ->to($email)
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html("<p>Bonjour,</p><p>Cliquez sur ce lien pour réinitialiser votre mot de passe :</p><p><a href='$lien'>Réinitialiser</a></p>");

                $mailer->send($emailMessage);
                $logger->info("Email de réinitialisation envoyé à : $email");

                $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
            } else {
                $logger->warning("Adresse email inconnue pour réinitialisation : $email");
                $this->addFlash('danger', 'Adresse email inconnue.');
            }
        }

        return $this->render('mot_de_passe_oublie.html.twig');
    }

    #[Route('/reinitialiser-mot-de-passe/{token}', name: 'reinitialiser_mot_de_passe')]
    public function reinitialiserMotDePasse(Request $request, string $token, UtilisateurRepository $repo, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, LoggerInterface $logger): Response
    {
        $logger->info("Tentative de réinitialisation de mot de passe avec le token : $token");
        $utilisateur = $repo->findOneBy(['jetonReinitialisation' => $token]);

        if (!$utilisateur) {
            $logger->warning("Échec : jeton de réinitialisation invalide ou expiré : $token");
            $this->addFlash('danger', 'Lien invalide ou expiré.');
            return $this->redirectToRoute('mot_de_passe_oublie');
        }

        if ($request->isMethod('POST')) {
            $motDePasse = $request->request->get('mot_de_passe');
            $utilisateur->setMdp($hasher->hashPassword($utilisateur, $motDePasse));
            $utilisateur->setJetonReinitialisation(null);
            $em->flush();

            $logger->info("Mot de passe réinitialisé avec succès pour l'utilisateur : " . $utilisateur->getEmail());

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reinitialiser_mot_de_passe.html.twig');
    }
}