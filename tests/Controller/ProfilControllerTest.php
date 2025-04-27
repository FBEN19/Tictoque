<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Utilisateur;
use App\Entity\Recette;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProfilControllerTest extends WebTestCase
{
    private function creerUtilisateur()
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Benjamin');
        $utilisateur->setEmail('benjamin@example.com');

        return $utilisateur;
    }

    public function testAccesProfil()
    {
        $client = static::createClient();

        $utilisateur = $this->creerUtilisateur();
        $client->loginUser($utilisateur);

        $client->request('GET', '/profil');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('h1');
    }

    public function testModifierProfil()
    {
        $client = static::createClient();

        $utilisateur = $this->creerUtilisateur();
        $client->loginUser($utilisateur);

        $client->request('POST', '/modifier-profil', [
            'nom' => 'Benji',
            'email' => 'benji@example.com'
        ]);

        $this->assertResponseRedirects('/profil');
    }

    public function testModifierProfilAvecChampsVides()
    {
        $client = static::createClient();

        $utilisateur = $this->creerUtilisateur();
        $client->loginUser($utilisateur);

        $client->request('POST', '/modifier-profil', [
            'nom' => '',
            'email' => ''
        ]);

        $this->assertResponseRedirects('/profil');
    }

    public function testUploadPhotoProfil()
    {
        $client = static::createClient();

        $utilisateur = $this->creerUtilisateur();
        $client->loginUser($utilisateur);

        $cheminFichierTemporaire = sys_get_temp_dir() . '/photo_test.jpg';
        file_put_contents($cheminFichierTemporaire, 'contenu fake d image');

        $fichier = new UploadedFile(
            $cheminFichierTemporaire,
            'photo_test.jpg',
            'image/jpeg',
            null,
            true
        );

        $client->request('POST', '/changer-photo', [
            'photo' => $fichier
        ]);

        $this->assertResponseRedirects('/profil');
    }

    public function testSupprimerRecette()
    {
        $client = static::createClient();

        $utilisateur = $this->creerUtilisateur();
        $client->loginUser($utilisateur);

        $recette = new Recette();
        $recette->setTitre('Recette test');
        $recette->setImage('test.jpg');

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($utilisateur);
        $em->persist($recette);
        $em->flush();

        $client->request('GET', '/supprimer-recette/' . $recette->getId());

        $this->assertResponseRedirects('/profil');
    }
}