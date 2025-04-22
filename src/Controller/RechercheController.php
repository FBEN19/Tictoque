<?php

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

        $qb = $recetteRepository->createQueryBuilder('r')
            ->leftJoin('r.notes', 'n')
            ->leftJoin('r.detenir', 'd')
            ->leftJoin('d.ingredient', 'i')
            ->groupBy('r.id');

        if ($terme) {
            $qb->andWhere('r.titre LIKE :terme')
                ->setParameter('terme', '%' . $terme . '%');
        }

        if ($minRating) {
            $qb->having('AVG(n.note) >= :minRating')
                ->setParameter('minRating', $minRating);
        }

        if ($excludeIngredient) {
            $qb->andWhere('i.nom != :excludeIngredient')
                ->setParameter('excludeIngredient', $excludeIngredient);
        }

        if ($sortOrder === 'newest') {
            $qb->orderBy('r.date_creation', 'DESC');
        } elseif ($sortOrder === 'oldest') {
            $qb->orderBy('r.date_creation', 'ASC');
        } elseif ($sortOrder === 'rating') {
            $qb->orderBy('AVG(n.note)', 'DESC');
        }

        $resultats = $qb->getQuery()->getResult();

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