<?php

namespace App\Controller;

use App\Entity\Intervention;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterventionController extends AbstractController
{
    private $client;
    public function __construct(HttpClientInterface $client){
        $this->client = $client;
    }
    #[Route('/', name: 'app_intervention')]
    public function index(\Doctrine\Persistence\ManagerRegistry $doctrine, Request $request): Response
    {
        $response = $this->client->request(
            'GET',
            'https://localhost:7238/swagger/v1/swagger.json'
        );

        return $this->render('home/index.hmtl.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
