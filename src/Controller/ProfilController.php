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
            $utilisateur->getRecettes(),
            $request->query->getInt('page', 1),
            3 
        );

        return $this->render('profil.html.twig', [
            'utilisateur' => $utilisateur,
            'pagination' => $pagination
        ]);
    }

    #[Route('/modifier-profil', name: 'modifier_profil')]
    public function modifierProfil(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = $this->getUser();
        
        $nom = $request->request->get('nom');
        $email = $request->request->get('email');

        if (empty($nom)) {
            return $this->redirectToRoute('app_profil');
        }

        if (empty($email)) {
            return $this->redirectToRoute('app_profil');
        }

        $utilisateur->setNom($nom);
        $utilisateur->setEmail($email);
        
        $em->persist($utilisateur);
        $em->flush();
        
        return $this->redirectToRoute('app_profil');
    }

    #[Route('/changer-photo', name: 'upload_photo_profil', methods: ['POST'])]
    public function uploadPhotoProfil(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = $this->getUser();
    
        $photo = $request->files->get('photo');
        if ($photo instanceof UploadedFile) {
            $anciennePhoto = $utilisateur->getPhotoProfil();
            if ($anciennePhoto) {
                $cheminFichier = $this->getParameter('photo_profil_directory') . '/' . $anciennePhoto;
                if (file_exists($cheminFichier)) {
                    unlink($cheminFichier);
                }
            }
    
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
        $nomImage = $recette->getImage();
        $cheminImage = $this->getParameter('kernel.project_dir') . '/public/images/recettes/' . $nomImage;

        $filesystem = new Filesystem();
        if ($filesystem->exists($cheminImage)) {
            $filesystem->remove($cheminImage);
        }

        foreach ($recette->getEtapes() as $etape) {
            $em->remove($etape);
        }

        foreach ($recette->getUstensiles() as $utilisation) {
            $em->remove($utilisation);
        }

        foreach ($recette->getDetenir() as $detenir) {
            $em->remove($detenir);
        }

        foreach ($recette->getCommentaires() as $commentaire) {
            $em->remove($commentaire);
        }

        foreach ($recette->getNotes() as $note) {
            $em->remove($note);
        }

        $em->remove($recette);
        $em->flush();

        return $this->redirectToRoute('app_profil');
    }
}