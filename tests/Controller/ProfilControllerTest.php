<?php
namespace App\Tests\Controller;

use App\Entity\Utilisateur;
use App\Entity\Recette;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProfilControllerTest extends WebTestCase
{
    public function testAccesProfil(): void
    {
        $client = static::createClient();
        $utilisateur = $this->createUtilisateur($client);

        $client->loginUser($utilisateur);
        $client->request('GET', '/profil');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', $utilisateur->getNom());
    }

    private function createUtilisateur($client): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Testeur');
        $utilisateur->setEmail('test@test.fr');
        $utilisateur->setMdp('motdepasse');
        $utilisateur->setDateInscription(new \DateTime());
        $utilisateur->setRole('ROLE_USER');

        $em = $client->getContainer()->get('doctrine')->getManager();
        $em->persist($utilisateur);
        $em->flush();

        return $utilisateur;
    }

    public function testModifierProfil(): void
    {
        $client = static::createClient();
        $utilisateur = $this->createUtilisateur($client);
        $client->loginUser($utilisateur);

        $client->request('POST', '/modifier-profil', [
            'nom' => 'NomModifié',
            'email' => 'nouvel@email.fr',
        ]);

        $this->assertResponseRedirects('/profil');

        $client->followRedirect();
        $this->assertSelectorTextContains('h3', 'NomModifié');
    }

    public function testUploadPhotoProfil(): void
    {
        $client = static::createClient();
        $utilisateur = $this->createUtilisateur($client);
        $client->loginUser($utilisateur);

        $filePath = __DIR__ . '/../../public/test.jpg';
        copy(__DIR__ . '/../../public/images/tictoque.jpg', $filePath);

        $client->request('POST', '/changer-photo', [], [
            'photo' => new UploadedFile($filePath, 'test.jpg', 'image/jpeg', null, true),
        ]);

        $this->assertResponseRedirects('/profil');
    }

    public function testSupprimerRecette()
{
    $entityManager = self::getContainer()->get('doctrine')->getManager();

    $utilisateur = new Utilisateur();
    $utilisateur->setNom('Test');
    $utilisateur->setEmail('test@example.com');
    $utilisateur->setMdp('password');
    $utilisateur->setRole('ROLE_USER');
    $utilisateur->setDateInscription(new \DateTime());

    $entityManager->persist($utilisateur);
    $entityManager->flush();

    $recette = new Recette();
    $recette->setTitre('Test');
    $recette->setDescription('Description de test');
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