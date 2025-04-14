<?php

// src/Controller/RecetteController.php

namespace App\Controller;

use App\Repository\RecetteRepository;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_recettes')]  // Remplacer '/recettes' par '/'
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
}