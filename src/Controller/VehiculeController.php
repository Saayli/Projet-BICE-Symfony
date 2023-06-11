<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Service\CallApiService;
use Grpc\Call;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class VehiculeController extends AbstractController
{
    #[Route('/vehicule', name: 'app_vehicule')]
    public function index(CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        //Liste Intervention
        $vehicules = $callApiService->getVehicules();

        // Créer un nouvel objet
        $vehicule = new Vehicule();

        // Créer le formulaire
        $form = $this->createFormBuilder($vehicule)
            ->add('denomination', TextType::class, [
                'label' => 'Dénomination'
            ])
            ->add('numero', IntegerType::class, [
                'label' => 'Numéro'
            ])
            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Les données du formulaire sont valides, mettre les données en tableau

            $tableau = array(
                'immatriculation' => $form->getData()->getimmatriculation(),
                'denomination' => $form->getData()->getdenomination(),
                'numero' => $form->getData()->getnumero(),
                'estActive' => true
            );
            if($callApiService->addVehicule($tableau) == 200){
                return $this->redirectToRoute('app_vehicule');
            };

        }

        return $this->render('vehicule/index.html.twig', [
            'formulaire'=> $form->createview(),
            'vehicules' => $vehicules
        ]);
    }

    #[Route('/vehicule/modifier/{id}', name: 'vehicule_modifier')]
    public function modifier($id, CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $vehiculeCible = $callApiService->getByIdVehicule($id);

        // Créer un nouvel objet
        $vehicule = new Vehicule();

        // Créer le formulaire
        $form = $this->createFormBuilder($vehicule)
            ->add('denomination', TextType::class, [
                'label' => 'Dénomination'
            ])
            ->add('numero', IntegerType::class, [
                'label' => 'Numéro'
            ])
            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Les données du formulaire sont valides, mettre les données en tableau

            $tableau = array(
                'id' => intval($id),
                'denomination' => $form->getData()->getdenomination(),
                'numero' => $form->getData()->getnumero(),
                'immatriculation' => $form->getData()->getimmatriculation(),
            );
            if ($callApiService->updateVehicule($tableau) == 200) {
                return $this->redirectToRoute('app_vehicule');
            };

        }

        return $this->render('vehicule/modifier.html.twig', [
            'formulaire' => $form->createview(),
            'v' => $vehiculeCible
        ]);
    }

    #[Route('/vehicule/supprimer/{id}', name: 'vehicule_supprimer')]
    public function supprimer($id, CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $vehiculeCible = $callApiService->getByIdVehicule($id);

        // Créer un nouvel objet
        $vehicule = new Vehicule();

        // Créer le formulaire
        $form = $this->createFormBuilder($vehicule)
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Les données du formulaire sont valides, mettre les données en tableau

            $tableau = array(
                'id' => intval($id),
                'denomination' => $vehiculeCible['denomination'],
                'numero' => $vehiculeCible['numero'],
                'immatriculation' => $vehiculeCible['immatriculation'],
                'estActive' => false
            );
            if($callApiService->updateVehicule($tableau) == 200){
                return $this->redirectToRoute('app_vehicule');
            };

        }

        return $this->render('vehicule/supprimer.html.twig', [
            'formulaire'=> $form->createview(),
            'v' => $vehiculeCible
        ]);
    }
}
