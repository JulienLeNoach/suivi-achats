<?php 

namespace App\Service;


use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticTypeMarcheService  extends AbstractController
{
    public function generateExcelFile($result_achats, $result_achats_mounts, $parameter, $projectDir)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //------------------------------ Tab % vol/val_type --------------------------//
            $sheet->setCellValue('B2', "MPPA"); 
            $sheet->setCellValue('C2', "MABC"); 
            $sheet->setCellValue('D2', "TOTAUX");
            $sheet->setCellValue('A3', "VALEUR"); 
            $sheet->setCellValue('A4', "NOMBRE"); 
            $sheet->setCellValue('A5', "MOYENNE"); 
            $sheet->setCellValue('A6', "% VALEUR");
            $sheet->setCellValue('A6', "% VOLUME"); 
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
            $sheet->setCellValue('G3', "X <= ". $parameter[0]->getFour2()); 
            $sheet->setCellValue('H3', $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); 
            $sheet->setCellValue('I3',  $parameter[0]->getFour3()." < X <=".$parameter[0]->getFour4()); 
            $sheet->setCellValue('J3', "X > ". $parameter[0]->getFour4()); 
            $sheet->setCellValue('G4', $result_achats_mounts[0]["nombre_achats_inf_four1"]); 
            $sheet->setCellValue('H4', $result_achats_mounts[0]["nombre_achats_four1_four2"]); 
            $sheet->setCellValue('I4',  $result_achats_mounts[0]["nombre_achats_four2_four3"]); 
            $sheet->setCellValue('J4', $result_achats_mounts[0]["nombre_achats_sup_four3"]); 

            $sheet->setCellValue('M2', "Montant des MABC"); 
            $sheet->setCellValue('L3', "X <= ". $parameter[0]->getFour2()); 
            $sheet->setCellValue('M3', $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); 
            $sheet->setCellValue('N3',  $parameter[0]->getFour3()." < X <=".$parameter[0]->getFour4()); 
            $sheet->setCellValue('O3', "X > ". $parameter[0]->getFour4()); 
            $sheet->setCellValue('L4', $result_achats_mounts[1]["nombre_achats_inf_four1"]); 
            $sheet->setCellValue('M4', $result_achats_mounts[1]["nombre_achats_four1_four2"]); 
            $sheet->setCellValue('N4',  $result_achats_mounts[1]["nombre_achats_four2_four3"]); 
            $sheet->setCellValue('O4', $result_achats_mounts[1]["nombre_achats_sup_four3"]); 

            $sheet->setCellValue('R2', "Montant des MABC + MPPA"); 
            $sheet->setCellValue('Q3', "X <= ". $parameter[0]->getFour2()); 
            $sheet->setCellValue('R3', $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); 
            $sheet->setCellValue('S3',  $parameter[0]->getFour3()." < X <=".$parameter[0]->getFour4()); 
            $sheet->setCellValue('T3', "X > ". $parameter[0]->getFour4()); 
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
            $mppaplotArea = new PlotArea(null, [$mppaSeries]);
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
            $mabcplotArea = new PlotArea(null, [$mabcSeries]);
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
            $totaltypeplotArea = new PlotArea(null, [$totaltypeSeries]);
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