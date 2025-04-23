<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AnecdoteService
{
    private $client;
    private $apiKey;
    private $storagePath;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = '6c5ea8ebb54d4447863b046fb9d18304';
        $this->storagePath = __DIR__ . '/../../var/anecdote.json';
    }

    public function getAnecdote(): ?string
    {
        if (file_exists($this->storagePath)) {
            $data = json_decode(file_get_contents($this->storagePath), true);

            if ($data && isset($data['date'], $data['text']) && $data['date'] === date('Y-m-d')) {
                return $data['text'];
            }
        }

        $url = 'https://api.spoonacular.com/food/trivia/random?apiKey=' . $this->apiKey;

        try {
            $response = $this->client->request('GET', $url);
            $data = $response->toArray();
            $anecdote = $data['text'] ?? null;

            if ($anecdote) {
                file_put_contents($this->storagePath, json_encode([
                    'date' => date('Y-m-d'),
                    'text' => $anecdote
                ]));
            }

            return $anecdote;
        } catch (\Exception $e) {
            return null;
        }
    }
}