<?php

namespace App\Controller\Statistic;

use App\Form\StatisticType;
use App\Repository\AchatRepository;
use App\Service\StatisticDelayService;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticDelayController extends AbstractController
{

    private $achatRepository;
    private $statisticDelayService;
    private $projectDir;

    public function __construct(AchatRepository $achatRepository,KernelInterface $kernel, StatisticDelayService $statisticDelayService)
    {

        $this->achatRepository = $achatRepository;
        $this->statisticDelayService = $statisticDelayService;
        $this->projectDir = $kernel->getProjectDir();

    }

    #[Route('/statistic/delay', name: 'app_statistic_delay')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(StatisticType::class, null, []);
        $form->handleRequest($request);        
        $achats[]=null;

        if ($form->isSubmitted() && $form->isValid()) {
            $achats_delay = $this->achatRepository->yearDelayDiff($form);
            // dd($achats_delay);
            $achats = $this->statisticDelayService->totalDelayPerMonth($achats_delay);
            $achats_delay_all = $this->achatRepository->yearDelayCount($form);
            
        // Récupérez les données achats
        $transStat = array_filter(array_values($achats[2]), 'is_numeric');
        $notStat = array_filter(array_values($achats[5]), 'is_numeric');
            
        if ($form->get('recherche')->isClicked()) {
            // dd($achats);
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
            'achats' => $achats,
            'transStat' => $transStat,
            'notStat' => $notStat,
            'achats_delay_all' => $achats_delay_all,

        ]);
        }
        if ($form->get('excel')->isClicked() ) {

               

                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

                    //-------------------------- Appro volume chart -------------------------------- // 
                        $mois = ['Délai','Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre','TOTAL'];
                        $col = 2; 
                        $colmonth='B';
                        for($j=0;$j<=13;$j++){
                            $sheet->setCellValue($colmonth . 1, $mois[$j]);
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
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$4', null, 12), 
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$7', null, 12), // Mois
                        ];

                        $notTransxAx = [
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1:$N$1', null, 12), // 'Valeurs'
                        ];
                        $notTransValues = [
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$4:$N$4', null, 12), // Valeurs pour datasets1
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$7:$N$7', null, 12), // Valeurs pour datasets2
                        ];
                        $notTransSeries = new DataSeries(
                            DataSeries::TYPE_BARCHART,
                            DataSeries::GROUPING_CLUSTERED,
                            range(0, count($notTransValues) - 1),
                            $notTransLabels,
                            $notTransxAx,
                            $notTransValues
                        );

                        $notTransplotArea = new PlotArea(null, [$notTransSeries]);
                        $notTransLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                        $notTransTitle = new Title('Activité appro en volume');
                                
                                $approVolChart = new Chart(
                                    'approVolChart',
                                    $notTransTitle,
                                    $notTransLegend,
                                    $notTransplotArea
                                );
                                
                                $approVolChart->setTopLeftPosition('B12');
                                $approVolChart->setBottomRightPosition('O25');
                                $sheet->addChart($approVolChart);


                //-------------------------- Ant GSBDD chart -------------------------------- // 
                            $sheet->setCellValue('A' . 27, 'Antenne GSBDD');
                            $sheet->setCellValue('B' . 26, '<= 3 jours / ' .$achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"]. "%");
                            $sheet->setCellValue('C' . 26, '> 3 jours / '.$achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"] . "%");  
                            $sheet->setCellValue('B' . 27, $achats_delay_all[0]["CountAntInf3"]); 
                            $sheet->setCellValue('C' . 27, $achats_delay_all[0]["CountAntSup3"]); 

                            $antLabels = [
                                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$27', null, 12), 

                            ];
                            
                            $antValues = [
                                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$27:$C$27', null, 2),
                            ];

                            $antxAx = [
                                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$26:$C$26', null, 4), // 'Valeurs'
                            ];
                            $antSeries = new DataSeries(
                                DataSeries::TYPE_PIECHART, // plotType
                                null, // plotGrouping (Pie charts don't have any grouping)
                                range(0, count($antValues) - 1), // plotOrder
                                $antLabels, // plotLabel
                                $antxAx, // plotCategory
                                $antValues          // plotValues
                            );
                            $antplotArea = new PlotArea(null, [$antSeries]);
                            $antLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                            $antTitle = new Title('ANT GSBDD');
                                    
                                    $antChart = new Chart(
                                        'antChart',
                                        $antTitle,
                                        $antLegend,
                                        $antplotArea
                                    );
                                    
                                $antChart->setTopLeftPosition('B28');
                                $antChart->setBottomRightPosition('F41');
                                $sheet->addChart($antChart);

                    


                //-------------------------- Budget chart -------------------------------- // 

                        $sheet->setCellValue('G' . 27, 'Budget');
                        $sheet->setCellValue('H' . 26, '<= 3 jours / ' .$achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"] . "%");
                        $sheet->setCellValue('I' . 26, '> 3 jours / '. $achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"] . "%");  
                        $sheet->setCellValue('H' . 27, $achats_delay_all[1]["CountBudgetInf3"]); 
                        $sheet->setCellValue('I' . 27, $achats_delay_all[1]["CountBudgetSup3"]); 

                        $budgetLabels = [
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$27', null, 12), 

                        ];
                        
                        $budgetValues = [
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$27:$I$27', null, 2),
                        ];

                        $budgetxAx = [
                            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$26:$I$26', null, 4), // 'Valeurs'
                        ];
                        $budgetSeries = new DataSeries(
                            DataSeries::TYPE_PIECHART, // plotType
                            null, // plotGrouping (Pie charts don't have any grouping)
                            range(0, count($budgetValues) - 1), // plotOrder
                            $budgetLabels, // plotLabel
                            $budgetxAx, // plotCategory
                            $budgetValues          // plotValues
                        );
                        $budgetplotArea = new PlotArea(null, [$budgetSeries]);
                        $budgetLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                        $budgetTitle = new Title('Budget');
                            
                            $budgetChart = new Chart(
                                'buggetChart',
                                $budgetTitle,
                                $budgetLegend,
                                $budgetplotArea
                            );
                            
                                    $budgetChart->setTopLeftPosition('H28');
                                    $budgetChart->setBottomRightPosition('L41');
                                    $sheet->addChart($budgetChart);





                                    
                //-------------------------- APPRO chart -------------------------------- // 

                    $sheet->setCellValue('M' . 27, 'APPRO');
                    $sheet->setCellValue('N' . 26, '<= 7 jours / ' .$achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"]. "%");
                    $sheet->setCellValue('O' . 26, '> 7 jours / '.$achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"]. "%");  
                    $sheet->setCellValue('N' . 27, $achats_delay_all[2]["CountApproInf7"]); 
                    $sheet->setCellValue('O' . 27, $achats_delay_all[2]["CountApproSup7"]); 

                    $approLabels = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$27', null, 12), 

                    ];
                    
                    $approValues = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$27:$O$27', null, 2),
                    ];

                    $approxAx = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$26:$O$26', null, 4), // 'Valeurs'
                    ];
                    $approSeries = new DataSeries(
                        DataSeries::TYPE_PIECHART, // plotType
                        null, // plotGrouping (Pie charts don't have any grouping)
                        range(0, count($approValues) - 1), // plotOrder
                        $approLabels, // plotLabel
                        $approxAx, // plotCategory
                        $approValues          // plotValues
                    );
                    $approplotArea = new PlotArea(null, [$approSeries]);
                    $approLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                    $approTitle = new Title('Appro');
                            
                            $approChart = new Chart(
                                'approChart',
                                $approTitle,
                                $approLegend,
                                $approplotArea
                            );
                            
                            $approChart->setTopLeftPosition('N28');
                            $approChart->setBottomRightPosition('R41');
                            $sheet->addChart($approChart);


                //-------------------------- Fin chart -------------------------------- // 


                    $sheet->setCellValue('A' . 43, 'Fin');
                    $sheet->setCellValue('B' . 42, '< 7 jours / ' .$achats_delay_all[3]["Pourcentage_Delai_Inf_7_Jours_Fin"] . "%");
                    $sheet->setCellValue('C' . 42, '> 7 jours / '.$achats_delay_all[3]["Pourcentage_Delai_Sup_7_Jours_Fin"] . "%");  
                    $sheet->setCellValue('B' . 43, $achats_delay_all[3]["CountFinInf7"]); 
                    $sheet->setCellValue('C' . 43, $achats_delay_all[3]["CountFinSup7"]); 

                    $finLabels = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$42', null, 12), 

                    ];
                    
                    $finValues = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$43:$C$43', null, 2),
                    ];

                    $finxAx = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$42:$C$42', null, 4), // 'Valeurs'
                    ];
                    $finSeries = new DataSeries(
                        DataSeries::TYPE_PIECHART, // plotType
                        null, // plotGrouping (Pie charts don't have any grouping)
                        range(0, count($finValues) - 1), // plotOrder
                        $finLabels, // plotLabel
                        $finxAx, // plotCategory
                        $finValues          // plotValues
                    );
                    $finplotArea = new PlotArea(null, [$finSeries]);
                    $finLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                    $finTitle = new Title('Fin');
                            
                            $finChart = new Chart(
                                'finChart',
                                $finTitle,
                                $finLegend,
                                $finplotArea
                            );
                            
                            $finChart->setTopLeftPosition('B44');
                            $finChart->setBottomRightPosition('F58');
                            $sheet->addChart($finChart);



                            
                //-------------------------- Chorus formul. chart -------------------------------- // 


                

                    $sheet->setCellValue('G' . 43, 'Chorus formul.');
                    $sheet->setCellValue('H' . 42, '<= 10 jours / ' .$achats_delay_all[4]["Pourcentage_Delai_Inf_10_Jours_Chorus"] . "%");
                    $sheet->setCellValue('I' . 42, '> à 10 jours / '.$achats_delay_all[4]["Pourcentage_Delai_Sup_10_Jours_Chorus"] . "%");  
                    $sheet->setCellValue('H' . 43, $achats_delay_all[4]["CountChorusFormInf10"]); 
                    $sheet->setCellValue('I' . 43, $achats_delay_all[4]["CountChorusFormSup10"]); 

                    $chorLabels = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$42', null, 12), 

                    ];
                    
                    $chorValues = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$43:$I$43', null, 2),
                    ];

                    $chorxAx = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$42:$I$42', null, 4), // 'Valeurs'
                    ];
                    $chorSeries = new DataSeries(
                        DataSeries::TYPE_PIECHART, // plotType
                        null, // plotGrouping (Pie charts don't have any grouping)
                        range(0, count($chorValues) - 1), // plotOrder
                        $chorLabels, // plotLabel
                        $chorxAx, // plotCategory
                        $chorValues          // plotValues
                    );
                    $chorplotArea = new PlotArea(null, [$chorSeries]);
                    $chorLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                    $chorTitle = new Title('Chorus formul.');
                            
                            $chorChart = new Chart(
                                'chorChart',
                                $chorTitle,
                                $chorLegend,
                                $chorplotArea
                            );
                            
                            $chorChart->setTopLeftPosition('H44');
                            $chorChart->setBottomRightPosition('L58');
                            $sheet->addChart($chorChart);



                            

                    
                //-------------------------- PFAF chart -------------------------------- // 

                    $sheet->setCellValue('M' . 43, 'PFAF');
                    $sheet->setCellValue('N' . 42,  '<= 14 jours / ' .$achats_delay_all[5]["Pourcentage_Delai_Inf_14_Jours_Pfaf"] . "%");
                    $sheet->setCellValue('O' . 42, '> à 14 jours / '.$achats_delay_all[5]["Pourcentage_Delai_Sup_14_Jours_Pfaf"] . "%");  
                    $sheet->setCellValue('N' . 43, $achats_delay_all[5]["CountPfafInf14"]); 
                    $sheet->setCellValue('O' . 43, $achats_delay_all[5]["CountPfafSup14"]); 

                    $pfafLabels = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$43', null, 12), 

                    ];
                    
                    $pfafValues = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$43:$O$43', null, 2),
                    ];

                    $pfafxAx = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$42:$O$42', null, 4), // 'Valeurs'
                    ];
                    $pfafSeries = new DataSeries(
                        DataSeries::TYPE_PIECHART, // plotType
                        null, // plotGrouping (Pie charts don't have any grouping)
                        range(0, count($pfafValues) - 1), // plotOrder
                        $pfafLabels, // plotLabel
                        $pfafxAx, // plotCategory
                        $pfafValues          // plotValues
                    );
                    $pfafplotArea = new PlotArea(null, [$pfafSeries]);
                    $pfafLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                    $pfafTitle = new Title('PFAF');
                            
                            $pfafChart = new Chart(
                                'pfafChart',
                                $pfafTitle,
                                $pfafLegend,
                                $pfafplotArea
                            );
                            
                            $pfafChart->setTopLeftPosition('N44');
                            $pfafChart->setBottomRightPosition('R58');
                            $sheet->addChart($pfafChart);


                //-------------------------- Total chart -------------------------------- // 

                    $sheet->setCellValue('A' . 60, 'Délai total');
                    $sheet->setCellValue('B' . 59,  '<= 15 jours / ' .$achats_delay_all[0]["Pourcentage_Delai_Inf_15_Jours"]. "%");
                    $sheet->setCellValue('C' . 59, '> à 15 jours / '.$achats_delay_all[0]["Pourcentage_Delai_Sup_15_Jours"]. "%");  
                    $sheet->setCellValue('B' . 60, $achats_delay_all[0]["CountDelaiTotalInf15"]); 
                    $sheet->setCellValue('C' . 60, $achats_delay_all[0]["CountDelaiTotalSup15"]); 

                    $totalLabels = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$59', null, 12), 

                    ];
                    
                    $totalValues = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$60:$C$60', null, 2),
                    ];

                    $totalxAx = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$59:$C$59', null, 4), // 'Valeurs'
                    ];
                    $totalSeries = new DataSeries(
                        DataSeries::TYPE_PIECHART, // plotType
                        null, // plotGrouping (Pie charts don't have any grouping)
                        range(0, count($totalValues) - 1), // plotOrder
                        $totalLabels, // plotLabel
                        $totalxAx, // plotCategory
                        $totalValues // plotValues
                    );
                    $totalplotArea = new PlotArea(null, [$totalSeries]);
                    $totalLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                    $totalTitle = new Title('Délai Total');
                            
                            $totalChart = new Chart(
                                'totalChart',
                                $totalTitle,
                                $totalLegend,
                                $totalplotArea
                            );
                            
                            $totalChart->setTopLeftPosition('B61');
                            $totalChart->setBottomRightPosition('F77');
                            $sheet->addChart($totalChart);



            
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($this->projectDir . '/public/nom_de_fichier.xlsx');
        return new BinaryFileResponse($this->projectDir . '/public/nom_de_fichier.xlsx');
        }

        }
        // dd("test");
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
            'achats' => $achats,
        ]);
    }


}