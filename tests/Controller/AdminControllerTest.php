<?php

namespace App\Tests\Controller;

use App\Entity\Recette;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Psr\Log\NullLogger;
use App\Controller\AdminController;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdminControllerTest extends WebTestCase
{
    private function createAdminUtilisateur(): Utilisateur
    {
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $utilisateur = new Utilisateur();
        $utilisateur->setNom('AdminTest');
        $utilisateur->setEmail('admin_' . uniqid() . '@test.com');
        $utilisateur->setRole('ROLE_ADMIN');
        $utilisateur->setDateInscription(new \DateTime());
        $utilisateur->setMdp($hasher->hashPassword($utilisateur, 'password123'));

        $em->persist($utilisateur);
        $em->flush();

        return $utilisateur;
    }

    public function testGererUtilisateursPageAccessibleAvecAdmin(): void
    {
        $client = static::createClient();
        $admin = $this->createAdminUtilisateur();
        $client->loginUser($admin);

        $client->request('GET', '/admin/gerer-utilisateurs');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#contenu-utilisateurs');
    }

    public function testGererRecettesPageAccessible(): void
    {
        $client = static::createClient();
        $admin = $this->createAdminUtilisateur();
        $client->loginUser($admin);

        $client->request('GET', '/admin/gerer-recettes');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#recipe-list');
    }

    public function testRechercheUtilisateurFonctionne(): void
    {
        $client = static::createClient();
        $admin = $this->createAdminUtilisateur();
        $client->loginUser($admin);

        $client->request('GET', '/admin/recherche-utilisateur?q=test');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table');
    }

    public function testRechercheRecetteFonctionne(): void
    {
        $client = static::createClient();
        $admin = $this->createAdminUtilisateur();
        $client->loginUser($admin);

        $client->request('GET', '/admin/recherche-recette?q=test');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.recipe-card');
    }

    public function testFormModificationUtilisateurAccessible(): void
    {
        $client = static::createClient();
        $admin = $this->createAdminUtilisateur();
        $client->loginUser($admin);

        $utilisateur = static::getContainer()->get(EntityManagerInterface::class)
            ->getRepository(Utilisateur::class)
            ->findOneBy([]);

        if (!$utilisateur) {
            $this->markTestSkipped('Aucun utilisateur disponible.');
        }

        $client->request('GET', '/admin/utilisateur/modifier/' . $utilisateur->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testSupprimerRecette(): void
    {
        self::bootKernel();
        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('admin@example.com');
        $utilisateur->setNom('Test');
        $utilisateur->setMdp('password');
        $utilisateur->setDateInscription(new \DateTime());
        $utilisateur->setRole('ROLE_ADMIN');
        $entityManager->persist($utilisateur);

        
        $recette = new Recette();
        $recette->setTitre('Recette à supprimer');
        $recette->setDescription('Description de la recette à supprimer');
        $recette->setImage('image.jpg');
        $recette->setDateCreation(new \DateTime());
        $recette->setUtilisateur($utilisateur);
        $entityManager->persist($recette);
        $entityManager->flush();

        $idRecette = $recette->getId();
        $this->assertNotNull($idRecette);

        
        $entityManager->remove($recette);
        $entityManager->flush();

        
        $deletedRecette = $entityManager->find(Recette::class, $idRecette);
        $this->assertNull($deletedRecette);

        
        $entityManager->remove($utilisateur);
        $entityManager->flush();
    }

}