<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Form\DataExtractType;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
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
        $achats = [];
        $errorMessage = null;

        if($form->isSubmitted() && $form->isValid()){
            $achats = $this->entityManager->getRepository(Achat::class)->extractSearchAchat($form)->getResult();
            if (empty($achats)) {
                $errorMessage = 'Aucun résultat pour cette recherche.';
                return $this->render('data_extract/index.html.twig', [
                    'form' => $form->createView(),
                    'achats' => $achats,
                    'errorMessage' => $errorMessage,
                ]);
            }
             $spreadsheet = new Spreadsheet();
             $sheet = $spreadsheet->getActiveSheet();
             $row = 1; // Ligne de départ pour le premier tableau

             // Supposons que $achats est un tableau contenant tous vos tableaux achat
             if (!empty($achats)) {
                
                $column = 'B'; // Colonne de départ pour les titres
            
                // Afficher les titres des colonnes

            
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
                foreach ($achat as $key => $value) {
                    if (strpos($key, "date_") === 0 && $achat[$key] !== null && $achat[$key] instanceof \DateTime) {
                        $achat[$key] = $achat[$key]->format('d/m/Y');
                    }
                }
                $reorderedAchat = [
                    'N° CHRONO' => $achat['numero_achat'],
                    'Code service' => $achat['code_service'],
                    'Nom service' => $achat['nom_service'],
                    'Code acheteur' => $achat['trigram'],
                    'Nom acheteur' => $achat['nom_utilisateur'],
                    'Code Formation' => $achat['code_formation'],
                    'Libellé formation' => $achat['libelle_formation'],
                    'N° SIRET' => $achat['num_siret'],
                    'Nom fournisseur' => $achat['nom_fournisseur'],
                    'Ville fournisseur' => $achat['ville'],
                    'CP' => $achat['code_postal'],
                    'PME ?' => $achat['pme'],
                    'Code client' => $achat['code_client'],
                    'N° CHORUS' => $achat['num_chorus_fournisseur'],
                    'N° Tel fournisseur' => $achat['tel'],
                    'N° fax fournisseur' => $achat['FAX'],
                    'Adresse mail' => $achat['mail'],
                    'Code UO' => $achat['code_uo'],
                    'Libellé UO' => $achat['libelle_uo'],
                    'Code CPV' => $achat['code_cpv'],
                    'Libellé CPV' => $achat['libelle_cpv'],
                    'ID Demande achat' => $achat['id_demande_achat'],
                    'Date sillage' => $achat['date_sillage'],
                    'Date commande CHORUS' => $achat['date_commande_chorus'],
                    'Date RUO' => $achat['date_valid_inter'],
                    'Date validation' => $achat['date_validation'],
                    'Date notification' => $achat['date_notification'],
                    'Date annulation' => $achat['date_annulation'],
                    'N° EJ' => $achat['numero_ej'],
                    'Objet de l achat' => $achat['objet_achat'],
                    'Type de marché' => $achat['type_marche'],
                    'Montant HT' => $achat['montant_ht'],
                    'TVA' => $achat['tva_taux'],
                    'Montant TTC' => $achat['montant_ttc'],
                    'Observations' => $achat['observations'],
                    'Etat de l achat' => $achat['etat_achat'],
                    'Marché avec pub' => $achat['place'],
                    'Devis' => $achat['devis'],
                    // ... autres champs dans l'ordre souhaité ...
                ];

                foreach ($reorderedAchat as $key => $value) {
                    $cell = $column . $row; // Construisez la référence de cellule pour les données, ex: B2, C2, etc.
                
                    if ($key === 'N° SIRET' ) {
                        $cellObject = $sheet->getCell($cell);
                        $cellObject->setValueExplicit($value, DataType::TYPE_STRING);
                
                        // Accéder à l'objet Style et définir le format comptabilité
                        $style = $cellObject->getStyle();
                        $numberFormat = $style->getNumberFormat();
                        $numberFormat->setFormatCode('0'); // Format comptabilité avec 0 chiffres après la virgule et pas de symbole
                    } else {
                        $sheet->setCellValue($cell, $value);
                    }
                
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
            $column = 'B'; // Colonne de départ pour les titres

// Afficher les titres des colonnes
foreach ($reorderedAchat as $key => $value) {
    $cell = $column . '1'; // Construisez la référence de cellule pour les titres, ex: B1, C1, etc.
    $sheet->setCellValue($cell, $key);
    $column++; // Passez à la colonne suivante pour le prochain titre
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
            'achats' => $achats,
            'errorMessage' => $errorMessage,
        ]);
    }
}
