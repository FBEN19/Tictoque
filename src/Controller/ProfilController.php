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
use App\Entity\Recette;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Knp\Component\Pager\PaginatorInterface;



class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function profil(PaginatorInterface $paginator, Request $request): Response
    {
        $utilisateur = $this->getUser();

        $pagination = $paginator->paginate(
            $utilisateur->getRecettes(), // ou une requête DQL si tu veux
            $request->query->getInt('page', 1),
            3 // Par page
        );

        return $this->render('profil.html.twig', [
            'utilisateur' => $utilisateur,
            'pagination' => $pagination
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

    #[Route('/supprimer-recette/{id}', name: 'supprimer_recette')]
    public function supprimerRecette(Recette $recette, EntityManagerInterface $em): Response
    {
        // Récupération du nom de fichier
        $nomImage = $recette->getImage(); // ou le nom de ton getter
        $cheminImage = $this->getParameter('kernel.project_dir') . '/public/images/recettes/' . $nomImage;

        $filesystem = new Filesystem();
        if ($filesystem->exists($cheminImage)) {
            $filesystem->remove($cheminImage);
        }

        // Supprimer les étapes
        foreach ($recette->getEtapes() as $etape) {
            $em->remove($etape);
        }

        // Supprimer les liaisons ustensiles
        foreach ($recette->getUstensiles() as $utilisation) {
            $em->remove($utilisation);
        }

        // Supprimer les liaisons ingrédients
        foreach ($recette->getDetenir() as $detenir) {
            $em->remove($detenir);
        }

        // Supprimer les commentaires
        foreach ($recette->getCommentaires() as $commentaire) {
            $em->remove($commentaire);
        }

        // Supprimer les notes
        foreach ($recette->getNotes() as $note) {
            $em->remove($note);
        }

        // Supprimer la recette
        $em->remove($recette);
        $em->flush();

        $this->addFlash('success', 'Recette supprimée avec succès.');
        return $this->redirectToRoute('app_profil');
    }
}