<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        $utilisateur = $this->getUser(); // Récupère l'utilisateur connecté
        $recettes = $utilisateur->getRecettes(); // Récupère ses recettes

        return $this->render('profil.html.twig', [
            'utilisateur' => $utilisateur,
            'recettes' => $recettes, // Passe les recettes au template
        ]);
    }

    #[Route('/modifier-profil', name: 'modifier_profil')]
    public function modifierProfil(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = $this->getUser(); // Utilisateur connecté
        
        // Récupérer les données du formulaire
        $nom = $request->request->get('nom');
        $email = $request->request->get('email');

        // Vérification que les données sont valides
        if (empty($nom)) {
            // Si le nom est vide, tu peux afficher un message d'erreur
            $this->addFlash('error', 'Le nom d\'utilisateur ne peut pas être vide.');
            return $this->redirectToRoute('app_profil');
        }

        if (empty($email)) {
            // Vérification de l'email
            $this->addFlash('error', 'L\'email ne peut pas être vide.');
            return $this->redirectToRoute('app_profil');
        }

        // Mettre à jour les informations de l'utilisateur
        $utilisateur->setNom($nom);
        $utilisateur->setEmail($email);
        
        // Sauvegarder les changements
        $em->persist($utilisateur);
        $em->flush();
        
        // Retourner une réponse ou rediriger l'utilisateur
        $this->addFlash('success', 'Profil mis à jour avec succès.');
        return $this->redirectToRoute('app_profil'); // Redirection vers la page de profil après mise à jour
    }
}