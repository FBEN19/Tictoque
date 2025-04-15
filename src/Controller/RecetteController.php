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
            $recette->setNoteMoyenne($noteMoyenne); // Affecte la note moyenne (sans toucher à Recette.php, si tu veux la conserver dans une variable)
        }

        return $this->render('index.html.twig', [
            'topRecettes' => $topRecettes,
            'dernieresRecettes' => $dernieresRecettes,
        ]);
    }

    #[Route('/ajouter-recette', name: 'ajouter_recette')]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $recette = new Recette();

        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        dd($form);

        if ($form->isSubmitted() && $form->isValid()) {
            // Remplissage automatique
            $recette->setDateCreation(new \DateTime());

            // Utilisateur connecté
            $utilisateur = $this->getUser();
            if ($utilisateur) {
                $recette->setUtilisateur($utilisateur);
            }

            $em->persist($recette);
            $em->flush();

            return $this->redirectToRoute('ajouter_etape', ['id' => $recette->getId()]);
        }

        return $this->render('ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}