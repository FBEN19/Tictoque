<?php


// src/Controller/RechercheController.php

namespace App\Controller;

use App\Repository\RecetteRepository; // Assurez-vous que RecetteRepository est créé et bien configuré
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function recherche()
    {
        // Passer les résultats à la vue
        return $this->render('recherche.html.twig');
    }
}