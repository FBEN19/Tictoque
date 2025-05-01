<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RechercheControllerTest extends WebTestCase
{
    public function testAccesPageRecherche()
    {
        $client = static::createClient();
        $client->request('GET', '/recherche');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Résultats de la recherche');
    }

    public function testRechercheAvecTerme()
    {
        $client = static::createClient();
        $client->request('GET', '/recherche?q=Test%20Recipe');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.card');
    }

    public function testRechercheAvecNoteMinimale()
    {
        $client = static::createClient();
        $client->request('GET', '/recherche?min_rating=0');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.card');
    }

    public function testRechercheAvecExclusionIngredient()
    {
        $client = static::createClient();
        $client->request('GET', '/recherche?exclude_ingredient=chocolat');

        $this->assertResponseIsSuccessful();

        $contenu = $client->getCrawler()->filter('.card');

        if ($contenu->count() > 0) {
            $this->assertSelectorExists('.card');
        } else {
            $this->assertSelectorTextContains('p', 'Aucune recette trouvée.');
        }
    }

    public function testRechercheAvecTriParNote()
    {
        $client = static::createClient();
        $client->request('GET', '/recherche?sort_order=rating');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.card');
    }

    public function testRechercheAucunResultat()
    {
        $client = static::createClient();
        $client->request('GET', '/recherche?q=recetteintrouvable123');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('p', 'Aucune recette trouvée.');
    }
}