<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FruitService
{
    private $client;
    private $fruits;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getFruitDuJour(): ?array
    {
        try {
            $response = $this->client->request('GET', 'https://www.fruityvice.com/api/fruit/all');
            $fruits = $response->toArray();

            if (empty($fruits)) {
                return null;
            }

            $jour = (int) date('z');
            $index = $jour % count($fruits);

            return $fruits[$index];
        } catch (\Exception $e) {
            return null;
        }
    }
}