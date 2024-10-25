<?php 

namespace App\Service\Statistic\VolVal;

use Dompdf\Dompdf;
use App\Repository\AchatRepository;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\BrowserKit\Request;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateExcelVolVal extends AbstractController
{
    private $achatRepository;
    private $projectDir;
    private $request;
    private $chartBuilder;

    public function __construct(AchatRepository $achatRepository, ChartBuilderInterface $chartBuilder, KernelInterface $kernel, private RequestStack $requestStack)
    {
        $this->achatRepository = $achatRepository;
        $this->chartBuilder = $chartBuilder;
        $this->projectDir = $kernel->getProjectDir();
    }

    public function generateExcelFile($chartDataCountCurrent, $chartDataCountPrevious, $chartDataTotalCurrent, $chartDataTotalPrevious, $totalAchatPerMonthUnder2K, $anne_precedente, $projectDir)
    {
        $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $includePreviousYear = $anne_precedente == 'anneePrecedente';
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('H1', 'Activités appro en volume/valeur ')
              ->getStyle('H1')
              ->getFont()
              ->setBold(true)
              ->setSize(18)
              ->setColor(new Color(Color::COLOR_RED));

        foreach (range('B', 'Q') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(15);
        }

        $cellBorder = ['B3:O6', 'B13:O16', 'B20:O23'];
        if ($includePreviousYear) {
            $cellBorder = array_merge($cellBorder, ['B7:O9', 'B17:O19', 'B24:O26']);
        }

        $styleBorderB = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        foreach ($cellBorder as $cellRange) {
            $sheet->getStyle($cellRange)->applyFromArray($styleBorderB);
        }

        // Tableaux pour les volumes
        $sheet->setCellValue('B3', 'Volume');
        $sheet->setCellValue('B4', 'MPPA (Année en cours)');
        $sheet->setCellValue('B5', 'MABC (Année en cours)');
        $sheet->setCellValue('B6', 'TOTAL (Année en cours)');
        if ($includePreviousYear) {
            $sheet->setCellValue('B7', 'MPPA (Année Précédente)');
            $sheet->setCellValue('B8', 'MABC (Année Précédente)');
            $sheet->setCellValue('B9', 'TOTAL (Année Précédente)');
        }
        $sheet->setCellValue('O3', 'TOTAL');

        // Tableaux pour les valeurs (HT)
        $sheet->setCellValue('B13', 'Valeur (HT)');
        $sheet->setCellValue('B14', 'MPPA (Année en cours)');
        $sheet->setCellValue('B15', 'MABC (Année en cours)');
        $sheet->setCellValue('B16', 'TOTAL (Année en cours)');
        if ($includePreviousYear) {
            $sheet->setCellValue('B17', 'MPPA (Année Précédente)');
            $sheet->setCellValue('B18', 'MABC (Année Précédente)');
            $sheet->setCellValue('B19', 'TOTAL (Année Précédente)');
        }
        $sheet->setCellValue('O13', 'TOTAL');

        // Insérer les mois en première ligne et calculer les cumulatives
        $col = 'C';
        $cumulativeTotalCurrent = 0;
        $cumulativeTotalPrevious = 0;

        foreach ($mois as $index => $moi) {
            // Volumes
            $sheet->setCellValue($col . '3', $moi);
            $sheet->setCellValue($col . '4', isset($chartDataCountCurrent['mppa'][$index]) ? $chartDataCountCurrent['mppa'][$index] : 0);
            $sheet->setCellValue($col . '5', isset($chartDataCountCurrent['mabc'][$index]) ? $chartDataCountCurrent['mabc'][$index] : 0);
            $sheet->setCellValue($col . '6', (isset($chartDataCountCurrent['mppa'][$index]) ? $chartDataCountCurrent['mppa'][$index] : 0) + (isset($chartDataCountCurrent['mabc'][$index]) ? $chartDataCountCurrent['mabc'][$index] : 0));

            if ($includePreviousYear && isset($chartDataCountPrevious['mppa'][$index], $chartDataCountPrevious['mabc'][$index])) {
                $sheet->setCellValue($col . '7', $chartDataCountPrevious['mppa'][$index]);
                $sheet->setCellValue($col . '8', $chartDataCountPrevious['mabc'][$index]);
                $sheet->setCellValue($col . '9', $chartDataCountPrevious['mppa'][$index] + $chartDataCountPrevious['mabc'][$index]);
            }

            // Valeurs (HT)
            $sheet->setCellValue($col . '13', $moi);
            $sheet->setCellValue($col . '14', isset($chartDataTotalCurrent['mppa'][$index]) ? $chartDataTotalCurrent['mppa'][$index] : 0);
            $sheet->setCellValue($col . '15', isset($chartDataTotalCurrent['mabc'][$index]) ? $chartDataTotalCurrent['mabc'][$index] : 0);
            $cumulativeTotalCurrent += (isset($chartDataTotalCurrent['mppa'][$index]) ? $chartDataTotalCurrent['mppa'][$index] : 0) + (isset($chartDataTotalCurrent['mabc'][$index]) ? $chartDataTotalCurrent['mabc'][$index] : 0);
            $sheet->setCellValue($col . '16', $cumulativeTotalCurrent);

            if ($includePreviousYear && isset($chartDataTotalPrevious['mppa'][$index], $chartDataTotalPrevious['mabc'][$index])) {
                $sheet->setCellValue($col . '17', $chartDataTotalPrevious['mppa'][$index]);
                $sheet->setCellValue($col . '18', $chartDataTotalPrevious['mabc'][$index]);
                $cumulativeTotalPrevious += $chartDataTotalPrevious['mppa'][$index] + $chartDataTotalPrevious['mabc'][$index];
                $sheet->setCellValue($col . '19', $cumulativeTotalPrevious);
            }

            $col++;
        }

        // Somme des totaux
        $sheet->setCellValue('O4', '=SUM(C4:N4)');
        $sheet->setCellValue('O5', '=SUM(C5:N5)');
        $sheet->setCellValue('O6', '=SUM(C6:N6)');
        if ($includePreviousYear) {
            $sheet->setCellValue('O7', '=SUM(C7:N7)');
            $sheet->setCellValue('O8', '=SUM(C8:N8)');
            $sheet->setCellValue('O9', '=SUM(C9:N9)');
        }
        $sheet->setCellValue('O14', '=SUM(C14:N14)');
        $sheet->setCellValue('O15', '=SUM(C15:N15)');
        $sheet->setCellValue('O16', '=SUM(C16:N16)');
        if ($includePreviousYear) {
            $sheet->setCellValue('O17', '=SUM(C17:N17)');
            $sheet->setCellValue('O18', '=SUM(C18:N18)');
            $sheet->setCellValue('O19', '=SUM(C19:N19)');
        }

        // Dossiers inférieurs à 2 000,00 € TTC
        $rowStart = $includePreviousYear ? 22 : 20;
        $sheet->setCellValue('B' . $rowStart, 'Dossiers inférieurs à 2 000,00 € TTC');
        $sheet->setCellValue('B' . ($rowStart + 1), 'MPPA (Année en cours)');
        $sheet->setCellValue('B' . ($rowStart + 2), 'MABC (Année en cours)');
        $sheet->setCellValue('B' . ($rowStart + 3), 'TOTAL (Année en cours)');

        if ($includePreviousYear) {
            $sheet->setCellValue('B' . ($rowStart + 4), 'MPPA (Année Précédente)');
            $sheet->setCellValue('B' . ($rowStart + 5), 'MABC (Année Précédente)');
            $sheet->setCellValue('B' . ($rowStart + 6), 'TOTAL (Année Précédente)');
        }

        $col = 'C';

        foreach ($mois as $index => $moi) {
            $sheet->setCellValue($col . $rowStart, $moi);

            // Vérifier si les données de l'année en cours existent
            if (isset($totalAchatPerMonthUnder2K['type_marche_1']['current_year'][$index], $totalAchatPerMonthUnder2K['type_marche_0']['current_year'][$index])) {
                $sheet->setCellValue($col . ($rowStart + 1), $totalAchatPerMonthUnder2K['type_marche_1']['current_year'][$index]['count']);
                $sheet->setCellValue($col . ($rowStart + 2), $totalAchatPerMonthUnder2K['type_marche_0']['current_year'][$index]['count']);
                $sheet->setCellValue($col . ($rowStart + 3), $totalAchatPerMonthUnder2K['type_marche_1']['current_year'][$index]['count'] + $totalAchatPerMonthUnder2K['type_marche_0']['current_year'][$index]['count']);
            }

            // Vérifier si les données de l'année précédente existent
            if ($includePreviousYear && isset($totalAchatPerMonthUnder2K['type_marche_1']['previous_year'][$index], $totalAchatPerMonthUnder2K['type_marche_0']['previous_year'][$index])) {
                $sheet->setCellValue($col . ($rowStart + 4), $totalAchatPerMonthUnder2K['type_marche_1']['previous_year'][$index]['count']);
                $sheet->setCellValue($col . ($rowStart + 5), $totalAchatPerMonthUnder2K['type_marche_0']['previous_year'][$index]['count']);
                $sheet->setCellValue($col . ($rowStart + 6), $totalAchatPerMonthUnder2K['type_marche_1']['previous_year'][$index]['count'] + $totalAchatPerMonthUnder2K['type_marche_0']['previous_year'][$index]['count']);
            }

            $col++;
        }

        // Somme des totaux pour les Dossiers inférieurs à 2 000,00 € TTC
        $sheet->setCellValue('O' . ($rowStart + 1), '=SUM(C' . ($rowStart + 1) . ':N' . ($rowStart + 1) . ')');
        $sheet->setCellValue('O' . ($rowStart + 2), '=SUM(C' . ($rowStart + 2) . ':N' . ($rowStart + 2) . ')');
        $sheet->setCellValue('O' . ($rowStart + 3), '=SUM(C' . ($rowStart + 3) . ':N' . ($rowStart + 3) . ')');
        if ($includePreviousYear) {
            $sheet->setCellValue('O' . ($rowStart + 4), '=SUM(C' . ($rowStart + 4) . ':N' . ($rowStart + 4) . ')');
            $sheet->setCellValue('O' . ($rowStart + 5), '=SUM(C' . ($rowStart + 5) . ':N' . ($rowStart + 5) . ')');
            $sheet->setCellValue('O' . ($rowStart + 6), '=SUM(C' . ($rowStart + 6) . ':N' . ($rowStart + 6) . ')');
        }

        // Graphiques : volumes et valeurs
        $dataSeriesLabelsVolume = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$4', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$5', null, 1)
        ];

        if ($includePreviousYear) {
            $dataSeriesLabelsVolume[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$7', null, 1);
            $dataSeriesLabelsVolume[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$8', null, 1);
        }

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$3:$N$3', null, 12)
        ];

        $dataSeriesValuesVolume = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$4:$N$4', null, 12),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$5:$N$5', null, 12)
        ];

        if ($includePreviousYear) {
            $dataSeriesValuesVolume[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$7:$N$7', null, 12);
            $dataSeriesValuesVolume[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$8:$N$8', null, 12);
        }

        $seriesVolume = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValuesVolume) - 1),
            $dataSeriesLabelsVolume,
            $xAxisTickValues,
            $dataSeriesValuesVolume
        );

        $layoutVolume = new Layout([
            'showVal' => true,
            'showCatName' => false
        ]);

        $plotAreaVolume = new PlotArea($layoutVolume, [$seriesVolume]);
        $legendVolume = new Legend(Legend::POSITION_RIGHT, null, false);
        $titleVolume = new Title('Activité appro en volume');

        $chartVolume = new Chart(
            'chartVolume',
            $titleVolume,
            $legendVolume,
            $plotAreaVolume
        );

        $chartVolume->setTopLeftPosition('B30');
        $chartVolume->setBottomRightPosition('P50');

        // Graphique pour les valeurs cumulées
        $dataSeriesLabelsValue = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$16', null, 1)
        ];

        if ($includePreviousYear) {
            $dataSeriesLabelsValue[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$19', null, 1);
        }

        $dataSeriesValuesValue = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$16:$N$16', null, 12)
        ];

        if ($includePreviousYear) {
            $dataSeriesValuesValue[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$19:$N$19', null, 12);
        }

        $seriesValue = new DataSeries(
            DataSeries::TYPE_LINECHART,
            null,
            range(0, count($dataSeriesValuesValue) - 1),
            $dataSeriesLabelsValue,
            $xAxisTickValues,
            $dataSeriesValuesValue
        );

        $layoutValue = new Layout([
            'showVal' => true,
            'showCatName' => false
        ]);

        $plotAreaValue = new PlotArea($layoutValue, [$seriesValue]);
        $legendValue = new Legend(Legend::POSITION_RIGHT, null, false);
        $titleValue = new Title('Activité appro en valeur cumulative (HT)');

        $chartValue = new Chart(
            'chartValue',
            $titleValue,
            $legendValue,
            $plotAreaValue
        );

        $chartValue->setTopLeftPosition('B51');
        $chartValue->setBottomRightPosition('P71');

        $sheet->addChart($chartVolume);
        $sheet->addChart($chartValue);

        // Styles pour les cellules
        $styleArrayMPPA_Current = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4f81bd']
            ]
        ];
        $styleArrayMABC_Current = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'c0504d']
            ]
        ];
        $styleArrayMPPA_Previous = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9bbb59']
            ]
        ];
        $styleArrayMABC_Previous = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '8064a2']
            ]
        ];
        $styleArrayTotal_Current = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '8ebaee']
            ]
        ];
        $styleArrayTotal_Previous = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'b7cc8a']
            ]
        ];

        // Appliquer les styles aux cellules
        $sheet->getStyle('C4:N4')->applyFromArray($styleArrayMPPA_Current);
        $sheet->getStyle('C5:N5')->applyFromArray($styleArrayMABC_Current);
        $sheet->getStyle('C6:N6')->applyFromArray($styleArrayTotal_Current);
        $sheet->getStyle('C14:N14')->applyFromArray($styleArrayMPPA_Current);
        $sheet->getStyle('C15:N15')->applyFromArray($styleArrayMABC_Current);
        $sheet->getStyle('C16:N16')->applyFromArray($styleArrayTotal_Current);

        if ($includePreviousYear) {
            $sheet->getStyle('C7:N7')->applyFromArray($styleArrayMPPA_Previous);
            $sheet->getStyle('C8:N8')->applyFromArray($styleArrayMABC_Previous);
            $sheet->getStyle('C9:N9')->applyFromArray($styleArrayTotal_Previous);
            $sheet->getStyle('C17:N17')->applyFromArray($styleArrayMPPA_Previous);
            $sheet->getStyle('C18:N18')->applyFromArray($styleArrayMABC_Previous);
            $sheet->getStyle('C19:N19')->applyFromArray($styleArrayTotal_Previous);
        }

        // Appliquer les styles aux dossiers inférieurs à 2 000,00 € TTC
        $sheet->getStyle('C' . ($rowStart + 1) . ':N' . ($rowStart + 1))->applyFromArray($styleArrayMPPA_Current);
        $sheet->getStyle('C' . ($rowStart + 2) . ':N' . ($rowStart + 2))->applyFromArray($styleArrayMABC_Current);
        $sheet->getStyle('C' . ($rowStart + 3) . ':N' . ($rowStart + 3))->applyFromArray($styleArrayTotal_Current);

        if ($includePreviousYear) {
            $sheet->getStyle('C' . ($rowStart + 4) . ':N' . ($rowStart + 4))->applyFromArray($styleArrayMPPA_Previous);
            $sheet->getStyle('C' . ($rowStart + 5) . ':N' . ($rowStart + 5))->applyFromArray($styleArrayMABC_Previous);
            $sheet->getStyle('C' . ($rowStart + 6) . ':N' . ($rowStart + 6))->applyFromArray($styleArrayTotal_Previous);
        }

        // Sauvegarder le fichier Excel
        $filePath = $projectDir . '/public/nom_de_fichier.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($filePath);

        return $filePath;
    }
}
