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
use function Sodium\add;

class InterventionController extends AbstractController
{
    #[Route('/', name: 'app_intervention')]
    public function index(CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        //Liste Intervention
        $interventions = $callApiService->getInterventions();

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
                return $this->redirectToRoute('app_intervention');
            };

        }

        return $this->render('intervention/index.html.twig', [
            'formulaire' => $form->createview(),
            'interventions' => $interventions
        ]);
    }

    #[Route('/retour/{id}', name: 'intervention_retour')]
    public function retour($id, CallApiService $callApiService, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
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
            $lsPasUtilise = new ArrayCollection();
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
                    dd($objet);
                    $lsPasUtilise.add($objet);
                }
                fclose($handle);
            }

            //materiel utilisé
            $lsUtilise = new ArrayCollection();
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
                    $lsUtilise.add($objet);
                }
                fclose($handle);
            }




        }

        return $this->render('intervention/retour.html.twig', ['formulaire' => $form->createview(),
            'i' => $interventionCible,
            ]);
    }
}
