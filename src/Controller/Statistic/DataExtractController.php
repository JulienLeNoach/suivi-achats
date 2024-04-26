<?php

namespace App\Controller\Statistic;

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

        if($form->isSubmitted()  ){
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
             $style = $sheet->getStyle('B1:E1');
$font = $style->getFont();

// Mettre en gras
$font->setBold(true);

// Agrandir la police
$font->setSize(14); 
             $sheet->mergeCells('B1:G1');
$sheet->setCellValue('B1', "EXTRACTION DES DONNEES BASE SUIVI DES ACHATS POUR L'ANNEE ");
             // Supposons que $achats est un tableau contenant tous vos tableaux achat
             if (!empty($achats)) {
                
                $column = 'B'; // Colonne de départ pour les titres
            
                // Afficher les titres des colonnes

            
                // Afficher les données de chaque achat
                $row = 3; // Ligne de départ pour les données
            
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
                $reorderedAchat = [];

                if (array_key_exists('numero_achat', $achat)) {
                    $reorderedAchat['N° CHRONO'] = $achat['numero_achat'];
                }
                if (array_key_exists('code_service', $achat)) {
                    $reorderedAchat['Code service'] = $achat['code_service'];
                }
                if (array_key_exists('nom_service', $achat)) {
                    $reorderedAchat['Nom service'] = $achat['nom_service'];
                }
                if (array_key_exists('trigram', $achat)) {
                    $reorderedAchat['Code acheteur'] = $achat['trigram'];
                }
                if (array_key_exists('nom_utilisateur', $achat)) {
                    $reorderedAchat['Nom acheteur'] = $achat['nom_utilisateur'];
                }
                if (array_key_exists('code_formation', $achat)) {
                    $reorderedAchat['Code Formation'] = $achat['code_formation'];
                }
                if (array_key_exists('libelle_formation', $achat)) {
                    $reorderedAchat['Libellé formation'] = $achat['libelle_formation'];
                }
                if (array_key_exists('num_siret', $achat)) {
                    $reorderedAchat['N° SIRET'] = $achat['num_siret'];
                }
                if (array_key_exists('nom_fournisseur', $achat)) {
                    $reorderedAchat['Nom fournisseur'] = $achat['nom_fournisseur'];
                }
                if (array_key_exists('ville', $achat)) {
                    $reorderedAchat['Ville fournisseur'] = $achat['ville'];
                }
                if (array_key_exists('code_postal', $achat)) {
                    $reorderedAchat['CP'] = $achat['code_postal'];
                }
                if (array_key_exists('pme', $achat)) {
                    $reorderedAchat['PME ?'] = $achat['pme'];
                }
                if (array_key_exists('code_client', $achat)) {
                    $reorderedAchat['Code client'] = $achat['code_client'];
                }
                if (array_key_exists('num_chorus_fournisseur', $achat)) {
                    $reorderedAchat['N° CHORUS'] = $achat['num_chorus_fournisseur'];
                }
                if (array_key_exists('tel', $achat)) {
                    $reorderedAchat['N° Tel fournisseur'] = $achat['tel'];
                }
                if (array_key_exists('FAX', $achat)) {
                    $reorderedAchat['N° fax fournisseur'] = $achat['FAX'];
                }
                if (array_key_exists('mail', $achat)) {
                    $reorderedAchat['Adresse mail'] = $achat['mail'];
                }
                if (array_key_exists('code_uo', $achat)) {
                    $reorderedAchat['Code UO'] = $achat['code_uo'];
                }
                if (array_key_exists('libelle_uo', $achat)) {
                    $reorderedAchat['Libellé UO'] = $achat['libelle_uo'];
                }
                if (array_key_exists('code_cpv', $achat)) {
                    $reorderedAchat['Code CPV'] = $achat['code_cpv'];
                }
                if (array_key_exists('libelle_cpv', $achat)) {
                    $reorderedAchat['Libellé CPV'] = $achat['libelle_cpv'];
                }
                if (array_key_exists('id_demande_achat', $achat)) {
                    $reorderedAchat['ID Demande achat'] = $achat['id_demande_achat'];
                }
                if (array_key_exists('date_sillage', $achat)) {
                    $reorderedAchat['Date sillage'] = $achat['date_sillage'];
                }
                if (array_key_exists('date_commande_chorus', $achat)) {
                    $reorderedAchat['Date commande CHORUS'] = $achat['date_commande_chorus'];
                }
                if (array_key_exists('date_valid_inter', $achat)) {
                    $reorderedAchat['Date RUO'] = $achat['date_valid_inter'];
                }
                if (array_key_exists('date_validation', $achat)) {
                    $reorderedAchat['Date validation'] = $achat['date_validation'];
                }
                if (array_key_exists('date_notification', $achat)) {
                    $reorderedAchat['Date notification'] = $achat['date_notification'];
                }
                if (array_key_exists('date_annulation', $achat)) {
                    $reorderedAchat['Date annulation'] = $achat['date_annulation'];
                }
                if (array_key_exists('numero_ej', $achat)) {
                    $reorderedAchat['N° EJ'] = $achat['numero_ej'];
                }
                if (array_key_exists('objet_achat', $achat)) {
                    $reorderedAchat['Objet de l achat'] = $achat['objet_achat'];
                }
                if (array_key_exists('type_marche', $achat)) {
                    $reorderedAchat['Type de marché'] = $achat['type_marche'];
                }
                if (array_key_exists('montant_ht', $achat)) {
                    $reorderedAchat['Montant HT'] = $achat['montant_ht'];
                }
                if (array_key_exists('tva_taux', $achat)) {
                    $reorderedAchat['TVA'] = $achat['tva_taux'];
                }
                if (array_key_exists('montant_ttc', $achat)) {
                    $reorderedAchat['Montant TTC'] = $achat['montant_ttc'];
                }
                if (array_key_exists('observations', $achat)) {
                    $reorderedAchat['Observations'] = $achat['observations'];
                }
                if (array_key_exists('etat_achat', $achat)) {
                    $reorderedAchat['Etat de l achat'] = $achat['etat_achat'];
                }
                if (array_key_exists('place', $achat)) {
                    $reorderedAchat['Marché avec pub'] = $achat['place'];
                }
                if (array_key_exists('devis', $achat)) {
                    $reorderedAchat['Devis'] = $achat['devis'];
                }
                

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
    $cell = $column . '2'; // Construisez la référence de cellule pour les titres, ex: B1, C1, etc.
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
