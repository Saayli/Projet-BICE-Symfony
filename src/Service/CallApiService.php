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

    public function addIntervention($data)
    {
        $jsonContent = json_encode($data);
        $response = $this->client->request('POST',
            'https://localhost:7238/InterventionAjouter',
        [
            'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json'
            ],
            'body' => $jsonContent,
        ]);
        return $response->getStatusCode();
    }

    public function getByIdIntervention($data)
    {
        $tabId = array(
            'id' => intval($data),
        );
        $jsonContent = json_encode($tabId);
        $response = $this->client->request('POST',
            'https://localhost:7238/InterventionGetById',
            [
                'query' => $tabId
            ]);
        return $response->ToArray();
    }

    public function getMateriels()
    {
        $response = $this->client->request('POST',
            'https://localhost:7238/MaterielGetAll');
        return $response->toArray();
    }

    public function addMateriel($data)
    {
        $jsonContent = json_encode($data);
        $response = $this->client->request('POST',
            'https://localhost:7238/MaterielAjouter',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ],
                'body' => $jsonContent,
            ]);
        return $response->getStatusCode();
    }

    public function getVehicules()
    {
        $response = $this->client->request('POST',
            'https://localhost:7238/VehiculeGetAll');
        return $response->toArray();
    }

    public function addVehicule($data)
    {
        $jsonContent = json_encode($data);
        $response = $this->client->request('POST',
            'https://localhost:7238/VehiculeAjouter',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ],
                'body' => $jsonContent,
            ]);
        return $response->getStatusCode();
    }

    public function getByIdVehicule($data)
    {
        $tabId = array(
            'id' => intval($data),
        );
        $response = $this->client->request('POST',
            'https://localhost:7238/VehiculeGetById',
            [
                'query' => $tabId
            ]);
        return $response->ToArray();
    }

    public function updateVehicule($data)
    {
        $jsonContent = json_encode($data);
        $response = $this->client->request('POST',
            'https://localhost:7238/VehiculeModifier',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ],
                'body' => $jsonContent,
            ]);
        return $response->getStatusCode();
    }

    public function getByIdMateriel($data)
    {
        $tabId = array(
            'id' => intval($data),
        );
        $response = $this->client->request('POST',
            'https://localhost:7238/MaterielGetById',
            [
                'query' => $tabId
            ]);
        return $response->ToArray();
    }

    public function updateMateriel($data)
    {
        $jsonContent = json_encode($data);
        $response = $this->client->request('POST',
            'https://localhost:7238/MaterielModifier',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ],
                'body' => $jsonContent,
            ]);
        return $response->getStatusCode();
    }

    public function addUtilisationMateriel($data)
    {
        $jsonContent = json_encode($data);
        $response = $this->client->request('POST',
            'https://localhost:7238/MaterielAjoutUtilisation',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ],
                'body' => $jsonContent,
            ]);
        return $response->getStatusCode();
    }

    public function addVehiculeIntervention($data)
    {
        $jsonContent = json_encode($data);
        $response = $this->client->request('POST',
            'https://localhost:7238/VInterventionAjouter',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ],
                'body' => $jsonContent,
            ]);
        return $response->getStatusCode();
    }

    public function getAllByIdVIntervention($data)
    {
        $tabId = array(
            'id' => intval($data),
        );
        $response = $this->client->request('POST',
            'https://localhost:7238/VInterventionGetAllById',
            [
                'query' => $tabId
            ]);
        return $response->ToArray();
    }

    public function addMateriel_VI($data)
    {
        $jsonContent = json_encode($data);
        $response = $this->client->request('POST',
            'https://localhost:7238/Materiel_VIAjouter',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ],
                'body' => $jsonContent,
            ]);
        return $response->getStatusCode();
    }

    public function getAllByIdMateriel_VI($data)
    {
        $tabId = array(
            'id' => intval($data),
        );
        $response = $this->client->request('POST',
            'https://localhost:7238/Materiel_VIGettAllById',
            [
                'query' => $tabId
            ]);
        return $response->ToArray();
    }
}