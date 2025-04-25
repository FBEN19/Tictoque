<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Recette;
use App\Form\InscriptionType;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/gerer-utilisateurs', name: 'app_admin_utilisateurs')]
    public function gererUtilisateurs(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $query = $em->getRepository(Utilisateur::class)->createQueryBuilder('u')
            ->orderBy('u.nom', 'ASC');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/gerer-utilisateurs.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/admin/utilisateur/supprimer/{id}', name: 'app_admin_utilisateur_supprimer', methods: ['POST'])]
    public function supprimerUtilisateur(Utilisateur $utilisateur, EntityManagerInterface $em, Request $request, LoggerInterface $logger): Response
    {
        if ($this->isCsrfTokenValid('supprimer' . $utilisateur->getId(), $request->request->get('_token'))) {
            $logger->info("Suppression de l'utilisateur ID {$utilisateur->getId()} ({$utilisateur->getEmail()})");
            $em->remove($utilisateur);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_utilisateurs');
    }

    #[Route('/admin/utilisateur/modifier/{id}', name: 'app_admin_utilisateur_modifier')]
    public function modifierUtilisateur(Utilisateur $utilisateur, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_admin_utilisateurs');
        }
    
        return $this->render('admin/modifier-utilisateur.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/recherche-utilisateur', name: 'app_admin_recherche_utilisateur')]
    public function rechercheUtilisateur(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): Response {
        $motCle = $request->query->get('q', '');
        $page = $request->query->getInt('page', 1);
    
        $query = $em->getRepository(Utilisateur::class)->createQueryBuilder('u')
            ->where('u.nom LIKE :motCle OR u.email LIKE :motCle')
            ->setParameter('motCle', '%' . $motCle . '%')
            ->orderBy('u.date_inscription', 'DESC');
    
        $pagination = $paginator->paginate(
            $query,
            $page,
            10
        );
    
        return $this->render('admin/_liste_utilisateurs.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/admin/gerer-recettes', name: 'app_admin_recettes')]
    public function gererRecettes(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $query = $em->getRepository(Recette::class)->createQueryBuilder('r')
            ->orderBy('r.date_creation', 'DESC');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/gerer-recettes.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/admin/recette/supprimer/{id}', name: 'app_supprimer_recette', methods: ['POST'])]
    public function supprimer(Recette $recette, EntityManagerInterface $em, Request $request, LoggerInterface $logger): Response
    {
        if ($this->isCsrfTokenValid('supprimer' . $recette->getId(), $request->request->get('_token'))) {
            $logger->info("Suppression de la recette ID {$recette->getId()} ({$recette->getTitre()})");
            $em->remove($recette);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_recettes');
    }

    #[Route('/admin/recherche-recette', name: 'app_admin_recherche_recette')]
    public function rechercheRecette(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): Response {
        $motCle = $request->query->get('q', '');
        $page = $request->query->getInt('page', 1);

        $query = $em->getRepository(Recette::class)->createQueryBuilder('r')
            ->join('r.utilisateur', 'u')
            ->where('r.titre LIKE :motCle OR r.description LIKE :motCle')
            ->setParameter('motCle', '%' . $motCle . '%')
            ->orderBy('r.date_creation', 'DESC');

        $pagination = $paginator->paginate(
            $query,
            $page,
            10
        );

        return $this->render('admin/_liste_recettes.html.twig', [
            'pagination' => $pagination
        ]);
    }
}