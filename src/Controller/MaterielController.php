<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CallApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Materiel;
class MaterielController extends AbstractController
{
    #[Route('/materiel', name: 'app_materiel')]
    public function index(CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        //Liste Intervention
        $materiels = $callApiService->getMateriels();

        // Créer un nouvel objet
        $materiel = new Materiel();

        // Créer le formulaire
        $form = $this->createFormBuilder($materiel)
            ->add('id', TextType::class, [
                'label' => 'Identifiant'
            ])
            ->add('denomination', TextType::class, [
                'label' => 'Dénomination'
            ])
            ->add('categorie', TextType::class, [
                'label' => 'Catégorie'
            ])
            ->add('utilisation', IntegerType::class, [
                'label' => 'Utilisation'
            ])
            ->add('utilisationMax', IntegerType::class, [
                'label' => 'Utilisation Max.',
            ])
            ->add('dateControle', DateType::class, [
                'label' => 'Date de Contrôle',
            ])
            ->add('dateExpiration', DateType::class, [
                'label' => "Date d'Expiration",
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
                'id' => $form->getData()->getid(),
                'denomination' => $form->getData()->getdenomination(),
                'categorie' => $form->getData()->getcategorie(),
                'utilisation' => $form->getData()->getutilisation(),
                'utilisationMax' => $form->getData()->getutilisationMax(),
                'dateExpiration' => $form->getData()->getdateExpiration()->format('Y-m-d'),
                'dateControle' => $form->getData()->getdateControle()->format('Y-m-d'),
                'stock' => "Caserne",
                'estStocke' => true,
                'estActive' => true,
                'id_vehicule' => null,
            );

            if ($callApiService->addMateriel($tableau) == 200) {
                return $this->redirectToRoute('app_materiel');
            };

        }

        return $this->render('materiel/index.html.twig', [
            'formulaire' => $form->createview(),
            'materiels' => $materiels
        ]);
    }

    #[Route('/materiel/modifier/{id}', name: 'materiel_modifier')]
    public function modifier($id, CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $materielCible = $callApiService->getByIdMateriel($id);

        // Créer un nouvel objet
        $materiel = new Materiel();

        // Créer le formulaire
        $form = $this->createFormBuilder($materiel)
            ->add('id', TextType::class, [
                'label' => 'Identifiant'
            ])
            ->add('denomination', TextType::class, [
                'label' => 'Dénomination'
            ])
            ->add('categorie', TextType::class, [
                'label' => 'Catégorie'
            ])
            ->add('utilisation', IntegerType::class, [
                'label' => 'Utilisation'
            ])
            ->add('utilisationMax', IntegerType::class, [
                'label' => 'Utilisation Max.',
            ])
            ->add('dateControle', DateType::class, [
                'label' => 'Date de Contrôle',
            ])
            ->add('dateExpiration', DateType::class, [
                'label' => "Date d'Expiration",
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
                'categorie' => $form->getData()->getcategorie(),
                'utilisation' => $form->getData()->getutilisation(),
                'utilisationMax' => $form->getData()->getutilisationMax(),
                'dateExpiration' => $form->getData()->getdateExpiration()->format('Y-m-d'),
                'dateControle' => $form->getData()->getdateControle()->format('Y-m-d'),
                'stock' => "Caserne",
                'estStocke' => true,
                'estActive' => true,
                'id_vehicule' => null,
            );
            if($callApiService->updateMateriel($tableau) == 200){
                return $this->redirectToRoute('app_materiel');
            };
        }

        return $this->render('materiel/modifier.html.twig', [
            'formulaire' => $form->createview(),
            'm' => $materielCible,
            'callApiService' => $callApiService
        ]);
    }

    #[Route('/materiel/supprimer/{id}', name: 'materiel_supprimer')]
    public function supprimer($id, CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $materielCible = $callApiService->getByIdMateriel($id);
        // Créer un nouvel objet
        $materiel = new Materiel();

        // Créer le formulaire
        $form = $this->createFormBuilder($materiel)
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
                'denomination' => $materielCible['denomination'],
                'categorie' => $materielCible['categorie'],
                'utilisation' => $materielCible['utilisation'],
                'utilisationMax' => $materielCible['utilisationMax'],
                'dateExpiration' => $materielCible['dateExpiration'],
                'dateControle' => $materielCible['dateControle'],
                'stock' => $materielCible['stock'],
                'estStocke' => $materielCible['estStocke'],
                'estActive' => false,
                'id_vehicule' => $materielCible['id_vehicule'],
            );
            if($callApiService->updateMateriel($tableau) == 200){
                return $this->redirectToRoute('app_materiel');
            };

        }

        return $this->render('materiel/supprimer.html.twig', [
            'formulaire'=> $form->createview(),
            'm' => $materielCible
        ]);
    }
}
