<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

class ConnexionTest extends WebTestCase
{
    public function testAffichagePageConnexion(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Connexion');
    }

    public function testConnexionReussie(): void
    {
        $entityManager = $this->getContainer()->get(EntityManagerInterface::class);

        $utilisateur = new Utilisateur();
        $utilisateur->setNom("Test Nom");
        $utilisateur->setEmail("test@email.com");
        $utilisateur->setMdp(password_hash("motdepasse", PASSWORD_BCRYPT));
        
        $utilisateur->setRole('ROLE_USER');

        $entityManager->persist($utilisateur);
        $entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('POST', '/connexion', [
            'email' => 'test@email.com',
            'password' => 'motdepasse',
        ]);

        
        $this->assertResponseRedirects('/');
    }
}