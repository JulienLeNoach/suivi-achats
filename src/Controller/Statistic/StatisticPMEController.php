<?php

namespace App\Controller\Statistic;

use App\Form\StatisticType;
use App\Repository\AchatRepository;
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

class StatisticPMEController extends AbstractController
{

    private $achatRepository;
    private $projectDir;

    public function __construct(AchatRepository $achatRepository,KernelInterface $kernel)
    {
        $this->achatRepository = $achatRepository;
        $this->projectDir = $kernel->getProjectDir();


    }

    
    #[Route('/statisticpme', name: 'app_statisticpme')]
    public function index(Request $request): Response
    { 
        
        $form = $this->createForm(StatisticType::class, null, []);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result_achats = $this->achatRepository->statisticPMESum($form);
            $result_achatsSum = $this->achatRepository->statisticPMEMonth($form);
            $result_achatsSumVol = $this->achatRepository->statisticPMETopVol($form);
            $result_achatsSumVal = $this->achatRepository->statisticPMETopVal($form);
                // dd($result_achatsSumVal);
                if ($request->request->has('excel2')) {

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $topValCol="J";
                $topVolCol="S";

                $sheet->setCellValue('B2', 'MPPA PME');
                $sheet->setCellValue('D2', 'PME');
                $sheet->setCellValue('E2','% PME');
                $sheet->setCellValue('C3','VALEUR');
                $sheet->setCellValue('C4', 'NOMBRE');
                $sheet->setCellValue('D3', $result_achats[0]["ValeurPME"]);
                $sheet->setCellValue('E3', $result_achats[0]["ValeurPercentPME"]);
                $sheet->setCellValue('D4', $result_achats[0]["VolumePME"]);
                $sheet->setCellValue('E4', $result_achats[0]["VolumePercentPME"]);

                $sheet->setCellValue('H2', 'TOP PME VALEUR');
                $sheet->setCellValue('I3','VALEUR');
                $sheet->setCellValue('I4', 'DEPARTEMENT');

                for($i=0;$i<5;$i++){

                    $sheet->setCellValue($topValCol . 3, $result_achatsSumVal[$i]["somme_montant_achat"]);
                    $sheet->setCellValue($topValCol . 4, $result_achatsSumVal[$i]["departement"]);
                    $topValCol++;
                }


                $sheet->setCellValue('Q2', 'TOP PME VALEUR');
                $sheet->setCellValue('R3','VALEUR');
                $sheet->setCellValue('R4', 'DEPARTEMENT');

                for($i=0;$i<5;$i++){

                    $sheet->setCellValue($topVolCol . 3, $result_achatsSumVol[$i]["total_nombre_achats"]);
                    $sheet->setCellValue($topVolCol . 4, $result_achatsSumVol[$i]["departement"]);
                    $topVolCol++;
                }
                //------------------------------------- top dep val chart------------------------------------//
                    $depValLabels = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$J4', null, 5),
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$K$4', null, 5), 
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$4', null, 5), 
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$4', null, 5), 
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$4', null, 5), 

                    ];

                    $depValxAx = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$J$4:$N$4', null, 5), // 'Valeurs'
                    ];
                    $depValValues = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$J$3', null, 5), // Valeurs pour datasets1
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$K$3', null, 5), // Valeurs pour datasets1
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$L$3', null, 5), // Valeurs pour datasets1
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$M$3', null, 5), // Valeurs pour datasets1
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$3', null, 5), // Valeurs pour datasets1
                    ];
                    $depValSeries = new DataSeries(
                        DataSeries::TYPE_BARCHART,
                        DataSeries::GROUPING_CLUSTERED,
                        range(0, count($depValValues) - 1),
                        $depValLabels,
                        $depValxAx,
                        $depValValues
                    );

                    $depValplotArea = new PlotArea(null, [$depValSeries]);
                    $depValLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                    $depValTitle = new Title('TOP 5 DEPARTEMENT MPPA PME EN VALEUR');
                            
                            $depValChart = new Chart(
                                'depValChart',
                                $depValTitle,
                                $depValLegend,
                                $depValplotArea
                            );
                            
                            $depValChart->setTopLeftPosition('H6');
                            $depValChart->setBottomRightPosition('O19');
                            $sheet->addChart($depValChart);



                //------------------------------------- top dep vol  chart------------------------------------//
     
                    $depVolLabels = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$S4', null, 5),
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$T$4', null, 5), 
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$U$4', null, 5), 
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$V$4', null, 5), 
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$W$4', null, 5), 

                    ];

                    $depVolxAx = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$S$4:$W$4', null, 5), // 'Valeurs'
                    ];
                    $depVolValues = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$S$3', null, 5), // Valeurs pour datasets1
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$T$3', null, 5), // Valeurs pour datasets1
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$U$3', null, 5), // Valeurs pour datasets1
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$V$3', null, 5), // Valeurs pour datasets1
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$W$3', null, 5), // Valeurs pour datasets1
                    ];
                    $depVolSeries = new DataSeries(
                        DataSeries::TYPE_BARCHART,
                        DataSeries::GROUPING_CLUSTERED,
                        range(0, count($depVolValues) - 1),
                        $depVolLabels,
                        $depVolxAx,
                        $depVolValues
                    );

                    $depVolplotArea = new PlotArea(null, [$depVolSeries]);
                    $depVolLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                    $depVolTitle = new Title('TOP 5 DEPARTEMENT MPPA PME EN VOLUME');
                            
                            $depVolChart = new Chart(
                                'depVolChart',
                                $depVolTitle,
                                $depVolLegend,
                                $depVolplotArea
                            );
                            
                            $depVolChart->setTopLeftPosition('Q6');
                            $depVolChart->setBottomRightPosition('Y19');
                            $sheet->addChart($depVolChart);


                //------------------------------------- activite appro pme  ------------------------------------//

                $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

                $sheet->setCellValue('B23', 'NB MPPA PME');
                $sheet->setCellValue('B24', '% MPPA');
                $approCol='C';

                for($i=0;$i<12;$i++){

                    $sheet->setCellValue($approCol . 22, $mois[$i]);
                    $sheet->setCellValue($approCol . 23, $result_achatsSum[$i]["nombre_total_achats_pme"]);
                    $sheet->setCellValue($approCol . 24, $result_achatsSum[$i]["pourcentage_achats_type_marche_1"]);
                    $approCol++;
                }
                $sheet->setCellValue('O22', "TOTAL");
                $sheet->setCellValue('O23',  '=SUM(C23:N23)');
                $sheet->setCellValue('O24',  '=SUM(C24:N24) / 12' );

                
                $approPmeLabels = [
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$23', null, 12), 
                ];

                $approPmexAx = [
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$22:$N$22', null, 12), // 'Valeurs'
                ];
                $approPmeValues = [
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$23:$N$23', null, 12), // Valeurs pour datasets2
                ];
                $approPmeSeries = new DataSeries(
                    DataSeries::TYPE_BARCHART,
                    DataSeries::GROUPING_CLUSTERED,
                    range(0, count($approPmeValues) - 1),
                    $approPmeLabels,
                    $approPmexAx,
                    $approPmeValues
                );

                $approPmeplotArea = new PlotArea(null, [$approPmeSeries]);
                $approPmeLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                $approPmeTitle = new Title('Activité appro PME');
                        
                        $approPmeChart = new Chart(
                            'approPmeChart',
                            $approPmeTitle,
                            $approPmeLegend,
                            $approPmeplotArea
                        );
                        
                        $approPmeChart->setTopLeftPosition('B25');
                        $approPmeChart->setBottomRightPosition('O38');
                        $sheet->addChart($approPmeChart);
                $writer = new Xlsx($spreadsheet);
                $writer->setIncludeCharts(true);
                $writer->save($this->projectDir . '/public/nom_de_fichier.xlsx');
                return new BinaryFileResponse($this->projectDir . '/public/nom_de_fichier.xlsx');

            }
            return $this->render('statistic_pme/index.html.twig', [
                'form' => $form->createView(),
                'result_achats'=>$result_achats,
                'result_achatsSum'=>$result_achatsSum,
                'result_achatsSumVol'=>$result_achatsSumVol,
                'result_achatsSumVal'=>$result_achatsSumVal


    ]);
        }
        return $this->render('statistic_pme/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
