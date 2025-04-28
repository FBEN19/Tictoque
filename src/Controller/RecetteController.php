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
use App\Service\AnecdoteService;
use Psr\Log\LoggerInterface;

class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        RecetteRepository $recetteRepository,
        NoteRepository $noteRepository,
        AnecdoteService $anecdoteService,
        LoggerInterface $logger
    ): Response {

        $anecdote = $anecdoteService->getAnecdote();
        $topRecettesRaw = $recetteRepository->findTopRecettesAvecNotes(4);
        $topRecettes = [];

        foreach ($topRecettesRaw as $item) {
            $recette = $item[0];
            $recette->noteMoyenne = round($item['moyenne'] ?? 0, 1);
            $topRecettes[] = $recette;
        }


        $dernieresRecettes = $recetteRepository->findBy([], ['date_creation' => 'DESC'], 4);
        foreach ($dernieresRecettes as $recette) {
            $recette->noteMoyenne = round($noteRepository->calculerNoteMoyennePourRecette($recette), 1);
        }


        return $this->render('index.html.twig', [
            'topRecettes' => $topRecettes,
            'dernieresRecettes' => $dernieresRecettes,
            'anecdote' => $anecdote,
        ]);
    }

    #[Route('/ajouter-recette', name: 'ajouter_recette')]
    public function ajouter(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, LoggerInterface $logger): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_recette_directory'),
                        $newFilename
                    );
                    $recette->setImage($newFilename);
                    $logger->info('Image uploadée avec succès : ' . $newFilename);
                } catch (FileException $e) {
                    $logger->error('Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                }
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

            $ustensiles = [];
            foreach ($recette->getUtiliser() as $utiliser) {
                $idUstensile = $utiliser->getUstensile()->getId();
                if (isset($ustensiles[$idUstensile])) {
                    $this->addFlash('error', 'Vous avez ajouté deux fois le même ustensile.');
                    return $this->redirectToRoute('ajouter_recette');
                }
                $ustensiles[$idUstensile] = true;
            }

            $em->persist($recette);
            $em->flush();

            $logger->info('Nouvelle recette enregistrée en base. Id :' . $recette->getId());

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
        SluggerInterface $slugger,
        LoggerInterface $logger
    ): Response {
        if ($recette->getUtilisateur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $logger->warning('Tentative d\'accès non autorisée à une recette.');
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette recette.');
        }

        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_recette_directory'),
                        $newFilename
                    );
                    $recette->setImage($newFilename);
                    $logger->info('Nouvelle image uploadée : ' . $newFilename);
                } catch (FileException $e) {
                    $logger->error('Erreur lors du changement d\'image : ' . $e->getMessage());
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

            $ustensiles = [];
            foreach ($recette->getUtiliser() as $utiliser) {
                $idUstensile = $utiliser->getUstensile()->getId();
                if (isset($ustensiles[$idUstensile])) {
                    $this->addFlash('error', 'Vous avez ajouté deux fois le même ustensile.');
                    return $this->redirectToRoute('modifier_recette', ['id' => $recette->getId()]);
                }
                $ustensiles[$idUstensile] = true;
            }

            $em->flush();

            $logger->info('Recette mise à jour avec succès. ID : ' . $recette->getId());

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
        NoteRepository $noteRepository,
        LoggerInterface $logger
    ): Response {

        $noteExistante = $noteRepository->findOneBy([
            'utilisateur' => $this->getUser(),
            'recette' => $recette
        ]);

        

        $formNote = null;
        $formCommentaire = null;
        $errorMessage = null;

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

                $logger->info('Nouvelle note enregistrée pour la recette ID : '.$recette->getId());

                return $this->redirectToRoute('app_afficher_recette', ['id' => $recette->getId()]);
            }

            $commentaire = new Commentaire();
            $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);
            $formCommentaire->handleRequest($request);

            if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
                try {
                    $commentaire->setUtilisateur($this->getUser());
                    $commentaire->setRecette($recette);
                    $commentaire->setDateCommentaire(new \DateTimeImmutable());
                    $entityManager->persist($commentaire);
                    $entityManager->flush();

                    $logger->info('Nouveau commentaire ajouté à la recette ID : '.$recette->getId());

                    return $this->redirectToRoute('app_afficher_recette', ['id' => $recette->getId()]);
                } catch (\Doctrine\DBAL\Exception\DriverException $e) {
                    if (strpos($e->getMessage(), 'Doublon de commentaire trouvé') !== false) {
                        $errorMessage = 'Un doublon de commentaire a été détecté pour cette recette.';
                    } else {
                        $errorMessage = 'Une erreur est survenue lors de l\'ajout de votre commentaire.';
                    }
                }
            }
        }

        $moyenne = $noteRepository->calculerNoteMoyennePourRecette($recette);

        return $this->render('info-recette.html.twig', [
            'recette' => $recette,
            'formNote' => $formNote ? $formNote->createView() : null,
            'formCommentaire' => $formCommentaire ? $formCommentaire->createView() : null,
            'moyenne' => $moyenne,
            'note_deja_donnee' => $noteExistante !== null,
            'errorMessage' => $errorMessage,
        ]);
    }
}