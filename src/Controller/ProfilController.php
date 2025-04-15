<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

    #[Route('/changer-photo', name: 'upload_photo_profil', methods: ['POST'])]
    public function uploadPhotoProfil(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = $this->getUser();
    
        $photo = $request->files->get('photo');
        if ($photo instanceof UploadedFile) {
            // Supprimer l'ancienne photo si elle existe
            $anciennePhoto = $utilisateur->getPhotoProfil();
            if ($anciennePhoto) {
                $cheminFichier = $this->getParameter('photo_profil_directory') . '/' . $anciennePhoto;
                if (file_exists($cheminFichier)) {
                    unlink($cheminFichier); // Supprime le fichier
                }
            }
    
            // Traitement de la nouvelle photo
            $extension = $photo->guessExtension();
            $uniqueName = uniqid() . '.' . $extension;
            $photo->move($this->getParameter('photo_profil_directory'), $uniqueName);
    
            $utilisateur->setPhotoProfil($uniqueName);
            $em->persist($utilisateur);
            $em->flush();
        }
    
        return $this->redirectToRoute('app_profil');
    }
}