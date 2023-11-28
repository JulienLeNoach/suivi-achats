<?php

namespace App\Controller\Statistic;

// ...

use Dompdf\Dompdf;
use App\Form\StatisticType;
use App\Form\ValidAchatType;
use App\Service\CalendarService;
use App\Repository\AchatRepository;
use App\Service\StatisticVolValService;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Achat; // Make sure this use statement is correct
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//La méthode showStat de la classe, qui est associée à la route '/statistic',
//est la fonction principale pour afficher les statistiques.
//D'abord, elle crée un formulaire à l'aide de la classe ValidEditAchatTyê
// et gère la requête HTTP entrante. Si le formulaire est soumis et validé,
// elle récupère la somme des achats pour deux types spécifiques
// (mpttaEtat et mabcEtat) via la méthode getCountsByDateAndType du dépôt AchatRepository.
//Ensuite, elle combine ces sommes par mois en un seul tableau, en incluant le total pour chaque mois.



class StatisticVolController extends AbstractController
{
    private $entityManager;
    private $achatRepository;
    private $projectDir;
    private $statisticService;
    public function __construct(EntityManagerInterface $entityManager, AchatRepository $achatRepository, KernelInterface $kernel, StatisticVolValService $statisticService)
    {
        $this->entityManager = $entityManager;
        $this->statisticService = $statisticService;
        $this->achatRepository = $achatRepository;
        $this->projectDir = $kernel->getProjectDir();
    }

    #[Route('/statistic/vol', name: 'app_statistic_vol')]
    public function showStat(Request $request, EntityManagerInterface $entityManager, ChartBuilderInterface $chartBuilder): Response
    {

        $form = $this->createForm(StatisticType::class, null, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mpttaEtat = 1;
            $mabcEtat = 0;
            $counts1 = [];
            $counts2 = [];
            $counts1 = $this->achatRepository->getPurchaseCountAndTotalAmount($mpttaEtat,$form);
            $counts1 = $this->statisticService->totalPerMonth($counts1);
            $counts2 = $this->achatRepository->getPurchaseCountAndTotalAmount($mabcEtat,$form);
            $counts2 = $this->statisticService->totalPerMonth($counts2);
            $purchaseCountByMonth = $this->statisticService->purchaseCountByMonth($counts1,$counts2);
            $purchaseTotalAmountByMonth = $this->statisticService->purchaseTotalAmountByMonth($counts1,$counts2);

            $chartData = $this->statisticService->arrayMapChart( $counts1, $counts2, 'count');
            $chartData2 = $this->statisticService->arrayMapChart($counts1, $counts2, 'totalmontant');
            $datasets1 = $chartData['datasets'];
            $datasets2 = $chartData['datasets2'];
            $datasets3 = $chartData2['datasets'];
            $datasets4 = $chartData2['datasets2'];
            if ($form->get('recherche')->isClicked()) {


                return $this->render('statistic/index.html.twig', [
                    'form' => $form->createView(),
                    'counts1' => $counts1,
                    'counts2' => $counts2,
                    'purchaseCountByMonth' => $purchaseCountByMonth,
                    'purchaseTotalAmountByMonth' => $purchaseTotalAmountByMonth,
                    'datasets1' => $datasets1,
                    'datasets2' => $datasets2,
                    'datasets3' => $datasets3,
                    'datasets4' => $datasets4,
                ]);
            } 

            if ($form->get('excel')->isClicked() ) {
     
                    $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

                    $col = 'E'; // Commencez à partir de la colonne E pour les données
                    foreach (range('B', 'Q') as $columnID) {
                        $sheet->getColumnDimension($columnID)->setWidth(15); // Définir la largeur à 15 pour chaque colonne
                    }

                    $sheet->setCellValue('D1', 'Volume'); 
                    $sheet->setCellValue('D2', 'MPPA'); 
                    $sheet->setCellValue('D3', 'MABC'); 
                    $sheet->setCellValue('D4', 'TOTAL'); 
                    $sheet->setCellValue('Q1', 'TOTAL');

                    $sheet->setCellValue('D21', 'Valeur (HT)'); 
                    $sheet->setCellValue('D22', 'MPPA'); 
                    $sheet->setCellValue('D23', 'MABC'); 
                    $sheet->setCellValue('D24', 'TOTAL'); 
                    $sheet->setCellValue('Q21', 'TOTAL'); 

                    // Insérer les mois en première ligne
                    foreach ($mois as $index => $moi) {
                        $sheet->setCellValue($col . '1', $moi); 
                        $sheet->setCellValue($col . '2', $datasets1[$index % count($datasets1)]); 
                        $sheet->setCellValue($col . '3', $datasets2[$index % count($datasets2)]); 
                        $sheet->setCellValue($col . '4', $datasets1[$index % count($datasets1)]+ $datasets2[$index % count($datasets2)]); 
                        $sheet->setCellValue($col . '21', $moi); // Insérer le mois
                        $sheet->setCellValue($col . '22', $datasets3[$index % count($datasets3)]); // Valeur de datasets1
                        $sheet->setCellValue($col . '23', $datasets4[$index % count($datasets4)]); // Valeur de datasets2
                        $sheet->setCellValue($col . '24', $datasets3[$index % count($datasets3)] + $datasets4[$index % count($datasets4)]); // Valeur de datasets2
                        $col++; // Passer à la colonne suivante pour le mois suivant
                    }   
                    $sheet->setCellValue('Q2', '=SUM(E2:P2)');
                    $sheet->setCellValue('Q3', '=SUM(E3:P3)');
                    $sheet->setCellValue('Q4', '=SUM(E4:P4)');
                    $sheet->setCellValue('Q22', '=SUM(E22:P22)');
                    $sheet->setCellValue('Q23', '=SUM(E23:P23)');
                    $sheet->setCellValue('Q24', '=SUM(E24:P24)');

                    $dataSeriesLabels = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$2', null, 12), 
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$3', null, 12), // Mois
                    ];
                    $xAxisTickValues = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$E$1:$P$1', null, 12), // 'Valeurs'
                    ];
                    $dataSeriesValues = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$E$2:$P$2', null, 12), // Valeurs pour datasets1
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$E$3:$P$3', null, 12), // Valeurs pour datasets2
                    ];

                            
                    $series1 = new DataSeries(
                        DataSeries::TYPE_BARCHART,
                        DataSeries::GROUPING_CLUSTERED,
                        range(0, count($dataSeriesValues) - 1),
                        $dataSeriesLabels,
                        $xAxisTickValues,
                        $dataSeriesValues
                    );

                    $plotArea = new PlotArea(null, [$series1]);
                    $legend = new Legend(Legend::POSITION_RIGHT, null, false);
                    $title = new Title('Activité appro en volume');
                            
                            $chart = new Chart(
                                'chart1',
                                $title,
                                $legend,
                                $plotArea
                            );
                            
                            $chart->setTopLeftPosition('D5');
                            $chart->setBottomRightPosition('R20');
                            
                            //valeur



                                $dataSeriesValues2 = [
                                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$E$22:$P$22', null, 12), // Valeurs
                                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$E$23:$P$23', null, 12), // Valeurs pour datasets2
                                ];

                                $series2 = new DataSeries(
                                    DataSeries::TYPE_BARCHART, // Type de graphe
                                    DataSeries::GROUPING_CLUSTERED,
                                    range(0, count($dataSeriesValues2) - 1),
                                    $dataSeriesLabels,
                                    $xAxisTickValues,
                                    $dataSeriesValues2
                                );

                                $plotArea2 = new PlotArea(null, [$series2]);
                                $legend2 = new Legend(Legend::POSITION_RIGHT, null, false);
                                $title2 = new Title('Activité appro en valeur (HT)');
                                
                                $chart2 = new Chart(
                                    'chart2',
                                    $title2,
                                    $legend2,
                                    $plotArea2
                                );
                                $chart2->setTopLeftPosition('D25');
                                $chart2->setBottomRightPosition('R40');
                            

                            $sheet->addChart($chart);
                            $sheet->addChart($chart2);
                            $writer = new Xlsx($spreadsheet);
                            $writer->setIncludeCharts(true);
                            $writer->save($this->projectDir . '/public/nom_de_fichier.xlsx');
                            return new BinaryFileResponse($this->projectDir . '/public/nom_de_fichier.xlsx');
                               }
        }
        return $this->render('statistic/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/statistic/vol_excel', name: 'vol_excel')]
    public function excel(Request $request, EntityManagerInterface $entityManager, ChartBuilderInterface $chartBuilder): Response
    {
        $form = $this->createForm(StatisticType::class, null, []);

        $form->handleRequest($request);
        


    }
}
