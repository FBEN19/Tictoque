<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InscriptionControllerTest extends WebTestCase
{
    public function testInscriptionPageIsUp(): void
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h2', 'Inscription');
    }

    public function testSuccessfulInscription(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');

        $form = $crawler->selectButton('S\'inscrire')->form([
            'inscription[nom]' => 'John Doe',
            'inscription[email]' => 'johndoe@example.com',
            'inscription[mdp]' => 'securepassword',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/connexion');

        $user = self::getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['email' => 'johndoe@example.com']);    $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->getNom());
        $this->assertTrue(password_verify('securepassword', $user->getMdp()));
        $this->assertEquals('ROLE_USER', $user->getRole());
        $this->assertInstanceOf(\DateTime::class, $user->getDateInscription());
    }
    public function testFailedInscriptionDueToInvalidEmail(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
    
        $form = $crawler->selectButton('S\'inscrire')->form([
            'inscription[nom]' => 'Jane Doe',
            'inscription[email]' => 'invalid-email',
            'inscription[mdp]' => 'securepassword',
        ]);
    
        $client->submit($form);
        $this->assertSelectorTextContains('.invalid-feedback', "L'adresse e-mail n'est pas valide.");
        }


}