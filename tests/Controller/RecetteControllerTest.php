<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Recette;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

class RecetteControllerTest extends WebTestCase
{
    private $client;
    private $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Top recettes');
    }

    private function authentifierUtilisateur(): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Testeur');
        $utilisateur->setEmail(uniqid() . '@example.com');
        $utilisateur->setRole('ROLE_USER');
        $utilisateur->setDateInscription(new \DateTime());

        $passwordHasher = self::getContainer()->get('security.user_password_hasher');
        $utilisateur->setMdp($passwordHasher->hashPassword($utilisateur, 'password'));

        $this->em->persist($utilisateur);
        $this->em->flush();

        $this->client->loginUser($utilisateur);

        return $utilisateur;
    }

    public function testAjouterRecette(): void
    {
        $this->authentifierUtilisateur();

        $crawler = $this->client->request('GET', '/ajouter-recette');
        $this->assertResponseIsSuccessful();

        $imagePath = __DIR__ . '/../../public/images/tictoque.jpg'; 
        $imageFile = new UploadedFile($imagePath, 'tictoque.jpg', 'image/jpeg', null, true);

        $form = $crawler->selectButton('Ajouter la recette')->form([
            'recette[titre]' => 'Recette test',
            'recette[description]' => 'Description test',
            'recette[image]' => $imageFile,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/profil');
    }

    public function testModifierRecette(): void
    {
        $utilisateur = $this->authentifierUtilisateur();
        $recette = $this->createRecette($utilisateur);
    
        $crawler = $this->client->request('GET', '/modifier-recette/' . $recette->getId());
        $this->assertResponseIsSuccessful();
    
        $form = $crawler->selectButton('Modifier la recette')->form();
        $form['recette[titre]'] = 'Recette modifiée';
        $form['recette[description]'] = 'Nouvelle description';
    
        $this->client->submit($form);
        
        $this->assertEquals('/modifier-recette/' . $recette->getId(), parse_url($crawler->getUri(), PHP_URL_PATH));
    }
    public function testAfficherRecette(): void
    {
        $recette = $this->createRecette($this->authentifierUtilisateur());

        $this->client->request('GET', '/recette/' . $recette->getId());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $recette->getTitre());
    }

    public function testAjouterNote(): void
    {
        $utilisateur = $this->authentifierUtilisateur();
        $recette = $this->createRecette($utilisateur);
    
        
        $this->authentifierUtilisateur();

        
        $crawler = $this->client->request('POST', '/recette/' . $recette->getId(), [
            'note[note]' => 4,
        ]);

        $this->assertSelectorNotExists('.form-error');

        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
    }

    public function testAjouterCommentaire()
    {
        $utilisateur = $this->authentifierUtilisateur();
        $recette = $this->createRecette($utilisateur);
    
        
        $this->authentifierUtilisateur();

        $crawler = $this->client->request('POST', '/recette/' . $recette->getId(), [
            'commentaire[texte]' => 'Ceci est un commentaire de test.',
        ]);
        $this->assertResponseIsSuccessful();

    }

    private function createUtilisateur(): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Test');
        $utilisateur->setEmail(uniqid() . '@example.com');
        $utilisateur->setDateInscription(new \DateTime());

        $passwordHasher = self::getContainer()->get('security.user_password_hasher');
        $utilisateur->setMdp($passwordHasher->hashPassword($utilisateur, 'password'));

        $utilisateur->setRole('ROLE_USER');

        $this->em->persist($utilisateur);
        $this->em->flush();

        return $utilisateur;
    }

    private function createRecette(Utilisateur $utilisateur): Recette
    {
        $recette = new Recette();
        $recette->setTitre('Test');
        $recette->setDescription('Description de test');
        $recette->setImage('tictoque.jpg');
        $recette->setDateCreation(new \DateTime());
        $recette->setUtilisateur($utilisateur);

        $this->em->persist($recette);
        $this->em->flush();

        return $recette;
    }
}