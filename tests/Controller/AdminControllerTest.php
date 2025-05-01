<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Utilisateur;
use App\Entity\Recette;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use App\Repository\RecetteRepository;

class AdminControllerTest extends WebTestCase
{
    private function getClientAvecAdmin(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $utilisateurRepo = static::getContainer()->get(UtilisateurRepository::class);
        $admin = $utilisateurRepo->findOneByEmail('admin@admin.fr'); // adapte cet email
        $client->loginUser($admin);

        // Démarre explicitement la session
        $client->getContainer()->get('session')->start();

        // Requête pour démarrer la session
        $client->request('GET', '/admin/gerer-utilisateurs');

        return $client;
    }

    public function testAccesPageGestionUtilisateurs(): void
    {
        $client = $this->getClientAvecAdmin();
        $client->request('GET', '/admin/gerer-utilisateurs');

        $this->assertResponseIsSuccessful();
    }

    public function testRechercheUtilisateurFonctionne(): void
    {
        $client = $this->getClientAvecAdmin();
        $client->request('GET', '/admin/recherche-utilisateur?q=test');

        $this->assertResponseIsSuccessful();
    }

    public function testSuppressionUtilisateur(): void
    {
        $client = $this->getClientAvecAdmin();

        // Vérifiez que la session est démarrée
        $this->assertTrue($client->getContainer()->has('session'));

        // Obtenez l'EntityManager
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $utilisateurRepo = $entityManager->getRepository(Utilisateur::class);
        $utilisateur = $utilisateurRepo->findOneBy([]);

        $csrfToken = static::getContainer()->get('security.csrf.token_manager')
            ->getToken('supprimer' . $utilisateur->getId());

        // Assurez-vous que la requête de suppression inclut le CSRF token
        $client->request('POST', '/admin/utilisateur/supprimer/' . $utilisateur->getId(), [
            '_token' => $csrfToken
        ]);

        $this->assertResponseRedirects('/admin/gerer-utilisateurs');
    }

    public function testAccesPageGestionRecettes(): void
    {
        $client = $this->getClientAvecAdmin();
        $client->request('GET', '/admin/gerer-recettes');

        $this->assertResponseIsSuccessful();
    }

    public function testRechercheRecetteFonctionne(): void
    {
        $client = $this->getClientAvecAdmin();
        $client->request('GET', '/admin/recherche-recette?q=gâteau');

        $this->assertResponseIsSuccessful();
    }

    public function testSuppressionRecette(): void
    {
        $client = $this->getClientAvecAdmin();
        
        // Obtenez l'EntityManager
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $recetteRepo = $entityManager->getRepository(Recette::class);
        $recette = $recetteRepo->findOneBy([]);

        $token = static::getContainer()->get('security.csrf.token_manager')->getToken('supprimer' . $recette->getId());

        $client->request('POST', '/admin/recette/supprimer/' . $recette->getId(), [
            '_token' => $token,
        ]);

        $this->assertResponseRedirects('/admin/gerer-recettes');
    }
}