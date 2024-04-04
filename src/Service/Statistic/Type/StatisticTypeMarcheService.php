<?php 

namespace App\Service\Statistic\Type;


use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticTypeMarcheService  extends AbstractController
{
    public function generateExcelFile($result_achats, $result_achats_mounts, $parameters, $projectDir)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach (range('A', 'T') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(15); // Définir la largeur à 15 pour chaque colonne
        }
        $sheet->setCellValue('H1', 'Statistiques MPPA/MABC')
        ->getStyle('H1')
        ->getFont()
        ->setBold(true)
        ->setSize(18)
        ->setColor(new Color(Color::COLOR_RED));
        $cellRangesByColor = [
            'c0504d' => [ // red
                'H3', 'H4', 'M3', 'M4', 'R3', 'R4'
            ],
            '4f81bd' => [ //bleu
                'G3', 'G4', 'L3', 'L4', 'Q3', 'Q4'
            ],
            '9bbb59' => [ // vert
                'I3', 'I4', 'N3', 'N4', 'S3', 'S4'
            ],
            '8064a2' => [ // violet
                'J3', 'J4', 'O3', 'O4', 'T3', 'T4'
            ],
            '4bacc6' => [ // bleu cyan
    
            ],
            // Ajoutez ici d'autres plages de cellules par couleur
        ];
    
        foreach ($cellRangesByColor as $color => $cellRanges) {
            $style = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]]];
            foreach ($cellRanges as $cellRange) {
                $sheet->getStyle($cellRange)->applyFromArray($style);
            }
        }
    
        $cellBorder = [
            'A2:D7','H2:I2','M2:N2','R2:S2','G3:J4','L3:O4','Q3:T4'
        ];
    
        $styleBorderB = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Style de bordure pour toutes les bordures
                ],
            ],
        ];
    
        foreach ($cellBorder as $cellRange) {
            $sheet->getStyle($cellRange)->applyFromArray($styleBorderB);
        }

        //------------------------------ Tab % vol/val_type --------------------------//
            $sheet->setCellValue('B2', "MPPA"); 
            $sheet->setCellValue('C2', "MABC"); 
            $sheet->setCellValue('D2', "TOTAUX");
            $sheet->setCellValue('A3', "VALEUR"); 
            $sheet->setCellValue('A4', "NOMBRE"); 
            $sheet->setCellValue('A5', "MOYENNE"); 
            $sheet->setCellValue('A6', "% VALEUR");
            $sheet->setCellValue('A7', "% VOLUME"); 
            $sheet->setCellValue('B3', $result_achats[0]["somme_montant_type_1"]); 
            $sheet->setCellValue('C3', $result_achats[1]["somme_montant_type_0"]); 
            $sheet->setCellValue('D3', $result_achats[1]["somme_montant_type_0"] + $result_achats[0]["somme_montant_type_1"]); 
            $sheet->setCellValue('B4', $result_achats[0]["nombre_achats_type_1"]); 
            $sheet->setCellValue('C4', $result_achats[1]["nombre_achats_type_0"]); 
            $sheet->setCellValue('D4', $result_achats[1]["nombre_achats_type_0"] +  $result_achats[0]["nombre_achats_type_1"]); 
            $sheet->setCellValue('B5', $result_achats[0]["moyenne_montant_type_1"]); 
            $sheet->setCellValue('C5', $result_achats[1]["moyenne_montant_type_0"]); 
            $sheet->setCellValue('D5', $result_achats[1]["moyenne_montant_type_0"] +  $result_achats[0]["moyenne_montant_type_1"]); 
            $sheet->setCellValue('B6', $result_achats[0]["pourcentage_type_1_total"]); 
            $sheet->setCellValue('C6', $result_achats[1]["pourcentage_type_0_total"]); 
            $sheet->setCellValue('B7', $result_achats[0]["pourcentage_type_1"]); 
            $sheet->setCellValue('C7', $result_achats[1]["pourcentage_type_0"]);


        //------------------------------ Tab montant_type --------------------------//
            $sheet->setCellValue('H2', "Montant des MPPA"); 
            $sheet->setCellValue('G3', "X <= ". $parameters['parameter2']); 
            $sheet->setCellValue('H3', $parameters['parameter2']." < X <=".$parameters['parameter3']); 
            $sheet->setCellValue('I3',  $parameters['parameter3']." < X <=".$parameters['parameter4']); 
            $sheet->setCellValue('J3', "X > ". $parameters['parameter4']); 
            $sheet->setCellValue('G4', $result_achats_mounts[0]["nombre_achats_inf_four1"]); 
            $sheet->setCellValue('H4', $result_achats_mounts[0]["nombre_achats_four1_four2"]); 
            $sheet->setCellValue('I4',  $result_achats_mounts[0]["nombre_achats_four2_four3"]); 
            $sheet->setCellValue('J4', $result_achats_mounts[0]["nombre_achats_sup_four3"]); 

            $sheet->setCellValue('M2', "Montant des MABC"); 
            $sheet->setCellValue('L3', "X <= ". $parameters['parameter2']); 
            $sheet->setCellValue('M3', $parameters['parameter2']." < X <=".$parameters['parameter3']); 
            $sheet->setCellValue('N3',  $parameters['parameter3']." < X <=".$parameters['parameter4']); 
            $sheet->setCellValue('O3', "X > ". $parameters['parameter4']); 
            $sheet->setCellValue('L4', $result_achats_mounts[1]["nombre_achats_inf_four1"]); 
            $sheet->setCellValue('M4', $result_achats_mounts[1]["nombre_achats_four1_four2"]); 
            $sheet->setCellValue('N4',  $result_achats_mounts[1]["nombre_achats_four2_four3"]); 
            $sheet->setCellValue('O4', $result_achats_mounts[1]["nombre_achats_sup_four3"]); 

            $sheet->setCellValue('R2', "Montant des MABC + MPPA"); 
            $sheet->setCellValue('Q3', "X <= ". $parameters['parameter2']); 
            $sheet->setCellValue('R3',$parameters['parameter2']." < X <=".$parameters['parameter3']); 
            $sheet->setCellValue('S3',  $parameters['parameter3']." < X <=".$parameters['parameter4']); 
            $sheet->setCellValue('T3', "X > ". $parameters['parameter4']); 
            $sheet->setCellValue('Q4', $result_achats_mounts[0]["nombre_achats_inf_four1"] + $result_achats_mounts[1]["nombre_achats_inf_four1"]); 
            $sheet->setCellValue('R4', $result_achats_mounts[0]["nombre_achats_four1_four2"] + $result_achats_mounts[1]["nombre_achats_four1_four2"]); 
            $sheet->setCellValue('S4',  $result_achats_mounts[0]["nombre_achats_four2_four3"] + $result_achats_mounts[1]["nombre_achats_four2_four3"]); 
            $sheet->setCellValue('T4', $result_achats_mounts[0]["nombre_achats_sup_four3"] + $result_achats_mounts[1]["nombre_achats_sup_four3"]);


        //------------------------------ chart montant_type --------------------------//

            $mppaLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$2', null, 12), 

            ];
            
            $mppaValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$G$4:$J$4', null, 4),
            ];

            $mppaxAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$3:$J$3', null, 4), // 'Valeurs'
            ];
            $mppaSeries = new DataSeries(
                DataSeries::TYPE_PIECHART, // plotType
                null, // plotGrouping (Pie charts don't have any grouping)
                range(0, count($mppaValues) - 1), // plotOrder
                $mppaLabels, // plotLabel
                $mppaxAx, // plotCategory
                $mppaValues          // plotValues
            );
            $layout = new Layout();
                $layout->setShowVal(true);
            $mppaplotArea = new PlotArea($layout, [$mppaSeries]);
            $mppaLegend = new Legend(Legend::POSITION_RIGHT, null, false);
            $mppaTitle = new Title('Montant des MPPA');
                    
                    $mppaChart = new Chart(
                        'mppaChart',
                        $mppaTitle,
                        $mppaLegend,
                        $mppaplotArea
                    );
                    
                $mppaChart->setTopLeftPosition('G5');
                $mppaChart->setBottomRightPosition('K16');
                $sheet->addChart($mppaChart);

        //------------------------------ chart montant_type --------------------------//

            $mabcLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$2', null, 12), 

            ];
            
            $mabcValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$L$4:$O$4', null, 4),
            ];

            $mabcxAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$3:$O$3', null, 4), // 'Valeurs'
            ];
            $mabcSeries = new DataSeries(
                DataSeries::TYPE_PIECHART, // plotType
                null, // plotGrouping (Pie charts don't have any grouping)
                range(0, count($mabcValues) - 1), // plotOrder
                $mabcLabels, // plotLabel
                $mabcxAx, // plotCategory
                $mabcValues          // plotValues
            );
            $layout = new Layout();
                $layout->setShowVal(true);
            $mabcplotArea = new PlotArea($layout, [$mabcSeries]);
            $mabcLegend = new Legend(Legend::POSITION_RIGHT, null, false);
            $mabcTitle = new Title('Montant des MABC');
                    
                    $mabcChart = new Chart(
                        'mabcChart',
                        $mabcTitle,
                        $mabcLegend,
                        $mabcplotArea
                    );
                    
                $mabcChart->setTopLeftPosition('L5');
                $mabcChart->setBottomRightPosition('P16');
                $sheet->addChart($mabcChart);

        //------------------------------ chart total_type --------------------------//
            $totaltypeLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$R$2', null, 12), 

            ];
            
            $totaltypeValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$Q$4:$T$4', null, 4),
            ];

            $totaltypexAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$Q$3:$T$3', null, 4), // 'Valeurs'
            ];
            $totaltypeSeries = new DataSeries(
                DataSeries::TYPE_PIECHART, // plotType
                null, // plotGrouping (Pie charts don't have any grouping)
                range(0, count($totaltypeValues) - 1), // plotOrder
                $totaltypeLabels, // plotLabel
                $totaltypexAx, // plotCategory
                $totaltypeValues          // plotValues
            );
            $layout = new Layout();
                $layout->setShowVal(true);
            $totaltypeplotArea = new PlotArea($layout, [$totaltypeSeries]);
            $totaltypeLegend = new Legend(Legend::POSITION_RIGHT, null, false);
            $totaltypeTitle = new Title('Montant des MABC + MPPA');
                    
                    $totaltypeChart = new Chart(
                        'totaltypeChart',
                        $totaltypeTitle,
                        $totaltypeLegend,
                        $totaltypeplotArea
                    );
                    
                $totaltypeChart->setTopLeftPosition('Q5');
                $totaltypeChart->setBottomRightPosition('U16');
                $sheet->addChart($totaltypeChart);
        $filePath = $projectDir . '/public/nom_de_fichier.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($filePath);
        
        return $filePath; // ou retournez un objet BinaryFileResponse si vous le souhaitez
    }

}