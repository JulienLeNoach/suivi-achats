<?php namespace App\Service;

use Dompdf\Dompdf;
use App\Repository\AchatRepository;
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
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CRAnnuelService  extends AbstractController
{
    private $achatRepository;
    private $projectDir;

    private $chartBuilder;
    public function __construct(AchatRepository $achatRepository,ChartBuilderInterface $chartBuilder, KernelInterface $kernel)
    {
        $this->achatRepository = $achatRepository;
        $this->chartBuilder = $chartBuilder;
        $this->projectDir = $kernel->getProjectDir();

    }

    public function generateExcelFile($datasets1, $datasets2, $datasets3, $datasets4, $projectDir,$achats, $achats_delay_all,
                                    $result_achats, $result_achats_mounts, $parameter,$result_achatsPME, $result_achatsSum, $result_achatsSumVol, $result_achatsSumVal)
    {
        $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('H1', 'COMPTE RENDU ANNUEL')
      ->getStyle('H1')
      ->getFont()
      ->setBold(true)
      ->setSize(18);
        $col = 'C'; // Commencez à partir de la colonne C pour les données
        foreach (range('B', 'Q') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(15); // Définir la largeur à 15 pour chaque colonne
        }

        $sheet->setCellValue('H2', 'Activités appro en volume/valeur')
        ->getStyle('H2')
        ->getFont()
        ->setSize(16)
        ->setColor(new Color(Color::COLOR_RED));
        
        $cellRanges = ['B2:O2', 'B40:O40', 'B129:O129', 'B159:O159']; // Plages de cellules à traiter

        $styleBorder = [
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN], // Bordure supérieure
                'bottom' => ['borderStyle' => Border::BORDER_THIN], // Bordure inférieure
            ],
        ];
        $cellRangesB = [
            'B3:O6', 'B21:O24', 'B42:O51', 'B67:D68', 'G67:I68', 'L67:N68', 'B88:D89', 'G88:I89', 'L88:N89', 'B109:D110',
            'B131:E136', 'B139:E141', 'G139:I141', 'L139:O141', 'B160:E162', 'C166:H167', 'J166:O167', 'B185:O187'
        ];
        
        $styleBorderB = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Style de bordure pour toutes les bordures
                ],
            ],
        ];
        
        foreach ($cellRangesB as $cellRange) {
            $sheet->getStyle($cellRange)->applyFromArray($styleBorderB);
        }
        foreach ($cellRanges as $cellRange) {
            $sheet->getStyle($cellRange)->applyFromArray($styleBorder);
        }
        
        $cellRangesByColor = [
            'c0504d' => [ // red
                'B5:O5', 'B23:O23', 'B48:O48',
                'D67', 'D68', 'D88', 'D89', 'D109', 'D110',
                'I67', 'I68', 'I88', 'I89',
                'N67', 'N68', 'N88', 'N89','C140','C141','H140','H141','M140','M141','E166','E167','L166','L167'
            ],
            '4f81bd' => [ //bleu
                'B4:O4', 'B22:O22', 'B45:O45',
                'C67', 'C68', 'C88', 'C89', 'C109', 'C110',
                'H67', 'H68', 'H88', 'H89','B186:O186',
                'M67', 'M68', 'M88', 'M89','B140','B141','G140','G141','L140','L141','D166','D167','K166','K167'
            ],
            '9bbb59' => [ // vert
                'F166','F167','M166','M167','D140','D141','I140','I141','N140','N141'
            ],
            '8064a2' => [ // violet
                'E140','E141','J140','J141','O140','O141','G166','G167','N166','N167'
            ],
            '4bacc6' => [ // bleu cyan
                'H166','H167','O166','O167'
            ],
            // Ajoutez ici d'autres plages de cellules par couleur
        ];
        
        foreach ($cellRangesByColor as $color => $cellRanges) {
            $style = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]]];
            foreach ($cellRanges as $cellRange) {
                $sheet->getStyle($cellRange)->applyFromArray($style);
            }
        }

        $sheet->setCellValue('B3', 'Volume'); // Commence à la ligne 3 pour le tableau
        $sheet->setCellValue('B4', 'MPPA');
        $sheet->setCellValue('B5', 'MABC');
        $sheet->setCellValue('B6', 'TOTAL');
        $sheet->setCellValue('O3', 'TOTAL');
        
        $sheet->setCellValue('B21', 'Valeur (HT)'); // Commence à la ligne 23 pour le tableau
        $sheet->setCellValue('B22', 'MPPA');
        $sheet->setCellValue('B23', 'MABC');
        $sheet->setCellValue('B24', 'TOTAL');
        $sheet->setCellValue('O23', 'TOTAL');
        
        // Insérer les mois en troisième ligne
        foreach ($mois as $index => $moi) {
            $sheet->setCellValue($col . '3', $moi); // Insérer le mois à partir de la ligne 3
    $sheet->setCellValue($col . '4', $datasets1[$index % count($datasets1)]);
    $sheet->setCellValue($col . '5', $datasets2[$index % count($datasets2)]);
    $sheet->setCellValue($col . '6', $datasets1[$index % count($datasets1)] + $datasets2[$index % count($datasets2)]);
            $sheet->setCellValue($col . '21', $moi); // Insérer le mois à partir de la ligne 23
            $sheet->setCellValue($col . '22', $datasets3[$index % count($datasets3)]);
            $sheet->setCellValue($col . '23', $datasets4[$index % count($datasets4)]);
            $sheet->setCellValue($col . '24', $datasets3[$index % count($datasets3)] + $datasets4[$index % count($datasets4)]);
            $col++; // Passer à la colonne suivante pour le mois suivant
        }
        
        // Ajustement des formules de somme dans la colonne O
        $sheet->setCellValue('O4', '=SUM(C4:N4)');
        $sheet->setCellValue('O5', '=SUM(C5:N5)');
        $sheet->setCellValue('O6', '=SUM(C6:N6)');
        $sheet->setCellValue('O22', '=SUM(C22:N22)');
        $sheet->setCellValue('O23', '=SUM(C23:N23)');
        $sheet->setCellValue('O24', '=SUM(C24:N24)');
        
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$4', null, 12),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$5', null, 12),
        ];
        
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$3:$N$3', null, 12),
        ];
        
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$4:$N$4', null, 12),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$5:$N$5', null, 12),
        ];
        
        $series1 = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        
        $layout = new Layout();
        $layout->setShowVal(true);
        $plotArea = new PlotArea($layout, [$series1]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Activité appro en volume');
        
        $chart = new Chart(
            'chart1',
            $title,
            $legend,
            $plotArea
        );  
        $chart->setTopLeftPosition('B7');
        $chart->setBottomRightPosition('P20');
                //valeur



                    $dataSeriesValues2 = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$22:$N$22', null, 12), // Valeurs
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$23:$N$23', null, 12), // Valeurs pour datasets2
                    ];

                    $series2 = new DataSeries(
                        DataSeries::TYPE_BARCHART, // Type de graphe
                        DataSeries::GROUPING_CLUSTERED,
                        range(0, count($dataSeriesValues2) - 1),
                        $dataSeriesLabels,
                        $xAxisTickValues,
                        $dataSeriesValues2
                    );
                    $layout = new Layout();
                    $layout->setShowVal(true);
                    $plotArea2 = new PlotArea($layout, [$series2]);
                    $legend2 = new Legend(Legend::POSITION_RIGHT, null, false);
                    $title2 = new Title('Activité appro en valeur (HT)');
                    
                    $chart2 = new Chart(
                        'chart2',
                        $title2,
                        $legend2,
                        $plotArea2
                    );
                    $chart2->setTopLeftPosition('B25');
                    $chart2->setBottomRightPosition('P38');
                

                $sheet->addChart($chart);
                $sheet->addChart($chart2);



                //-------------------------- Delay chart -------------------------------- // 
                $sheet->setCellValue('H40', 'Activités appro délais')
                ->getStyle('H40')
                ->getFont()
                ->setSize(16)
                ->setColor(new Color(Color::COLOR_RED));
            $mois = ['Délai','Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre','TOTAL'];
            $col = 43; 
            $colmonth='B';

            for($j=0;$j<=13;$j++){
                $sheet->setCellValue($colmonth . 42, $mois[$j]);
                $colmonth++;
            }
            for ($i=0;$i < 9;$i++) {
                $sheet->setCellValue('B' . $col, $achats[$i]["source"]); 
                $sheet->setCellValue('C' . $col, $achats[$i]["Janvier"]); 
                $sheet->setCellValue('D' . $col, $achats[$i]["Février"]); 
                $sheet->setCellValue('E' . $col, $achats[$i]["Mars"]); 
                $sheet->setCellValue('F' . $col, $achats[$i]["Avril"]); 
                $sheet->setCellValue('G' . $col, $achats[$i]["Mai"]); 
                $sheet->setCellValue('H' . $col, $achats[$i]["Juin"]); 
                $sheet->setCellValue('I' . $col, $achats[$i]["Juillet"]); 
                $sheet->setCellValue('J' . $col, $achats[$i]["Aout"]); 
                $sheet->setCellValue('K' . $col, $achats[$i]["Septembre"]); 
                $sheet->setCellValue('L' . $col, $achats[$i]["Octobre"]); 
                $sheet->setCellValue('M' . $col, $achats[$i]["Novembre"]); 
                $sheet->setCellValue('N' . $col, $achats[$i]["Decembre"]);
                $sheet->setCellValue('O' . $col, ($achats[$i]["Janvier"] +  $achats[$i]["Février"] + $achats[$i]["Mars"] + $achats[$i]["Avril"] + $achats[$i]["Mai"] + $achats[$i]["Juin"] + $achats[$i]["Juillet"] + $achats[$i]["Aout"] + $achats[$i]["Septembre"] + $achats[$i]["Octobre"] + $achats[$i]["Novembre"] + $achats[$i]["Decembre"])/12);
                $col++;
            }   

            $notTransLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$45', null, 12), 
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$48', null, 12), // Mois
            ];

            $notTransxAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$42:$N$42', null, 12), // 'Valeurs'
            ];
            $notTransValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$45:$N$45', null, 12), // Valeurs pour datasets1
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$48:$N$48', null, 12), // Valeurs pour datasets2
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
                    
                    $approVolChart->setTopLeftPosition('B52');
                    $approVolChart->setBottomRightPosition('P66');
                    $sheet->addChart($approVolChart);


    //-------------------------- Ant GSBDD chart -------------------------------- // 
                $sheet->setCellValue('B' . 68, 'Ant. GSBDD');
                $sheet->setCellValue('C' . 67, '<= 3j / ' .$achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"]. "%");
                $sheet->setCellValue('D' . 67, '> 3j / '.$achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"] . "%");  
                $sheet->setCellValue('C' . 68, $achats_delay_all[0]["CountAntInf3"]); 
                $sheet->setCellValue('D' . 68, $achats_delay_all[0]["CountAntSup3"]); 

                $antLabels = [
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$68', null, 12), 

                ];
                
                $antValues = [
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$68:$D$68', null, 2),
                ];

                $antxAx = [
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$67:$D$67', null, 4), // 'Valeurs'
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
                        
                    $antChart->setTopLeftPosition('B69');
                    $antChart->setBottomRightPosition('F86');
                    $sheet->addChart($antChart);

        


    //-------------------------- Budget chart -------------------------------- // 

            $sheet->setCellValue('G' . 68, 'Budget');
            $sheet->setCellValue('H' . 67, '<= 3j / ' .$achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"] . "%");
            $sheet->setCellValue('I' . 67, '> 3j / '. $achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"] . "%");  
            $sheet->setCellValue('H' . 68, $achats_delay_all[1]["CountBudgetInf3"]); 
            $sheet->setCellValue('I' . 68, $achats_delay_all[1]["CountBudgetSup3"]); 

            $budgetLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$68', null, 12), 

            ];
            
            $budgetValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$68:$I$68', null, 2),
            ];

            $budgetxAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$67:$I$67', null, 4), // 'Valeurs'
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
                
                        $budgetChart->setTopLeftPosition('G69');
                        $budgetChart->setBottomRightPosition('K86');
                        $sheet->addChart($budgetChart);





                        
    //-------------------------- APPRO chart -------------------------------- // 

        $sheet->setCellValue('L' . 68, 'APPRO');
        $sheet->setCellValue('M' . 67, '<= 7j / ' .$achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"]. "%");
        $sheet->setCellValue('N' . 67, '> 7j / '.$achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"]. "%");  
        $sheet->setCellValue('M' . 68, $achats_delay_all[2]["CountApproInf7"]); 
        $sheet->setCellValue('N' . 68, $achats_delay_all[2]["CountApproSup7"]); 

        $approLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$68', null, 12), 

        ];
        
        $approValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$M$68:$N$68', null, 2),
        ];

        $approxAx = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$67:$Nx$67', null, 4), // 'Valeurs'
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
                
                $approChart->setTopLeftPosition('L69');
                $approChart->setBottomRightPosition('P86');
                $sheet->addChart($approChart);


    //-------------------------- Fin chart -------------------------------- // 


        $sheet->setCellValue('B' . 89, 'Fin');
        $sheet->setCellValue('C' . 88, '< 7j / ' .$achats_delay_all[3]["Pourcentage_Delai_Inf_7_Jours_Fin"] . "%");
        $sheet->setCellValue('D' . 88, '> 7j / '.$achats_delay_all[3]["Pourcentage_Delai_Sup_7_Jours_Fin"] . "%");  
        $sheet->setCellValue('C' . 89, $achats_delay_all[3]["CountFinInf7"]); 
        $sheet->setCellValue('D' . 89, $achats_delay_all[3]["CountFinSup7"]); 

        $finLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$88', null, 12), 

        ];
        
        $finValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$89:$D$89', null, 2),
        ];

        $finxAx = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$88:$D$88', null, 4), // 'Valeurs'
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
                
                $finChart->setTopLeftPosition('B90');
                $finChart->setBottomRightPosition('F107');
                $sheet->addChart($finChart);



                
    //-------------------------- Chorus formul. chart -------------------------------- // 


    

        $sheet->setCellValue('G' . 89, 'Chorus formul.');
        $sheet->setCellValue('H' . 88, '<= 10j / ' .$achats_delay_all[4]["Pourcentage_Delai_Inf_10_Jours_Chorus"] . "%");
        $sheet->setCellValue('I' . 88, '> à 10j / '.$achats_delay_all[4]["Pourcentage_Delai_Sup_10_Jours_Chorus"] . "%");  
        $sheet->setCellValue('H' . 89, $achats_delay_all[4]["CountChorusFormInf10"]); 
        $sheet->setCellValue('I' . 89, $achats_delay_all[4]["CountChorusFormSup10"]); 

        $chorLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$88', null, 12), 

        ];
        
        $chorValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$89:$I$89', null, 2),
        ];

        $chorxAx = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$88:$I$88', null, 4), // 'Valeurs'
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
                
                $chorChart->setTopLeftPosition('G90');
                $chorChart->setBottomRightPosition('K107');
                $sheet->addChart($chorChart);



                

        
    //-------------------------- PFAF chart -------------------------------- // 

        $sheet->setCellValue('L' . 89, 'PFAF');
        $sheet->setCellValue('M' . 88,  '<= 14j / ' .$achats_delay_all[5]["Pourcentage_Delai_Inf_14_Jours_Pfaf"] . "%");
        $sheet->setCellValue('N' . 88, '> à 14j / '.$achats_delay_all[5]["Pourcentage_Delai_Sup_14_Jours_Pfaf"] . "%");  
        $sheet->setCellValue('M' . 89, $achats_delay_all[5]["CountPfafInf14"]); 
        $sheet->setCellValue('N' . 89, $achats_delay_all[5]["CountPfafSup14"]); 

        $pfafLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$89', null, 12), 

        ];
        
        $pfafValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$M$89:$N$89', null, 2),
        ];

        $pfafxAx = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$88:$N$88', null, 4), // 'Valeurs'
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
                
                $pfafChart->setTopLeftPosition('L90');
                $pfafChart->setBottomRightPosition('P107');
                $sheet->addChart($pfafChart);


    //-------------------------- Total chart -------------------------------- // 

        $sheet->setCellValue('B' . 110, 'Délai total');
        $sheet->setCellValue('C' . 109,  '<= 15j / ' .$achats_delay_all[6]["Pourcentage_Delai_Inf_15_Jours"]. "%");
        $sheet->setCellValue('D' . 109, '> à 15j / '.$achats_delay_all[6]["Pourcentage_Delai_Sup_15_Jours"]. "%");  
        $sheet->setCellValue('C' . 110, $achats_delay_all[6]["CountDelaiTotalInf15"]); 
        $sheet->setCellValue('D' . 110, $achats_delay_all[6]["CountDelaiTotalSup15"]); 

        $totalLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$109', null, 12), 

        ];
        
        $totalValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$110:$D$110', null, 2),
        ];

        $totalxAx = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$109:$D$109', null, 4), // 'Valeurs'
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
                
                $totalChart->setTopLeftPosition('B111');
                $totalChart->setBottomRightPosition('F128');
                $sheet->addChart($totalChart);



                //------------------------------ Tab % vol/val_type --------------------------//

                $sheet->setCellValue('H129', 'Activités par type de marché')
                ->getStyle('H129')
                ->getFont()
                ->setSize(16)
                ->setColor(new Color(Color::COLOR_RED));
            $sheet->setCellValue('C131', "MPPA"); 
            $sheet->setCellValue('D131', "MABC"); 
            $sheet->setCellValue('E131', "TOTAUX");
            $sheet->setCellValue('B132', "VALEUR"); 
            $sheet->setCellValue('B133', "NOMBRE"); 
            $sheet->setCellValue('B134', "MOYENNE"); 
            $sheet->setCellValue('B135', "% VALEUR");
            $sheet->setCellValue('B136', "% VOLUME"); 
            $sheet->setCellValue('C133', $result_achats[0]["somme_montant_type_1"]); 
            $sheet->setCellValue('D132', $result_achats[1]["somme_montant_type_0"]); 
            $sheet->setCellValue('E132', $result_achats[1]["somme_montant_type_0"] + $result_achats[0]["somme_montant_type_1"]); 
            $sheet->setCellValue('C133', $result_achats[0]["nombre_achats_type_1"]); 
            $sheet->setCellValue('D133', $result_achats[1]["nombre_achats_type_0"]); 
            $sheet->setCellValue('C133', $result_achats[1]["nombre_achats_type_0"] +  $result_achats[0]["nombre_achats_type_1"]); 
            $sheet->setCellValue('C134', $result_achats[0]["moyenne_montant_type_1"]); 
            $sheet->setCellValue('D134', $result_achats[1]["moyenne_montant_type_0"]); 
            $sheet->setCellValue('E134', $result_achats[1]["moyenne_montant_type_0"] +  $result_achats[0]["moyenne_montant_type_1"]); 
            $sheet->setCellValue('C135', $result_achats[0]["pourcentage_type_1_total"]); 
            $sheet->setCellValue('D135', $result_achats[1]["pourcentage_type_0_total"]); 
            $sheet->setCellValue('C136', $result_achats[0]["pourcentage_type_1"]); 
            $sheet->setCellValue('D136', $result_achats[1]["pourcentage_type_0"]);


        //------------------------------ Tab montant_type --------------------------//
            $sheet->setCellValue('C139', "Montant des MPPA");  
            $sheet->setCellValue('B140', "X <= ". $parameter[0]->getFour2()); 
            $sheet->setCellValue('C140', $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); 
            $sheet->setCellValue('D140',  $parameter[0]->getFour3()." < X <=".$parameter[0]->getFour4()); 
            $sheet->setCellValue('E140', "X > ". $parameter[0]->getFour4()); 
            $sheet->setCellValue('B141', $result_achats_mounts[0]["nombre_achats_inf_four1"]); 
            $sheet->setCellValue('C141', $result_achats_mounts[0]["nombre_achats_four1_four2"]); 
            $sheet->setCellValue('D141',  $result_achats_mounts[0]["nombre_achats_four2_four3"]); 
            $sheet->setCellValue('E141', $result_achats_mounts[0]["nombre_achats_sup_four3"]); 

            $sheet->setCellValue('H139', "Montant des MABC"); 
            $sheet->setCellValue('G140', "X <= ". $parameter[0]->getFour2()); 
            $sheet->setCellValue('H140', $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); 
            $sheet->setCellValue('I140',  $parameter[0]->getFour3()." < X <=".$parameter[0]->getFour4()); 
            $sheet->setCellValue('J140', "X > ". $parameter[0]->getFour4()); 
            $sheet->setCellValue('G141', $result_achats_mounts[1]["nombre_achats_inf_four1"]); 
            $sheet->setCellValue('H141', $result_achats_mounts[1]["nombre_achats_four1_four2"]); 
            $sheet->setCellValue('I141',  $result_achats_mounts[1]["nombre_achats_four2_four3"]); 
            $sheet->setCellValue('J141', $result_achats_mounts[1]["nombre_achats_sup_four3"]); 

            $sheet->setCellValue('M139', "Montant des MABC + MPPA"); 
            $sheet->setCellValue('L140', "X <= ". $parameter[0]->getFour2()); 
            $sheet->setCellValue('M140',$parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); 
            $sheet->setCellValue('N140',  $parameter[0]->getFour3()." < X <=".$parameter[0]->getFour4()); 
            $sheet->setCellValue('O140', "X > ". $parameter[0]->getFour4()); 
            $sheet->setCellValue('L141', $result_achats_mounts[0]["nombre_achats_inf_four1"] + $result_achats_mounts[1]["nombre_achats_inf_four1"]); 
            $sheet->setCellValue('M141', $result_achats_mounts[0]["nombre_achats_four1_four2"] + $result_achats_mounts[1]["nombre_achats_four1_four2"]); 
            $sheet->setCellValue('N141',  $result_achats_mounts[0]["nombre_achats_four2_four3"] + $result_achats_mounts[1]["nombre_achats_four2_four3"]); 
            $sheet->setCellValue('O141', $result_achats_mounts[0]["nombre_achats_sup_four3"] + $result_achats_mounts[1]["nombre_achats_sup_four3"]);


        //------------------------------ chart montant_type --------------------------//

            $mppaLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$139', null, 12), 

            ];
            
            $mppaValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$141:$E$141', null, 4),
            ];

            $mppaxAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$140:$E$140', null, 4), // 'Valeurs'
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
                    
                $mppaChart->setTopLeftPosition('B142');
                $mppaChart->setBottomRightPosition('F157');
                $sheet->addChart($mppaChart);

        //------------------------------ chart montant_type --------------------------//

            $mabcLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$139', null, 12), 

            ];
            
            $mabcValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$G$141:$J$141', null, 4),
            ];

            $mabcxAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$140:$J$140', null, 4), // 'Valeurs'
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
                    
                $mabcChart->setTopLeftPosition('G142');
                $mabcChart->setBottomRightPosition('K157');
                $sheet->addChart($mabcChart);

        //------------------------------ chart total_type --------------------------//
            $totaltypeLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$139', null, 12), 

            ];
            
            $totaltypeValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$L$141:$O$141', null, 4),
            ];

            $totaltypexAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$140:$M$140', null, 4), // 'Valeurs'
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
                    
                $totaltypeChart->setTopLeftPosition('L142');
                $totaltypeChart->setBottomRightPosition('P157');
                $sheet->addChart($totaltypeChart);
                
                $sheet->setCellValue('H159', 'Activités des PME')
                ->getStyle('H159')
                ->getFont()
                ->setSize(16)
                ->setColor(new Color(Color::COLOR_RED));
        
        $topValCol="D";
        $topVolCol="K";

        $sheet->setCellValue('B160', 'MPPA PME');
        $sheet->setCellValue('D160', 'PME');
        $sheet->setCellValue('E160','% PME');
        $sheet->setCellValue('C161','VALEUR');
        $sheet->setCellValue('C162', 'NOMBRE');
        $sheet->setCellValue('D161', $result_achatsPME[0]["ValeurPME"]);
        $sheet->setCellValue('E161', $result_achatsPME[0]["ValeurPercentPME"]);
        $sheet->setCellValue('D162', $result_achatsPME[0]["VolumePME"]);
        $sheet->setCellValue('E162', $result_achatsPME[0]["VolumePercentPME"]);

        $sheet->setCellValue('B165', 'TOP PME VALEUR');
        $sheet->setCellValue('C166','VALEUR');
        $sheet->setCellValue('C167', 'DEPARTEMENT');

        for($i=0;$i<count($result_achatsSumVal);$i++){

            $sheet->setCellValue($topValCol . 166, $result_achatsSumVal[$i]["somme_montant_achat"]);
            $sheet->setCellValue($topValCol . 167, $result_achatsSumVal[$i]["departement"]);
            $topValCol++;
        }


        $sheet->setCellValue('I165', 'TOP PME VOLUME');
        $sheet->setCellValue('J166','VOLUME');
        $sheet->setCellValue('J167', 'DEPARTEMENT');

        for($i=0;$i<count($result_achatsSumVol);$i++){

            $sheet->setCellValue($topVolCol . 166, $result_achatsSumVol[$i]["total_nombre_achats"]);
            $sheet->setCellValue($topVolCol . 167, $result_achatsSumVol[$i]["departement"]);
            $topVolCol++;
        }
        //------------------------------------- top dep val chart------------------------------------//
            $depValLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$167', null, 5),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$E$167', null, 5), 
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$F$167', null, 5), 
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$167', null, 5), 
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$167', null, 5), 

            ];

            $depValxAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$165', null, 5), // 'Valeurs'
            ];
            $depValValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$166', null, 5), // Valeurs pour datasets1
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$E$166', null, 5), // Valeurs pour datasets1
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$F$166', null, 5), // Valeurs pour datasets1
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$G$166', null, 5), // Valeurs pour datasets1
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$166', null, 5), // Valeurs pour datasets1
            ];
            $depValSeries = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($depValValues) - 1),
                $depValLabels,
                $depValxAx,
                $depValValues
            );
            $layout = new Layout();
            $layout->setShowVal(true);
            $depValplotArea = new PlotArea($layout, [$depValSeries]);
            $depValLegend = new Legend(Legend::POSITION_RIGHT, null, false);
            $depValTitle = new Title('TOP 5 DEPARTEMENT MPPA PME EN VALEUR');
                    
                    $depValChart = new Chart(
                        'depValChart',
                        $depValTitle,
                        $depValLegend,
                        $depValplotArea
                    );
                    
                    $depValChart->setTopLeftPosition('B169');
                    $depValChart->setBottomRightPosition('I182');
                    $sheet->addChart($depValChart);



        //------------------------------------- top dep vol  chart------------------------------------//

            $depVolLabels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$K$167', null, 5),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$167', null, 5), 
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$167', null, 5), 
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$167', null, 5), 
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$O$167', null, 5), 

            ];

            $depVolxAx = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$I$165', null, 5), // 'Valeurs'
            ];  
            $depVolValues = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$K$166', null, 5), // Valeurs pour datasets1
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$L$166', null, 5), // Valeurs pour datasets1
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$M$166', null, 5), // Valeurs pour datasets1
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$166', null, 5), // Valeurs pour datasets1
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$O$166', null, 5), // Valeurs pour datasets1
            ];
            $depVolSeries = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($depVolValues) - 1),
                $depVolLabels,
                $depVolxAx,
                $depVolValues
            );
            $layout = new Layout();
            $layout->setShowVal(true);
            $depVolplotArea = new PlotArea($layout, [$depVolSeries]);
            $depVolLegend = new Legend(Legend::POSITION_RIGHT, null, false);
            $depVolTitle = new Title('TOP 5 DEPARTEMENT MPPA PME EN VOLUME');
                    
                    $depVolChart = new Chart(
                        'depVolChart',
                        $depVolTitle,
                        $depVolLegend,
                        $depVolplotArea
                    );
                    
                    $depVolChart->setTopLeftPosition('I169');
                    $depVolChart->setBottomRightPosition('P182');
                    $sheet->addChart($depVolChart);


        //------------------------------------- activite appro pme  ------------------------------------//

        $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        $sheet->setCellValue('B186', 'NB MPPA PME');
        $sheet->setCellValue('B187', '% MPPA');
        $approCol='C';

        for($i=0;$i<count($result_achatsSum);$i++){

            $sheet->setCellValue($approCol . 185, $mois[$i]);
            $sheet->setCellValue($approCol . 186, $result_achatsSum[$i]["nombre_achats_pme"]);
            $sheet->setCellValue($approCol . 187, $result_achatsSum[$i]["pourcentage_achats_type_marche_1"]);
            $approCol++;
        }
        $sheet->setCellValue('O185', "TOTAL");
        $sheet->setCellValue('O186',  '=SUM(C186:N186)');
        $sheet->setCellValue('O187',  '=SUM(C187:N187) / 12' );

        
        $approPmeLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$186', null, 12), 
        ];

        $approPmexAx = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$185:$N$185', null, 12), // 'Valeurs'
        ];
        $approPmeValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$186:$N$186', null, 12), // Valeurs pour datasets2
        ];
        $approPmeSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($approPmeValues) - 1),
            $approPmeLabels,
            $approPmexAx,
            $approPmeValues
        );
        $layout = new Layout();
        $layout->setShowVal(true);
        $approPmeplotArea = new PlotArea($layout, [$approPmeSeries]);
        $approPmeLegend = new Legend(Legend::POSITION_RIGHT, null, false);
        $approPmeTitle = new Title('Activité appro PME');
                
                $approPmeChart = new Chart(
                    'approPmeChart',
                    $approPmeTitle,
                    $approPmeLegend,
                    $approPmeplotArea
                );
                
                $approPmeChart->setTopLeftPosition('B188');
                $approPmeChart->setBottomRightPosition('P201');
                $sheet->addChart($approPmeChart);


        $filePath = $projectDir . '/public/nom_de_fichier.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($filePath);
        
        return $filePath; // ou retournez un objet BinaryFileResponse si vous le souhaitez
    }

}