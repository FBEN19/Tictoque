<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route("/connexion", name: "app_login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Vérifie si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home'); // Redirection vers une page accessible après la connexion
        }

        // Récupère l'erreur de connexion si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();
        // Récupère le dernier email entré
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('connexion.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route("/deconnexion", name: "app_logout")]
    public function logout()
    {
        // Symfony gère automatiquement la déconnexion
    }
}