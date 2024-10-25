<?php 

namespace App\Service\Statistic\StatisticDelay;

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
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateExcelDelay  extends AbstractController
{
    private $projectDir;

    public function __construct(KernelInterface $kernel, private RequestStack $requestStack)
    {
        $this->projectDir = $kernel->getProjectDir();
    }

    public function createExcelFile($achats, $achats_delay_all,array $delais)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $session = $this->requestStack->getSession()->get('toPDF');

        $sheet->setCellValue('H1', "Délai d'activité annuelle "  . $session['criteria']['Date'])
            ->getStyle('H1')
            ->getFont()
            ->setBold(true)
            ->setSize(18)
            ->setColor(new Color(Color::COLOR_RED));
        $sheet->getColumnDimension('R')->setWidth(30);

        //-------------------------- Appro volume chart -------------------------------- // 
        $mois = ['Délai', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre', 'TOTAL'];
        $col = 2; 
        $colmonth = 'B';
        foreach (range('A', 'Q') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(15); // Définir la largeur à 15 pour chaque colonne
        }
        for ($j = 0; $j <= 13; $j++) {
            $sheet->setCellValue($colmonth . 2, $mois[$j]);
            $colmonth++;
        }

        $cellRangesByColor = [
            'c0504d' => [ // red
                'C10','C9', 'I9','I10', 'O9','O10', 'C26','C27'
            ],
            '4f81bd' => [ // bleu
                'B10','B9', 'H9','H10', 'N9','N10', 'B26','B27'
            ],
            '9bbb59' => [ // vert
                // Ajoutez les cellules correspondantes
            ],
            '8064a2' => [ // violet
                // Ajoutez les cellules correspondantes
            ],
            '4bacc6' => [ // bleu cyan
                // Ajoutez les cellules correspondantes
            ],
        ];

        foreach ($cellRangesByColor as $color => $cellRanges) {
            $style = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]]];
            foreach ($cellRanges as $cellRange) {
                $sheet->getStyle($cellRange)->applyFromArray($style);
            }
        }

        $cellBorder = [
            'B2:O6','A10','G10','M10','A27','B9:C10','H9:I10','N9:O10','N25:O26','B26:C27'
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

        for ($i = 0; $i < 4; $i++) {
            $sheet->setCellValue('B' . ($col + 1), $achats[$i]["source"]); 
            $sheet->setCellValue('C' . ($col + 1), $achats[$i]["Janvier"]); 
            $sheet->setCellValue('D' . ($col + 1), $achats[$i]["Février"]); 
            $sheet->setCellValue('E' . ($col + 1), $achats[$i]["Mars"]); 
            $sheet->setCellValue('F' . ($col + 1), $achats[$i]["Avril"]); 
            $sheet->setCellValue('G' . ($col + 1), $achats[$i]["Mai"]); 
            $sheet->setCellValue('H' . ($col + 1), $achats[$i]["Juin"]); 
            $sheet->setCellValue('I' . ($col + 1), $achats[$i]["Juillet"]); 
            $sheet->setCellValue('J' . ($col + 1), $achats[$i]["Aout"]); 
            $sheet->setCellValue('K' . ($col + 1), $achats[$i]["Septembre"]); 
            $sheet->setCellValue('L' . ($col + 1), $achats[$i]["Octobre"]); 
            $sheet->setCellValue('M' . ($col + 1), $achats[$i]["Novembre"]); 
            $sheet->setCellValue('N' . ($col + 1), $achats[$i]["Decembre"]);
            $sheet->setCellValue('O' . ($col + 1), ($achats[$i]["Janvier"] +  $achats[$i]["Février"] + $achats[$i]["Mars"] + $achats[$i]["Avril"] + $achats[$i]["Mai"] + $achats[$i]["Juin"] + $achats[$i]["Juillet"] + $achats[$i]["Aout"] + $achats[$i]["Septembre"] + $achats[$i]["Octobre"] + $achats[$i]["Novembre"] + $achats[$i]["Decembre"])/12);
            $col++;
        }   
//------------------------- Affichage des délais dans Excel -------------------------- //
$sheet->setCellValue('R2', 'Délais');
$sheet->setCellValue('R3', 'Délai de transmission (en jours) :');
$sheet->setCellValue('S3', $delais[0]);

$sheet->setCellValue('R4', 'Délai de traitement (en jours) :');
$sheet->setCellValue('S4', $delais[1]);

$sheet->setCellValue('R5', 'Délai de notification (en jours) :');
$sheet->setCellValue('S5', $delais[2]);

$sheet->setCellValue('R6', 'Délai total (en jours) :');
$sheet->setCellValue('S6', $delais[3]);
//------------------------- Fin Affichage des délais --------------------------------- //
        //-------------------------- Transmission chart -------------------------------- // 
        $sheet->setCellValue('A10', 'Transmission');
        $sheet->setCellValue('B9', '<= 3j / ' .$achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"]. "%");
        $sheet->setCellValue('C9', '> 3j / '.$achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"] . "%");  
        $sheet->setCellValue('B10', $achats_delay_all[0]["CountAntInf3"]); 
        $sheet->setCellValue('C10', $achats_delay_all[0]["CountAntSup3"]); 

        $antLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$9', null, 12), 
        ];

        $antValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$10:$C$10', null, 2),
        ];

        $antxAx = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$9:$C$9', null, 4), // 'Valeurs'
        ];
        $antSeries = new DataSeries(
            DataSeries::TYPE_PIECHART, // plotType
            null, // plotGrouping (Pie charts don't have any grouping)
            range(0, count($antValues) - 1), // plotOrder
            $antLabels, // plotLabel
            $antxAx, // plotCategory
            $antValues          // plotValues
        );
        $layout = new Layout();
        $layout->setShowVal(true);
        $antplotArea = new PlotArea($layout, [$antSeries]);
        $antLegend = new Legend(Legend::POSITION_RIGHT, null, false);
        $antTitle = new Title('Transmission');
                
        $antChart = new Chart(
            'antChart',
            $antTitle,
            $antLegend,
            $antplotArea
        );
                
        $antChart->setTopLeftPosition('B11');
        $antChart->setBottomRightPosition('F24');
        $sheet->addChart($antChart);

        //-------------------------- Traitement chart -------------------------------- // 
        $sheet->setCellValue('G10', 'Traitement');
        $sheet->setCellValue('H9', '<= 3j / ' . $achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"] . "%");
        $sheet->setCellValue('I9', '> 3j / ' . $achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"] . "%");
        $sheet->setCellValue('H10', $achats_delay_all[1]["CountBudgetInf3"]);
        $sheet->setCellValue('I10', $achats_delay_all[1]["CountBudgetSup3"]);
        
        $budgetLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$9', null, 12),
        ];
        
        $budgetValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$10:$I$10', null, 2),
        ];
        
        $budgetxAx = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$9:$I$9', null, 4), // 'Valeurs'
        ];
        $budgetSeries = new DataSeries(
            DataSeries::TYPE_PIECHART, // plotType
            null, // plotGrouping (Pie charts don't have any grouping)
            range(0, count($budgetValues) - 1), // plotOrder
            $budgetLabels, // plotLabel
            $budgetxAx, // plotCategory
            $budgetValues          // plotValues
        );
        $layout = new Layout();
        $layout->setShowVal(true);
        $budgetplotArea = new PlotArea($layout, [$budgetSeries]);
        $budgetLegend = new Legend(Legend::POSITION_RIGHT, null, false);
        $budgetTitle = new Title('Traitement');
        
        $budgetChart = new Chart(
            'budgetChart',
            $budgetTitle,
            $budgetLegend,
            $budgetplotArea
        );
        
        $budgetChart->setTopLeftPosition('H11');
        $budgetChart->setBottomRightPosition('L24');
        $sheet->addChart($budgetChart);

        //-------------------------- Notification chart -------------------------------- // 
        $sheet->setCellValue('M10', 'Notification');
        $sheet->setCellValue('N9', '<= 7j / ' .$achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"]. "%");
        $sheet->setCellValue('O9', '> 7j / '.$achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"]. "%");  
        $sheet->setCellValue('N10', $achats_delay_all[2]["CountApproInf7"]); 
        $sheet->setCellValue('O10', $achats_delay_all[2]["CountApproSup7"]); 
        
        $approLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$9', null, 12), 
        ];
        
        $approValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$10:$O$10', null, 2),
        ];
        
        $approxAx = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$9:$O$9', null, 4), // 'Valeurs'
        ];
        $approSeries = new DataSeries(
            DataSeries::TYPE_PIECHART, // plotType
            null, // plotGrouping (Pie charts don't have any grouping)
            range(0, count($approValues) - 1), // plotOrder
            $approLabels, // plotLabel
            $approxAx, // plotCategory
            $approValues          // plotValues
        );
        $layout = new Layout();
        $layout->setShowVal(true);
        $approplotArea = new PlotArea($layout, [$approSeries]);
        $approLegend = new Legend(Legend::POSITION_RIGHT, null, false);
        $approTitle = new Title('Notification');
        
        $approChart = new Chart(
            'approChart',
            $approTitle,
            $approLegend,
            $approplotArea
        );
        
        $approChart->setTopLeftPosition('N11');
        $approChart->setBottomRightPosition('R24');
        $sheet->addChart($approChart);

        //-------------------------- Total chart -------------------------------- // 
       //-------------------------- Total chart -------------------------------- // 
       $sheet->setCellValue('A27', 'Délai total');
       $sheet->setCellValue('B26', '<= 15j / ' .$achats_delay_all[3]["Pourcentage_Delai_Inf_15_Jours"]. "%");
       $sheet->setCellValue('C26', '> à 15j / '.$achats_delay_all[3]["Pourcentage_Delai_Sup_15_Jours"]. "%");  
       $sheet->setCellValue('B27', $achats_delay_all[3]["CountDelaiTotalInf15"]); 
       $sheet->setCellValue('C27', $achats_delay_all[3]["CountDelaiTotalSup15"]); 
       
       $totalLabels = [
           new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$26', null, 12), 
       ];
       
       $totalValues = [
           new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$27:$C$27', null, 2),
       ];
       
       $totalxAx = [
           new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$26:$C$26', null, 4), // 'Valeurs'
       ];
       $totalSeries = new DataSeries(
           DataSeries::TYPE_PIECHART, // plotType
           null, // plotGrouping (Pie charts don't have any grouping)
           range(0, count($totalValues) - 1), // plotOrder
           $totalLabels, // plotLabel
           $totalxAx, // plotCategory,
           $totalValues // plotValues
       );
       $layout = new Layout();
       $layout->setShowVal(true);
       $totalplotArea = new PlotArea($layout, [$totalSeries]);
       $totalLegend = new Legend(Legend::POSITION_RIGHT, null, false);
       $totalTitle = new Title('Délai Total');
               
       $totalChart = new Chart(
           'totalChart',
           $totalTitle,
           $totalLegend,
           $totalplotArea
       );
               
       $totalChart->setTopLeftPosition('B28');
       $totalChart->setBottomRightPosition('F44');
       $sheet->addChart($totalChart);
        
        $filePath = $this->projectDir . '/public/nom_de_fichier.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($filePath);
        
        return $filePath;
    }
}
