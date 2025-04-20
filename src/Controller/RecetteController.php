<?php

// src/Controller/RecetteController.php

namespace App\Controller;

use App\Repository\RecetteRepository;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recette;
use App\Form\RecetteType;
use App\Form\DetenirType;
use App\Form\UtiliserType;
use App\Entity\Detenir;
use App\Entity\Utiliser;
use App\Entity\Etape;
use App\Form\EtapeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\String\Slugger\SluggerInterface;
class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_home')]  // Remplacer '/recettes' par '/'
    public function index(RecetteRepository $recetteRepository, NoteRepository $noteRepository): Response
    {
        // On récupère les top recettes triées par note (note moyenne descendante)
        $topRecettes = $recetteRepository->findBy([], ['date_creation' => 'DESC'], 4);

        // On récupère les dernières recettes triées par date de création descendante
        $dernieresRecettes = $recetteRepository->findBy([], ['date_creation' => 'DESC'], 4);

        // Calculer la note moyenne pour chaque recette
        foreach ($topRecettes as $recette) {
            $noteMoyenne = $noteRepository->calculerNoteMoyennePourRecette($recette);

        }

        return $this->render('index.html.twig', [
            'topRecettes' => $topRecettes,
            'dernieresRecettes' => $dernieresRecettes,
        ]);
    }

    #[Route('/ajouter-recette', name: 'ajouter_recette')]
    public function ajouter(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_recette_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion de l’erreur si besoin
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }

                $recette->setImage($newFilename);
            }

            // Informations supplémentaires
            $recette->setDateCreation(new \DateTime());
            $recette->setUtilisateur($this->getUser());

            // Liaison des sous-éléments à la recette
            foreach ($recette->getEtapes() as $etape) {
                $etape->setRecette($recette);
            }

            foreach ($recette->getDetenir() as $detenir) {
                $detenir->setRecette($recette);
            }

            foreach ($recette->getUtiliser() as $utiliser) {
                $utiliser->setRecette($recette);
            }

            // Enregistrement
            $em->persist($recette);
            $em->flush();

            $this->addFlash('success', 'Recette ajoutée avec succès !');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('recette.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/modifier-recette/{id}', name: 'modifier_recette')]
    public function modifier(
        Recette $recette,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        // Vérifie si l'utilisateur est bien propriétaire
        if ($recette->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette recette.');
        }

        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_recette_directory'),
                        $newFilename
                    );
                    $recette->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            // Met à jour les relations
            foreach ($recette->getEtapes() as $etape) {
                $etape->setRecette($recette);
            }
            foreach ($recette->getDetenir() as $detenir) {
                $detenir->setRecette($recette);
            }
            foreach ($recette->getUtiliser() as $utiliser) {
                $utiliser->setRecette($recette);
            }

            $em->flush();

            $this->addFlash('success', 'Recette modifiée avec succès !');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('recette.html.twig', [
            'form' => $form->createView(),
            'modifier' => true
        ]);
    }

}