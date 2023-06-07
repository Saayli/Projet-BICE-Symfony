<?php

namespace App\Service;

use http\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
    public function getInterventions()
    {
        $response = $this->client->request('POST',
        'https://localhost:7238/InterventionGetAll');
        return $response->toArray();
    }
}