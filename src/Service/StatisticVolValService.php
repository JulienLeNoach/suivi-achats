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
    $sheet->setCellValue('H1', 'Activités appro en volume/valeur')
    ->getStyle('H1')
    ->getFont()
    ->setBold(true)
    ->setSize(18)
    ->setColor(new Color(Color::COLOR_RED));

    $col = 'C'; // Commencez à partir de la colonne E pour les données
    foreach (range('B', 'Q') as $columnID) {
        $sheet->getColumnDimension($columnID)->setWidth(15); // Définir la largeur à 15 pour chaque colonne
    }
    $cellRangesByColor = [
        'c0504d' => [ // red
            'B25:O25', 'B5:O5'
        ],
        '4f81bd' => [ //bleu
            'B24:O24', 'B4:O4'
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
        'B3:O6', 'B23:O26'
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
    $sheet->setCellValue('B3', 'Volume');
    $sheet->setCellValue('B4', 'MPPA');
    $sheet->setCellValue('B5', 'MABC');
    $sheet->setCellValue('B6', 'TOTAL');
    $sheet->setCellValue('O3', 'TOTAL');

    $sheet->setCellValue('B23', 'Valeur (HT)');
    $sheet->setCellValue('B24', 'MPPA');
    $sheet->setCellValue('B25', 'MABC');
    $sheet->setCellValue('B26', 'TOTAL');
    $sheet->setCellValue('O23', 'TOTAL');

    // Insérer les mois en première ligne
    foreach ($mois as $index => $moi) {
        $sheet->setCellValue($col . '3', $moi);
        $sheet->setCellValue($col . '4', $datasets1[$index % count($datasets1)]);
        $sheet->setCellValue($col . '5', $datasets2[$index % count($datasets2)]);
        $sheet->setCellValue($col . '6', $datasets1[$index % count($datasets1)] + $datasets2[$index % count($datasets2)]);
        $sheet->setCellValue($col . '23', $moi); // Insérer le mois
        $sheet->setCellValue($col . '24', $datasets3[$index % count($datasets3)]); // Valeur de datasets1
        $sheet->setCellValue($col . '25', $datasets4[$index % count($datasets4)]); // Valeur de datasets2
        $sheet->setCellValue($col . '26', $datasets3[$index % count($datasets3)] + $datasets4[$index % count($datasets4)]); // Valeur de datasets2
        $col++; // Passer à la colonne suivante pour le mois suivant
    }
    $sheet->setCellValue('O4', '=SUM(C4:N4)');
    $sheet->setCellValue('O5', '=SUM(C5:N5)');
    $sheet->setCellValue('O6', '=SUM(C6:N6)');
    $sheet->setCellValue('O24', '=SUM(C24:N24)');
    $sheet->setCellValue('O25', '=SUM(C25:N25)');
    $sheet->setCellValue('O26', '=SUM(C26:N26)');

    $dataSeriesLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$4', null, 12),
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$5', null, 12), // Mois
    ];
    $xAxisTickValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$2:$N$2', null, 12), // 'Valeurs'
    ];
    $dataSeriesValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$4:$N$4', null, 12), // Valeurs pour datasets1
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$5:$N$5', null, 12), // Valeurs pour datasets2
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
    $chart->setBottomRightPosition('P22');

    //valeur

    $dataSeriesValues2 = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$24:$N$24', null, 12), // Valeurs
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$25:$N$25', null, 12), // Valeurs pour datasets2
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
    $chart2->setTopLeftPosition('B27');
    $chart2->setBottomRightPosition('P42');

    $sheet->addChart($chart);
    $sheet->addChart($chart2);
    $filePath = $projectDir . '/public/nom_de_fichier.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->setIncludeCharts(true);
    $writer->save($filePath);

    return $filePath; // ou retournez un objet BinaryFileResponse si vous le souhaitez
}


}