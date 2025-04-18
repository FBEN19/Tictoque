<?php

// src/Controller/RechercheController.php

namespace App\Controller;

use App\Repository\RecetteRepository;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function recherche(Request $request, RecetteRepository $recetteRepository, NoteRepository $noteRepository): Response
    {
        $terme = $request->query->get('q');
        $minRating = $request->query->get('min_rating');
        $excludeIngredient = $request->query->get('exclude_ingredient');
        $sortOrder = $request->query->get('sort_order');

        $qb = $recetteRepository->createQueryBuilder('r');

        if ($terme) {
            $qb->andWhere('r.nom LIKE :terme')
                ->setParameter('terme', '%' . $terme . '%');
        }

        if ($minRating) {
            $qb->andWhere('r.note >= :minRating')
                ->setParameter('minRating', $minRating);
        }

        if ($excludeIngredient) {
            $qb->andWhere('r.ingredients NOT LIKE :exclude')
                ->setParameter('exclude', '%' . $excludeIngredient . '%');
        }

        if ($sortOrder === 'newest') {
            $qb->orderBy('r.dateCreation', 'DESC');
        } elseif ($sortOrder === 'oldest') {
            $qb->orderBy('r.dateCreation', 'ASC');
        } elseif ($sortOrder === 'rating') {
            $qb->orderBy('r.note', 'DESC');
        }

        $resultats = $qb->getQuery()->getResult();

        // 🔥 Ajouter la note moyenne pour chaque recette
        $recettesAvecMoyenne = [];
        foreach ($resultats as $recette) {
            $moyenne = $noteRepository->calculerNoteMoyennePourRecette($recette);
            $recettesAvecMoyenne[] = [
                'recette' => $recette,
                'moyenne' => round($moyenne, 1)
            ];
        }

        return $this->render('recherche.html.twig', [
            'resultats' => $recettesAvecMoyenne,
            'terme' => $terme,
        ]);
    }
}