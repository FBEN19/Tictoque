<?php


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
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Commentaire;
use App\Entity\Note;
use App\Form\CommentaireType;
use App\Form\NoteType;
class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RecetteRepository $recetteRepository, NoteRepository $noteRepository): Response
    {
        $topRecettes = $recetteRepository->findBy([], ['date_creation' => 'DESC'], 4);

        $dernieresRecettes = $recetteRepository->findBy([], ['date_creation' => 'DESC'], 4);

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
                }

                $recette->setImage($newFilename);
            }

            $recette->setDateCreation(new \DateTime());
            $recette->setUtilisateur($this->getUser());

            foreach ($recette->getEtapes() as $etape) {
                $etape->setRecette($recette);
            }

            foreach ($recette->getDetenir() as $detenir) {
                $detenir->setRecette($recette);
            }

            foreach ($recette->getUtiliser() as $utiliser) {
                $utiliser->setRecette($recette);
            }

            $em->persist($recette);
            $em->flush();

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
                }
            }

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

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('recette.html.twig', [
            'form' => $form->createView(),
            'modifier' => true
        ]);
    }


    #[Route('/recette/{id}', name: 'app_afficher_recette')]
    public function afficher(
        Recette $recette,
        Request $request,
        EntityManagerInterface $entityManager,
        NoteRepository $noteRepository
    ): Response {
        $noteExistante = $noteRepository->findOneBy([
            'utilisateur' => $this->getUser(),
            'recette' => $recette
        ]);

        $formNote = null;
        $formCommentaire = null;

        if ($this->getUser()) {
            $note = new Note();
            $formNote = $this->createForm(NoteType::class, $note);
            $formNote->handleRequest($request);

            if (!$noteExistante && $formNote->isSubmitted() && $formNote->isValid()) {
                $note->setUtilisateur($this->getUser());
                $note->setRecette($recette);
                $note->setDateNote(new \DateTimeImmutable());
                $entityManager->persist($note);
                $entityManager->flush();

                return $this->redirectToRoute('app_afficher_recette', ['id' => $recette->getId()]);
            }

            $commentaire = new Commentaire();
            $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);
            $formCommentaire->handleRequest($request);

            if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
                $commentaire->setUtilisateur($this->getUser());
                $commentaire->setRecette($recette);
                $commentaire->setDateCommentaire(new \DateTimeImmutable());
                $entityManager->persist($commentaire);
                $entityManager->flush();

                return $this->redirectToRoute('app_afficher_recette', ['id' => $recette->getId()]);
            }
        }
        $moyenne = $noteRepository->calculerNoteMoyennePourRecette($recette);

        return $this->render('info-recette.html.twig', [
            'recette' => $recette,
            'formNote' => $formNote ? $formNote->createView() : null,
            'formCommentaire' => $formCommentaire ? $formCommentaire->createView() : null,
            'moyenne' => $moyenne,
            'note_deja_donnee' => $noteExistante !== null
        ]);
    }
}