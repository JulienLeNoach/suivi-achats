<?php

namespace App\Service\Statistic;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CreateExcelDataExtract
{
    private $projectDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDir = $kernel->getProjectDir();
    }

    public function createExcelFile(array $achats, string $selectedYear): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1; // Ligne de départ pour le premier tableau

        // Style pour le titre
        $style = $sheet->getStyle('B1:E1');
        $font = $style->getFont();
        $font->setBold(true);
        $font->setSize(14);

        $sheet->setCellValue('A1', 'Année sélectionnée : ' . $selectedYear);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->mergeCells('B1:G1');
        $sheet->setCellValue('B1', "EXTRACTION DES DONNEES BASE SUIVI DES ACHATS POUR L'ANNEE ");

        if (!empty($achats)) {
            $column = 'B'; // Colonne de départ pour les titres
            $row = 3; // Ligne de départ pour les données

            foreach ($achats as $achat) {
                $column = 'B'; // Réinitialiser la colonne pour chaque nouveau tableau achat

                // Formatage des données
                if (isset($achat["devis"])) {
                    $achat["devis"] = $achat["devis"] == 0 ? "Prescripteur" : "GSBdD/PFAF";
                }
                if (isset($achat["type_marche"])) {
                    $achat["type_marche"] = $achat["type_marche"] == 0 ? "MABC" : "MPPA";
                }
                if (isset($achat["etat_achat"])) {
                    $achat["etat_achat"] = $achat["etat_achat"] == 0 ? "En cours" : ($achat["etat_achat"] == 1 ? "Annulé" : "Validé");
                }
                if (isset($achat["place"])) {
                    $achat["place"] = $achat["place"] == 0 ? "Non" : "Oui";
                }
                foreach ($achat as $key => $value) {
                    if (strpos($key, "date_") === 0 && $achat[$key] !== null && $achat[$key] instanceof \DateTime) {
                        $achat[$key] = $achat[$key]->format('d/m/Y');
                    }
                }

                // Réorganisation des données
                $reorderedAchat = $this->reorderAchatData($achat);

                // Ajout des données dans le fichier Excel
                foreach ($reorderedAchat as $key => $value) {
                    $cell = $column . $row;

                    if ($key === 'N° SIRET') {
                        $cellObject = $sheet->getCell($cell);
                        $cellObject->setValueExplicit($value, DataType::TYPE_STRING);
                        $style = $cellObject->getStyle();
                        $numberFormat = $style->getNumberFormat();
                        $numberFormat->setFormatCode('0');
                    } else {
                        $sheet->setCellValue($cell, $value);
                    }

                    $column++;
                }

                $row++;
            }

            // Ajustement des colonnes
            $column = 'B';
            $highestColumn = $sheet->getHighestColumn();
            while ($column != $highestColumn) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
                $column++;
            }
            $sheet->getColumnDimension($highestColumn)->setAutoSize(true);
            $sheet->calculateColumnWidths();
        }

        // Enregistrement du fichier
        $filePath = $this->projectDir . '/public/nom_de_fichier.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($filePath);

        return $filePath;
    }

    private function reorderAchatData(array $achat): array
    {
        $reorderedAchat = [];

        $fields = [
            'numero_achat' => 'N° CHRONO',
            'code_service' => 'Code service',
            'nom_service' => 'Nom service',
            'trigram' => 'Code acheteur',
            'nom_utilisateur' => 'Nom acheteur',
            'code_formation' => 'Code Formation',
            'libelle_formation' => 'Libellé formation',
            'num_siret' => 'N° SIRET',
            'nom_fournisseur' => 'Nom fournisseur',
            'ville' => 'Ville fournisseur',
            'code_postal' => 'CP',
            'pme' => 'PME ?',
            'code_client' => 'Code client',
            'num_chorus_fournisseur' => 'N° CHORUS',
            'tel' => 'N° Tel fournisseur',
            'FAX' => 'N° fax fournisseur',
            'mail' => 'Adresse mail',
            'code_uo' => 'Code UO',
            'libelle_uo' => 'Libellé UO',
            'code_cpv' => 'Code CPV',
            'libelle_cpv' => 'Libellé CPV',
            'libelle_gsbdd' => 'GSBDD / Grands compte',
            'id_demande_achat' => 'ID Demande achat',
            'date_sillage' => 'Date sillage',
            'date_commande_chorus' => 'Date commande CHORUS',
            'date_valid_inter' => 'Date RUO',
            'date_validation' => 'Date validation',
            'date_notification' => 'Date notification',
            'date_annulation' => 'Date annulation',
            'numero_ej' => 'N° EJ',
            'objet_achat' => 'Objet de l achat',
            'type_marche' => 'Type de marché',
            'montant_ht' => 'Montant HT',
            'tva_taux' => 'TVA',
            'montant_ttc' => 'Montant TTC',
            'observations' => 'Observations',
            'etat_achat' => 'Etat de l achat',
            'place' => 'Marché avec pub',
            'devis' => 'Devis',
        ];

        foreach ($fields as $field => $label) {
            if (array_key_exists($field, $achat)) {
                $reorderedAchat[$label] = $achat[$field];
            }
        }

        return $reorderedAchat;
    }
}