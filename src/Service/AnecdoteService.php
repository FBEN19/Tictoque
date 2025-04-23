<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AnecdoteService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = '6c5ea8ebb54d4447863b046fb9d18304';
    }

    public function getAnecdote(): ?string
    {
        $url = 'https://api.spoonacular.com/food/trivia/random?apiKey=' . $this->apiKey;

        try {
            $response = $this->client->request('GET', $url);
            $data = $response->toArray();
            return $data['text'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}