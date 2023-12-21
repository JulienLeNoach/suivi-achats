<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Form\DataExtractType;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DataExtractController extends AbstractController
{

    private $entityManager;
    private $kernel;

    private $projectDir;

    public function __construct(EntityManagerInterface $entityManager,KernelInterface $kernel)
    {
    $this->projectDir = $kernel->getProjectDir();
    $this->entityManager = $entityManager;


    }
    
    #[Route('/dataextract', name: 'data_extract')]
    public function index(Request $request,KernelInterface $kernel): Response
    {

        $form = $this->createForm(DataExtractType::class, null, [
        ]);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $achats = $this->entityManager->getRepository(Achat::class)->extractSearchAchat($form)->getResult();

            //   dd($achats);
             $spreadsheet = new Spreadsheet();
             $sheet = $spreadsheet->getActiveSheet();
             $row = 1; // Ligne de départ pour le premier tableau

             // Supposons que $achats est un tableau contenant tous vos tableaux achat
             if (!empty($achats)) {
                
                $column = 'B'; // Colonne de départ pour les titres
            
                // Afficher les titres des colonnes
                foreach (array_keys($achats[0]) as $key) {
                    $cell = $column . '1'; // Construisez la référence de cellule pour les titres, ex: B1, C1, etc.
                    $sheet->setCellValue($cell, $key);
            
                    $column++; // Passez à la colonne suivante pour le prochain titre
                }
            
                // Afficher les données de chaque achat
                $row = 2; // Ligne de départ pour les données
            
                foreach ($achats as $achat) {
                    $column = 'B'; // Réinitialiser la colonne pour chaque nouveau tableau achat
                    if (isset($achat["devis"])){
                    $achat["devis"] = $achat["devis"] == 0 ? "Prescripteur" : "GSBdD/PFAF";
                }
                if (isset($achat["type_marche"])){
                    $achat["type_marche"] = $achat["type_marche"] == 0 ? "MABC" : "MPPA";
                }
                if (isset($achat["etat_achat"])){
                    $achat["etat_achat"] = $achat["etat_achat"] == 0 ? "En cours" : ($achat["etat_achat"] == 1 ? "Annulé" : "Validé");
                }
                if (isset($achat["place"])){
                    $achat["place"] = $achat["place"] == 0 ? "Non" : "Oui";
                }

                    foreach ($achat as $value) {
                        $cell = $column . $row; // Construisez la référence de cellule pour les données, ex: B2, C2, etc.
                        $sheet->setCellValue($cell, $value);
                        $column++; // Passez à la colonne suivante pour la prochaine valeur
                    }
            
                    $row++; // Après avoir terminé avec un tableau achat, passez à la ligne suivante
                }
            }
            $column = 'B'; // Commencez à la colonne 'B'
            $highestColumn = $sheet->getHighestColumn(); // Obtenez la dernière colonne utilisée
            
            while ($column != $highestColumn) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
                $column++; // Passez à la colonne suivante
            }
            
            // Assurez-vous de traiter également la dernière colonne
            $sheet->getColumnDimension($highestColumn)->setAutoSize(true);
            $sheet->calculateColumnWidths();
            $filePath = $kernel->getProjectDir() . '/public/nom_de_fichier.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->setIncludeCharts(true);
            $writer->save($filePath);
            // return $filePath; // ou retournez un objet BinaryFileResponse si vous le souhaitez
            return new BinaryFileResponse($filePath);

        }
        return $this->render('data_extract/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
