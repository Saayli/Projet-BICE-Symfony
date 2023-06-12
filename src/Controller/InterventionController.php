<?php

namespace App\Controller;

use App\Entity\Intervention;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\Materiel;
use App\Service\CallApiService;
use Grpc\Call;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use function Sodium\add;

class InterventionController extends AbstractController
{
    #[Route('/', name: 'app_intervention')]
    public function index(CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        //Liste Intervention
        $interventions = $callApiService->getInterventions();
        $vehicules = $callApiService->getVehicules();
        $vehiculesactif = array();
        foreach ($vehicules as $vehicule){
            if ($vehicule['estActive'] == true){
                array_push($vehiculesactif, $vehicule);
            }
        }
        $choices = [];
        foreach ($vehiculesactif as $vehicule) {
            $choices[$vehicule['denomination']] = $vehicule['id'];
        }

        // Créer un nouvel objet
        $intervention = new Intervention();

        // Créer le formulaire
        $form = $this->createFormBuilder($intervention)
            ->add('denomination', TextType::class, [
                'label' => 'Dénomination'
            ])
            ->add('description', TextType::class, [
                'label' => 'Description'
            ])
            ->add('menuDeroulant', ChoiceType::class, [
                'label' => 'Menu déroulant',
                'choices' => $choices,
                'multiple' => true,
                'mapped' => false,
                'expanded' => true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Les données du formulaire sont valides, mettre les données en tableau
            $date = new DateTime();
            $formattedDate = $date->format('Y-m-d\TH:i:s.uP');

            $tableau = array(
                'denomination' => $form->getData()->getdenomination(),
                'date' => $formattedDate,
                'description' => $form->getData()->getdescription(),
            );
            if ($callApiService->addIntervention($tableau) == 200) {
                $interventions = $callApiService->getInterventions();
                $intervention = reset($interventions);
                $tableau2 =  $form->get('menuDeroulant')->getData();
                foreach ($tableau2 as $id){
                    $demande = array(
                        'id_vehicule' => $id,
                        'id_intervention' => $intervention['id']
                    );
                    $callApiService->addVehiculeIntervention($demande);

                }
                return $this->redirectToRoute('app_intervention');
            };

        }

        return $this->render('intervention/index.html.twig', [
            'formulaire' => $form->createview(),
            'interventions' => $interventions
        ]);
    }

    #[Route('/intervention/{id}/vehicules', name: 'intervention_vehicule_afficher')]
    public function interventionVehicule_afficher($id, CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $lsVehiculeIntervention = $callApiService->getAllByIdVIntervention($id);
        $lsVehicule = Array();
        foreach ($lsVehiculeIntervention as $VI){
            $idVI = $VI['id_Vehicule'];
            $vehicule = $callApiService->getByIdVehicule($idVI);
            array_push($lsVehicule,$vehicule);
        }


        return $this->render('intervention/interventionVehiculesAfficher.html.twig', [
            'vehicules' => $lsVehicule,
            'interventionId' => $id
        ]);
    }

    #[Route('/intervention/{id}/vehicules/stock/{id_vehicule}', name: 'VI_stock')]
    public function stock($id, CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine, $id_vehicule): Response
    {
        $interventionCible = $callApiService->getByIdIntervention($id);

        $form = $this->createFormBuilder()
            ->add('stockVehicule', FileType::class, [
                'label' => 'Matériel utilisé (format Excel)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //materiel utilisé
            $lsMateriel = Array();
            $materielFile = $form->get('stockVehicule')->getData();
            $materielFilePath = $materielFile->getPathname();
            $materielFileName = $materielFile->getClientOriginalName();

            if (($handle = fopen($materielFilePath, 'r')) !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    // Traitement des données du fichier CSV
                    // $data contient une ligne du fichier CSV sous forme de tableau
                    // On peut accéder aux valeurs de chaque colonne en utilisant les index du tableau
                    // Par exemple : $data[0] pour la première colonne, $data[1] pour la deuxième colonne, etc.
                    $objet = $callApiService->getByIdMateriel($data[0]);
                    array_push($lsMateriel, $objet);
                }
                fclose($handle);
            }
            foreach ($lsMateriel as $materiel){
                $lsvehiculeIntervention = $callApiService->getAllByIdVIntervention($id);
                $vehiculeInterventionId = 0;
                foreach ($lsvehiculeIntervention as $VI){
                    if($VI['id_Vehicule'] == $id_vehicule){
                        $vehiculeInterventionId = $VI['id'];
                    }
                }
                $tableau = array(
                    'id_Materiel' => $materiel['id'],
                    'id_VehiculeIntervention' => intval($vehiculeInterventionId),
                    'etat' => "ok"
                );
                $callApiService->addMateriel_VI($tableau);
                $materiel['id_vehicule'] = $id_vehicule;
                $materiel['stock'] = 'Véhicule';
                $callApiService->updateMateriel($materiel);
            }
        }

        return $this->render('intervention/retour.html.twig', ['formulaire' => $form->createview(),
            'i' => $interventionCible,
        ]);
    }

    #[Route('/intervention/{id}/vehicules/retour/{id_vehicule}', name: 'intervention_retour')]
    public function retour($id, CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine, $id_vehicule): Response
    {
        $interventionCible = $callApiService->getByIdIntervention($id);


        $form = $this->createFormBuilder()
            ->add('materielPasUtilise', FileType::class, [
                'label' => 'Matériel pas utilisé (format Excel)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                    ])
                ],
            ])
            ->add('materielUtilise', FileType::class, [
                'label' => 'Matériel utilisé (format Excel)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //matériel pas utilisé
            $lsPasUtilise = Array();
            $pasUtiliseFile = $form->get('materielPasUtilise')->getData();
            $pasUtiliseFilePath = $pasUtiliseFile->getPathname();
            $pasUtiliseFileName = $pasUtiliseFile->getClientOriginalName();

            if (($handle = fopen($pasUtiliseFilePath, 'r')) !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    // Traitement des données du fichier CSV
                    // $data contient une ligne du fichier CSV sous forme de tableau
                    // On peut accéder aux valeurs de chaque colonne en utilisant les index du tableau
                    // Par exemple : $data[0] pour la première colonne, $data[1] pour la deuxième colonne, etc.
                    $objet = $callApiService->getByIdMateriel($data[0]);
                    array_push($lsPasUtilise, $objet);
                }
                fclose($handle);
            }

            //materiel utilisé
            $lsUtilise = Array();
            $utiliseFile = $form->get('materielUtilise')->getData();
            $utiliseFilePath = $utiliseFile->getPathname();
            $utiliseFileName = $utiliseFile->getClientOriginalName();

            if (($handle = fopen($utiliseFilePath, 'r')) !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    // Traitement des données du fichier CSV
                    // $data contient une ligne du fichier CSV sous forme de tableau
                    // On peut accéder aux valeurs de chaque colonne en utilisant les index du tableau
                    // Par exemple : $data[0] pour la première colonne, $data[1] pour la deuxième colonne, etc.
                    $objet = $callApiService->getByIdMateriel($data[0]);
                    array_push($lsUtilise, $objet);
                }
                fclose($handle);
            }

            //Gestion utilisation matériel
            foreach ($lsUtilise as $objet ){
                $callApiService->addUtilisationMateriel($objet);
            }

            //Gestion matériel perdu
            $listeMateriel = $callApiService->getMateriels();
            $lsMaterielDansVehicule = Array();
            foreach ($listeMateriel as $materiel){
                if ($materiel['id_vehicule'] == $id_vehicule){
                    array_push($lsMaterielDansVehicule, $materiel);
                }
            }
            $nbRetour = count($lsMaterielDansVehicule);
            $nbParti = count($lsUtilise) + count($lsPasUtilise);
            if($nbRetour == $nbParti){

            }
            else{
                $fusion = array_merge($lsPasUtilise, $lsUtilise);
                $cpt = 0;
                $differences = Array();
                foreach ($lsMaterielDansVehicule as $materiel){
                    $cpt = 0;
                    foreach ($fusion as $materielRetour){
                        if ($materiel['id'] == $materielRetour['id']){
                            $cpt = $cpt + 1;
                        }
                    }
                    if ($cpt == 0){
                        array_push($differences, $materiel);
                    }
                }
                foreach ($differences as $item){
                    $item['stock'] = "Perdu";
                    $item['estStocke'] = false;
                    $callApiService->updateMateriel($item);
                }
            }





        }

        return $this->render('intervention/retour.html.twig', ['formulaire' => $form->createview(),
            'i' => $interventionCible,
        ]);
    }

    #[Route('/intervention/{id}/vehicules/{id_vehicule}/materiels', name: 'intervention_vehicule_materiel_afficher')]
    public function interventionVehiculeMateriel_afficher($id, CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine, $id_vehicule): Response
    {
        $lsvehiculeIntervention = $callApiService->getAllByIdVIntervention($id);
        $vehiculeInterventionId = 0;
        foreach ($lsvehiculeIntervention as $VI){
            if($VI['id_Vehicule'] == $id_vehicule){
                $vehiculeInterventionId = $VI['id'];
            }
        }
        $lsMateriel_VI = $callApiService->getAllByIdMateriel_VI($vehiculeInterventionId);
        $lsMateriel = Array();
        foreach ($lsMateriel_VI as $materiel_VI){
            $id_materiel_VI = $materiel_VI['id_Materiel'];
            $materiel = $callApiService->getByIdMateriel($id_materiel_VI);
            array_push($lsMateriel, $materiel);
        }
        return $this->render('intervention/interventionVehiculesMaterielAfficher.html.twig', [
            'materiels' => $lsMateriel,
        ]);
    }


}
