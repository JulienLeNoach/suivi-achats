<?php 

namespace App\Service;


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
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticDelayService  extends AbstractController
{
    private $projectDir;

    public function __construct(KernelInterface $kernel)
    {

        $this->projectDir = $kernel->getProjectDir();

    }

    public function totalDelayPerMonth(array $achats): array
    {
        $transmission = [];
        $notification = [];
        $delaiTotal = [];
    
        $monthNames = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Aout',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Decembre',
        ];
    
        // Renommer les clés "Mois_x" en utilisant les noms des mois
        foreach ($achats as &$achat) {
            foreach ($monthNames as $monthNumber => $monthName) {
                $achat[$monthName] = $achat['Mois_' . $monthNumber] ?? 0;
                unset($achat['Mois_' . $monthNumber]);
            }
        }
        unset($achat); // Délier la dernière référence pour éviter des effets de bord
    
        // Calcul des sommes pour Transmission et Notification
        foreach ($monthNames as $monthName) {
            $sumTransmission = 0;
            $sumNotification = 0;
    
            foreach ($achats as $achat) {
                if ($achat['source'] === 'ANT GSBDD' || $achat['source'] === 'BUDGET') {
                    $sumTransmission += $achat[$monthName];
                } elseif ($achat['source'] === 'APPRO' || $achat['source'] === 'FIN') {
                    $sumNotification += $achat[$monthName];
                }   
            }
    
            $transmission[$monthName] = $sumTransmission;
            $notification[$monthName] = $sumNotification;
        }
    
        // Calculer la ligne "Délai TOTAL"
        foreach ($monthNames as $monthName) {
            $total = $transmission[$monthName] + $notification[$monthName];
            $delaiTotal[$monthName] = number_format($total, 1);
        }
        $delaiTotal['source'] = 'Délai TOTAL';
        $transmission['source'] = 'Transmission';
        $notification['source'] = 'Notification';
    
        // Organiser les éléments dans l'ordre spécifié
        $orderedAchats = [];
        foreach (['ANT GSBDD', 'BUDGET', 'APPRO', 'FIN', 'PFAF', 'Chorus formul.'] as $source) {
            foreach ($achats as $achat) {
                if ($achat['source'] === $source) {
                    $orderedAchats[] = $achat;
                    break;
                }
            }
        }
    
        // Ajouter les éléments calculés aux positions spécifiées
        array_splice($orderedAchats, 2, 0, [$transmission]);
        array_splice($orderedAchats, 5, 0, [$notification]);
        $orderedAchats[] = $delaiTotal;
        
        return $orderedAchats;
    }
    
    public function createExcelFile($achats, $achats_delay_all)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('H1', "Délai d'activité annuelle")
    ->getStyle('H1')
    ->getFont()
    ->setBold(true)
    ->setSize(18)
    ->setColor(new Color(Color::COLOR_RED));
    //-------------------------- Appro volume chart -------------------------------- // 
    $mois = ['Délai','Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre','TOTAL'];
    $col = 2; 
    $colmonth='B';
    foreach (range('A', 'Q') as $columnID) {
        $sheet->getColumnDimension($columnID)->setWidth(15); // Définir la largeur à 15 pour chaque colonne
    }
    for ($j=0; $j<=13; $j++){
        $sheet->setCellValue($colmonth . 2, $mois[$j]);
        $colmonth++;
    }

    $cellRangesByColor = [
        'c0504d' => [ // red
             'B8:O8', 'C28','C27', 'I27','I28', 'O27','O28', 'C43','C44', 'I43','I44', 'O43','O44', 'C60','C61'
        ],
        '4f81bd' => [ // bleu
            'B5:O5', 'B28','B27', 'H27','H28', 'N27','N28', 'B43','B44', 'H43','H44', 'N43','N44', 'B60','B61'
        ],
        '9bbb59' => [ // vert
    
        ],
        '8064a2' => [ // violet
    
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
        'B2:O11', 'B23:O26','A28','G28','M28','A44','G44','M44','A61','B27:C28','H27:I28','N27:O28','B43:C44','H43:I44','N43:N44','B60:C61'
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


    for ($i=0; $i < 9; $i++) {
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

    $notTransLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$5', null, 12), 
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$8', null, 12), // Mois
    ];

    $notTransxAx = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$2:$N$2', null, 12), // 'Valeurs'
    ];
    $notTransValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$5:$N$5', null, 12), // Valeurs pour datasets1
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$8:$N$8', null, 12), // Valeurs pour datasets2
    ];
    $notTransSeries = new DataSeries(
        DataSeries::TYPE_BARCHART,
        DataSeries::GROUPING_CLUSTERED,
        range(0, count($notTransValues) - 1),
        $notTransLabels,
        $notTransxAx,
        $notTransValues
    );
    $layout = new Layout();
    $layout->setShowVal(true);
    $notTransplotArea = new PlotArea($layout, [$notTransSeries]);
    $notTransLegend = new Legend(Legend::POSITION_RIGHT, null, false);
    $notTransTitle = new Title("Délai d'activié annuelle");
                
    $approVolChart = new Chart(
        'approVolChart',
        $notTransTitle,
        $notTransLegend,
        $notTransplotArea
    );
                
    $approVolChart->setTopLeftPosition('B13');
    $approVolChart->setBottomRightPosition('P26');
    $sheet->addChart($approVolChart);


    //-------------------------- Ant GSBDD chart -------------------------------- // 
    $sheet->setCellValue('A' . 28, 'Ant. GSBDD');
    $sheet->setCellValue('B' . 27, '<= 3j / ' .$achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"]. "%");
    $sheet->setCellValue('C' . 27, '> 3j / '.$achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"] . "%");  
    $sheet->setCellValue('B' . 28, $achats_delay_all[0]["CountAntInf3"]); 
    $sheet->setCellValue('C' . 28, $achats_delay_all[0]["CountAntSup3"]); 

    $antLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$28', null, 12), 

    ];
    
    $antValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$28:$C$28', null, 2),
    ];

    $antxAx = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$27:$C$27', null, 4), // 'Valeurs'
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
    $antTitle = new Title('ANT GSBDD');
            
    $antChart = new Chart(
        'antChart',
        $antTitle,
        $antLegend,
        $antplotArea
    );
            
    $antChart->setTopLeftPosition('B29');
    $antChart->setBottomRightPosition('F42');
    $sheet->addChart($antChart);



        


    //-------------------------- Budget chart -------------------------------- // 

    $sheet->setCellValue('G' . 28, 'Budget');
    $sheet->setCellValue('H' . 27, '<= 3j / ' . $achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"] . "%");
    $sheet->setCellValue('I' . 27, '> 3j / ' . $achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"] . "%");
    $sheet->setCellValue('H' . 28, $achats_delay_all[1]["CountBudgetInf3"]);
    $sheet->setCellValue('I' . 28, $achats_delay_all[1]["CountBudgetSup3"]);
    
    $budgetLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$28', null, 12),
    ];
    
    $budgetValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$28:$I$28', null, 2),
    ];
    
    $budgetxAx = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$27:$I$27', null, 4), // 'Valeurs'
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
    $budgetTitle = new Title('Budget');
    
    $budgetChart = new Chart(
        'buggetChart',
        $budgetTitle,
        $budgetLegend,
        $budgetplotArea
    );
    
    $budgetChart->setTopLeftPosition('H29');
    $budgetChart->setBottomRightPosition('L42');
    $sheet->addChart($budgetChart);
    





                        
    //-------------------------- APPRO chart -------------------------------- // 

    $sheet->setCellValue('M' . 28, 'APPRO');
    $sheet->setCellValue('N' . 27, '<= 7j / ' .$achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"]. "%");
    $sheet->setCellValue('O' . 27, '> 7j / '.$achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"]. "%");  
    $sheet->setCellValue('N' . 28, $achats_delay_all[2]["CountApproInf7"]); 
    $sheet->setCellValue('O' . 28, $achats_delay_all[2]["CountApproSup7"]); 
    
    $approLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$28', null, 12), 
    ];
    
    $approValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$28:$O$28', null, 2),
    ];
    
    $approxAx = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$27:$O$27', null, 4), // 'Valeurs'
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
    $approTitle = new Title('Appro');
    
    $approChart = new Chart(
        'approChart',
        $approTitle,
        $approLegend,
        $approplotArea
    );
    
    $approChart->setTopLeftPosition('N29');
    $approChart->setBottomRightPosition('R42');
    $sheet->addChart($approChart);


    //-------------------------- Fin chart -------------------------------- // 


    $sheet->setCellValue('A' . 44, 'Fin');
    $sheet->setCellValue('B' . 43, '< 7j / ' .$achats_delay_all[3]["Pourcentage_Delai_Inf_7_Jours_Fin"] . "%");
    $sheet->setCellValue('C' . 43, '> 7j / '.$achats_delay_all[3]["Pourcentage_Delai_Sup_7_Jours_Fin"] . "%");  
    $sheet->setCellValue('B' . 44, $achats_delay_all[3]["CountFinInf7"]); 
    $sheet->setCellValue('C' . 44, $achats_delay_all[3]["CountFinSup7"]); 
    
    $finLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$43', null, 12), 
    ];
    
    $finValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$44:$C$44', null, 2),
    ];
    
    $finxAx = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$43:$C$43', null, 4), // 'Valeurs'
    ];
    $finSeries = new DataSeries(
        DataSeries::TYPE_PIECHART, // plotType
        null, // plotGrouping (Pie charts don't have any grouping)
        range(0, count($finValues) - 1), // plotOrder
        $finLabels, // plotLabel
        $finxAx, // plotCategory
        $finValues          // plotValues
    );
    $layout = new Layout();
    $layout->setShowVal(true);
    $finplotArea = new PlotArea($layout, [$finSeries]);
    $finLegend = new Legend(Legend::POSITION_RIGHT, null, false);
    $finTitle = new Title('Fin');
    
    $finChart = new Chart(
        'finChart',
        $finTitle,
        $finLegend,
        $finplotArea
    );
    
    $finChart->setTopLeftPosition('B45');
    $finChart->setBottomRightPosition('F59');
    $sheet->addChart($finChart);



                
    //-------------------------- Chorus formul. chart -------------------------------- // 


    

    $sheet->setCellValue('G' . 44, 'Chorus formul.');
    $sheet->setCellValue('H' . 43, '<= 10j / ' .$achats_delay_all[4]["Pourcentage_Delai_Inf_10_Jours_Chorus"] . "%");
    $sheet->setCellValue('I' . 43, '> à 10j / '.$achats_delay_all[4]["Pourcentage_Delai_Sup_10_Jours_Chorus"] . "%");  
    $sheet->setCellValue('H' . 44, $achats_delay_all[4]["CountChorusFormInf10"]); 
    $sheet->setCellValue('I' . 44, $achats_delay_all[4]["CountChorusFormSup10"]); 
    
    $chorLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$43', null, 12), 
    ];
    
    $chorValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$44:$I$44', null, 2),
    ];
    
    $chorxAx = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$43:$I$43', null, 4), // 'Valeurs'
    ];
    $chorSeries = new DataSeries(
        DataSeries::TYPE_PIECHART, // plotType
        null, // plotGrouping (Pie charts don't have any grouping)
        range(0, count($chorValues) - 1), // plotOrder
        $chorLabels, // plotLabel
        $chorxAx, // plotCategory
        $chorValues          // plotValues
    );
    $layout = new Layout();
    $layout->setShowVal(true);
    $chorplotArea = new PlotArea($layout, [$chorSeries]);
    $chorLegend = new Legend(Legend::POSITION_RIGHT, null, false);
    $chorTitle = new Title('Chorus formul.');
    
    $chorChart = new Chart(
        'chorChart',
        $chorTitle,
        $chorLegend,
        $chorplotArea
    );
    
    $chorChart->setTopLeftPosition('H45');
    $chorChart->setBottomRightPosition('L59');
    $sheet->addChart($chorChart);


                

        
    //-------------------------- PFAF chart -------------------------------- // 

    $sheet->setCellValue('M' . 44, 'PFAF');
    $sheet->setCellValue('N' . 43, '<= 14j / ' .$achats_delay_all[5]["Pourcentage_Delai_Inf_14_Jours_Pfaf"] . "%");
    $sheet->setCellValue('O' . 43, '> à 14j / '.$achats_delay_all[5]["Pourcentage_Delai_Sup_14_Jours_Pfaf"] . "%");  
    $sheet->setCellValue('N' . 44, $achats_delay_all[5]["CountPfafInf14"]); 
    $sheet->setCellValue('O' . 44, $achats_delay_all[5]["CountPfafSup14"]); 
    
    $pfafLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$44', null, 12), 
    ];
    
    $pfafValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$44:$O$44', null, 2),
    ];
    
    $pfafxAx = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$43:$O$43', null, 4), // 'Valeurs'
    ];
    $pfafSeries = new DataSeries(
        DataSeries::TYPE_PIECHART, // plotType
        null, // plotGrouping (Pie charts don't have any grouping)
        range(0, count($pfafValues) - 1), // plotOrder
        $pfafLabels, // plotLabel
        $pfafxAx, // plotCategory
        $pfafValues          // plotValues
    );
    $layout = new Layout();
    $layout->setShowVal(true);
    $pfafplotArea = new PlotArea($layout, [$pfafSeries]);
    $pfafLegend = new Legend(Legend::POSITION_RIGHT, null, false);
    $pfafTitle = new Title('PFAF');
            
    $pfafChart = new Chart(
        'pfafChart',
        $pfafTitle,
        $pfafLegend,
        $pfafplotArea
    );
            
    $pfafChart->setTopLeftPosition('N45');
    $pfafChart->setBottomRightPosition('R59');
    $sheet->addChart($pfafChart);
    


    //-------------------------- Total chart -------------------------------- // 

    $sheet->setCellValue('A' . 61, 'Délai total');
    $sheet->setCellValue('B' . 60, '<= 15j / ' .$achats_delay_all[6]["Pourcentage_Delai_Inf_15_Jours"]. "%");
    $sheet->setCellValue('C' . 60, '> à 15j / '.$achats_delay_all[6]["Pourcentage_Delai_Sup_15_Jours"]. "%");  
    $sheet->setCellValue('B' . 61, $achats_delay_all[6]["CountDelaiTotalInf15"]); 
    $sheet->setCellValue('C' . 61, $achats_delay_all[6]["CountDelaiTotalSup15"]); 
    
    $totalLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$60', null, 12), 
    ];
    
    $totalValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$61:$C$61', null, 2),
    ];
    
    $totalxAx = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$60:$C$60', null, 4), // 'Valeurs'
    ];
    $totalSeries = new DataSeries(
        DataSeries::TYPE_PIECHART, // plotType
        null, // plotGrouping (Pie charts don't have any grouping)
        range(0, count($totalValues) - 1), // plotOrder
        $totalLabels, // plotLabel
        $totalxAx, // plotCategory
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
            
    $totalChart->setTopLeftPosition('B62');
    $totalChart->setBottomRightPosition('F78');
    $sheet->addChart($totalChart);
    
    $filePath = $this->projectDir . '/public/nom_de_fichier.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->setIncludeCharts(true);
    $writer->save($filePath);
    
    return $filePath;
    }
    

}