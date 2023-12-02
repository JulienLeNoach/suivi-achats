<?php namespace App\Service;

use Dompdf\Dompdf;
use App\Repository\AchatRepository;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticVolValService  extends AbstractController
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

    // public function getCountsByDateAndType($data)
    // {
    //     // ... Logique de récupération des données depuis le repository ...
    //     return $counts;
    // }
    public function totalPerMonth(array $result): array
    {
        $months = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
        ];

        $counts = [];

        foreach ($months as $month) {
            $counts[$month] = ['count' => 0, 'totalmontant' => 0];
        }

        foreach ($result as $row) {
            $month = intval($row['month']);
            $totalmontant = $row['totalmontant'];
            $count = intval($row['count']);
            $counts[$months[$month]]['count'] += $count;
            $counts[$months[$month]]['totalmontant'] += $totalmontant;
        }

        return $counts;
    }


    public function purchaseCountByMonth($counts1,$counts2)
    {
        foreach ($counts1 as $month => $data1) {
            $data2 = $counts2[$month] ?? null;

            $purchaseCountByMonth[$month] = [
                'count1' => $data1['count'],
                'count2' => $data2 ? $data2['count'] : 0,
                'total'  => $data1['count'] + ($data2 ? $data2['count'] : 0)
            ];
        }
                return $purchaseCountByMonth;
    }

    public function purchaseTotalAmountByMonth($counts1,$counts2)
    {
        foreach($counts1 as $month => $data1) {
            $data2 = $counts2[$month] ?? null;

            $purchaseTotalAmountByMonth[$month] = [
                'totalmontant1' => $data1['totalmontant'],
                'totalmontant2' => $data2 ? $data2['totalmontant'] : 0,
                'total' => $data1['totalmontant'] + ($data2 ? $data2['totalmontant'] : 0)
            ];
        }
                return $purchaseTotalAmountByMonth;
    }
    public function arrayMapChart($counts1, $counts2, $dataKey)
    {
        foreach ($counts1 as $count1) {
            $datasets[] = $count1[$dataKey];
        }
        foreach ($counts2 as $count2) {
            $datasets2[] = $count2[$dataKey];
        }

        foreach ($counts1 as $count1) {
            $datasetst[] = $count1[$dataKey];
        }
        foreach ($counts2 as $count2) {
            $datasetst2[] = $count2[$dataKey];
        }
        $datasets = array_map(function($value) {
            return round($value, 2);
        }, $datasets);
        $datasets2 = array_map(function($value) {
            return round($value, 2);
        }, $datasets2);
        
        return ['datasets' => $datasets, 'datasets2' => $datasets2];
    }

    public function generateExcelFile($datasets1, $datasets2, $datasets3, $datasets4, $projectDir)
    {
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
        $filePath = $projectDir . '/public/nom_de_fichier.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($filePath);
        
        return $filePath; // ou retournez un objet BinaryFileResponse si vous le souhaitez
    }

}