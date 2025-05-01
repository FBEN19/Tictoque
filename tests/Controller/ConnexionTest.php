<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
        $client = static::createClient();
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $hasher = self::getContainer()->get('security.user_password_hasher');

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('test@example.com');
        $utilisateur->setNom('Test');
        $utilisateur->setRole('ROLE_USER');
        $utilisateur->setDateInscription(new \DateTime());
        $utilisateur->setMdp(
            $hasher->hashPassword($utilisateur, 'password')
        );
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        $crawler = $client->request('GET', '/connexion');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test@example.com',
            '_password' => 'password',
        ]);

        $client->submit($form);
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h2:contains("Connexion")');
    }
}