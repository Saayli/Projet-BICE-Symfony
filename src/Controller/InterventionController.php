<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Service\CallApiService;
use Grpc\Call;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterventionController extends AbstractController
{
    #[Route('/', name: 'app_intervention')]
    public function index(CallApiService $callApiService): Response
    {
        return $this->render('intervention/index.html.twig', [
        ]);
    }
}
