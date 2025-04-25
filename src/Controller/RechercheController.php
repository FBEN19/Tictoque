<?php

namespace App\Controller;

use App\Repository\RecetteRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function recherche(
        Request $request,
        RecetteRepository $recetteRepository,
        LoggerInterface $logger
    ): Response {
        $terme = $request->query->get('q');
        $minRating = $request->query->get('min_rating');
        $excludeIngredient = $request->query->get('exclude_ingredient');
        $sortOrder = $request->query->get('sort_order');

        $logger->info('Début de la recherche', [
            'terme' => $terme,
            'min_rating' => $minRating,
            'exclude_ingredient' => $excludeIngredient,
            'sort_order' => $sortOrder
        ]);

        $qb = $recetteRepository->createQueryBuilder('r')
            ->leftJoin('r.notes', 'n')
            ->leftJoin('r.detenir', 'd')
            ->leftJoin('d.ingredient', 'i')
            ->addSelect('AVG(n.note) AS moyenne')
            ->addSelect('COUNT(n.id) AS nombreNotes')
            ->groupBy('r.id');

        if ($terme) {
            $logger->debug("Filtrage par titre contenant : $terme");
            $qb->andWhere('r.titre LIKE :terme')
                ->setParameter('terme', '%' . $terme . '%');
        }

        if ($minRating) {
            $logger->debug("Filtrage par note minimale : $minRating");
            $qb->having('AVG(n.note) >= :minRating')
                ->setParameter('minRating', $minRating);
        }

        if ($excludeIngredient) {
            $logger->debug("Exclusion de l'ingrédient : $excludeIngredient");
            $qb->andWhere('i.nom != :excludeIngredient')
                ->setParameter('excludeIngredient', $excludeIngredient);
        }

        if ($sortOrder === 'newest') {
            $logger->debug("Tri par date de création descendante");
            $qb->orderBy('r.date_creation', 'DESC');
        } elseif ($sortOrder === 'oldest') {
            $logger->debug("Tri par date de création ascendante");
            $qb->orderBy('r.date_creation', 'ASC');
        } elseif ($sortOrder === 'rating') {
            $logger->debug("Tri par note moyenne décroissante");
            $qb->orderBy('moyenne', 'DESC')
                ->addOrderBy('nombreNotes', 'DESC');
        }

        $resultatsRaw = $qb->getQuery()->getResult();
        $logger->info('Nombre de résultats bruts récupérés', ['count' => count($resultatsRaw)]);

        $recettesAvecMoyenne = [];
        foreach ($resultatsRaw as $item) {

            $recette = $item[0];
            $recette->noteMoyenne = round($item['moyenne'] ?? 0, 1);
            $recettesAvecMoyenne[] = $recette;
        }

        $logger->info('Résultats formatés avec note moyenne', ['count' => count($recettesAvecMoyenne)]);

        return $this->render('recherche.html.twig', [
            'resultats' => $recettesAvecMoyenne,
            'terme' => $terme,
        ]);
    }
}