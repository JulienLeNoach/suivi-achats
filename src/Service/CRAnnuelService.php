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

    public function generateExcelFile($chartDataCountCurrent, $chartDataCountPrevious, $chartDataTotalCurrent, $chartDataTotalPrevious, $projectDir,$achats, $achats_delay_all,
                                    $result_achats, $result_achats_mounts, $parameter,$result_achatsPME, $result_achatsSum, $result_achatsSumVol, $result_achatsSumVal,$totalAchatPerMonthUnder2K,$selectedYear)
    {
        $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('H1', 'COMPTE RENDU ANNUEL')
     ->getStyle('H1')
     ->getFont()
     ->setBold(true)
     ->setSize(18);
     $sheet->setCellValue('A1', 'Année sélectionnée : ' . $selectedYear)
     ->getStyle('A1')
     ->getFont()
     ->setBold(true);
foreach (range('B', 'Q') as $columnID) {
   $sheet->getColumnDimension($columnID)->setWidth(15);
}

$includePreviousYear = true;

// Définir le style de bordure
$styleBorder = [
   'borders' => [
       'allBorders' => [
           'borderStyle' => Border::BORDER_THIN,
           'color' => ['argb' => '00000000'],
       ],
   ],
];

// Appliquer les bordures aux plages de cellules
$cellRanges = [
   'B3:O6', 'B11:O14', 'B18:O25', 'B28:O35',
 'B151:E156', 'B159:E161', 'G159:J161',
   'L159:O161', 
   'B113:E118', 'B121:E123', 'G121:J123', 'L121:O123',
    'B142:E147', 'I145:O147', 'B167:O169'
];

foreach ($cellRanges as $range) {
   $sheet->getStyle($range)->applyFromArray($styleBorder);
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
$sheet->setCellValue('B11', 'Valeur (HT)');
$sheet->setCellValue('B12', 'MPPA (Année en cours)');
$sheet->setCellValue('B13', 'MABC (Année en cours)');
$sheet->setCellValue('B14', 'TOTAL (Année en cours)');
if ($includePreviousYear) {
   $sheet->setCellValue('B15', 'MPPA (Année Précédente)');
   $sheet->setCellValue('B16', 'MABC (Année Précédente)');
   $sheet->setCellValue('B17', 'TOTAL (Année Précédente)');
}
$sheet->setCellValue('O11', 'TOTAL');

// Tableaux pour les dossiers inférieurs à 2 000,00 € TTC
$sheet->setCellValue('B18', 'Dossiers inférieurs à 2 000,00 € TTC');
$sheet->setCellValue('B19', 'MPPA (Année en cours)');
$sheet->setCellValue('B20', 'MABC (Année en cours)');
$sheet->setCellValue('B21', 'TOTAL (Année en cours)');
if ($includePreviousYear) {
   $sheet->setCellValue('B22', 'MPPA (Année Précédente)');
   $sheet->setCellValue('B23', 'MABC (Année Précédente)');
   $sheet->setCellValue('B24', 'TOTAL (Année Précédente)');
}
$sheet->setCellValue('O18', 'TOTAL');

// Insérer les mois en première ligne et calculer les cumulatives
$col = 'C';
$cumulativeTotalCurrent = 0;
$cumulativeTotalPrevious = 0;
foreach ($mois as $index => $moi) {
   // Volumes
   $sheet->setCellValue($col . '3', $moi);
   $sheet->setCellValue($col . '4', $chartDataCountCurrent['mppa'][$index]);
   $sheet->setCellValue($col . '5', $chartDataCountCurrent['mabc'][$index]);
   $sheet->setCellValue($col . '6', $chartDataCountCurrent['mppa'][$index] + $chartDataCountCurrent['mabc'][$index]);
   if ($includePreviousYear) {
       $sheet->setCellValue($col . '7', $chartDataCountPrevious['mppa'][$index]);
       $sheet->setCellValue($col . '8', $chartDataCountPrevious['mabc'][$index]);
       $sheet->setCellValue($col . '9', $chartDataCountPrevious['mppa'][$index] + $chartDataCountPrevious['mabc'][$index]);
   }

   // Valeurs (HT)
   $sheet->setCellValue($col . '11', $moi);
   $sheet->setCellValue($col . '12', $chartDataTotalCurrent['mppa'][$index]);
   $sheet->setCellValue($col . '13', $chartDataTotalCurrent['mabc'][$index]);
   $cumulativeTotalCurrent += $chartDataTotalCurrent['mppa'][$index] + $chartDataTotalCurrent['mabc'][$index];
   $sheet->setCellValue($col . '14', $cumulativeTotalCurrent);
   if ($includePreviousYear) {
       $sheet->setCellValue($col . '15', $chartDataTotalPrevious['mppa'][$index]);
       $sheet->setCellValue($col . '16', $chartDataTotalPrevious['mabc'][$index]);
       $cumulativeTotalPrevious += $chartDataTotalPrevious['mppa'][$index] + $chartDataTotalPrevious['mabc'][$index];
       $sheet->setCellValue($col . '17', $cumulativeTotalPrevious);
   }
   if ($totalAchatPerMonthUnder2K['type_marche_1']['current_year']) {
   // Dossiers inférieurs à 2 000,00 € TTC
   $sheet->setCellValue($col . '18', $moi);
   $sheet->setCellValue($col . '19', $totalAchatPerMonthUnder2K['type_marche_1']['current_year'][$index]['count']);
   $sheet->setCellValue($col . '20', $totalAchatPerMonthUnder2K['type_marche_0']['current_year'][$index]['count']);
   $sheet->setCellValue($col . '21', $totalAchatPerMonthUnder2K['type_marche_1']['current_year'][$index]['count'] + $totalAchatPerMonthUnder2K['type_marche_0']['current_year'][$index]['count']);
   }
   if ($includePreviousYear) {
       $sheet->setCellValue($col . '22', $totalAchatPerMonthUnder2K['type_marche_1']['previous_year'][$index]['count']);
       $sheet->setCellValue($col . '23', $totalAchatPerMonthUnder2K['type_marche_0']['previous_year'][$index]['count']);
       $sheet->setCellValue($col . '24', $totalAchatPerMonthUnder2K['type_marche_1']['previous_year'][$index]['count'] + $totalAchatPerMonthUnder2K['type_marche_0']['previous_year'][$index]['count']);
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
$sheet->setCellValue('O12', '=SUM(C12:N12)');
$sheet->setCellValue('O13', '=SUM(C13:N13)');
$sheet->setCellValue('O14', '=SUM(C14:N14)');
if ($includePreviousYear) {
   $sheet->setCellValue('O15', '=SUM(C15:N15)');
   $sheet->setCellValue('O16', '=SUM(C16:N16)');
   $sheet->setCellValue('O17', '=SUM(C17:N17)');
}
$sheet->setCellValue('O19', '=SUM(C19:N19)');
$sheet->setCellValue('O20', '=SUM(C20:N20)');
$sheet->setCellValue('O21', '=SUM(C21:N21)');
if ($includePreviousYear) {
   $sheet->setCellValue('O22', '=SUM(C22:N22)');
   $sheet->setCellValue('O23', '=SUM(C23:N23)');
   $sheet->setCellValue('O24', '=SUM(C24:N24)');
}

// Ajouter les graphiques pour les volumes, valeurs et dossiers inférieurs à 2 000,00 € TTC
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

$chartVolume->setTopLeftPosition('B26');
$chartVolume->setBottomRightPosition('P45');

// Ajouter le graphique pour les valeurs cumulatives
$dataSeriesLabelsValue = [
   new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$12', null, 1)
];
if ($includePreviousYear) {
   $dataSeriesLabelsValue[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$15', null, 1);
}

$dataSeriesValuesValue = [
   new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$14:$N$14', null, 12)
];
if ($includePreviousYear) {
   $dataSeriesValuesValue[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$17:$N$17', null, 12);
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

$chartValue->setTopLeftPosition('B46');
$chartValue->setBottomRightPosition('P65');

$sheet->addChart($chartVolume);
$sheet->addChart($chartValue);

// Coloriser les cases selon les couleurs utilisées dans le graphique
$styleArrayMPPA_Current = [
   'fill' => [
       'fillType' => Fill::FILL_SOLID,
       'startColor' => ['rgb' => '4f81bd'] // Bleu pour MPPA Année en cours
   ]
];
$styleArrayMABC_Current = [
   'fill' => [
       'fillType' => Fill::FILL_SOLID,
       'startColor' => ['rgb' => 'c0504d'] // Rouge pour MABC Année en cours
   ]
];
$styleArrayMPPA_Previous = [
   'fill' => [
       'fillType' => Fill::FILL_SOLID,
       'startColor' => ['rgb' => '9bbb59'] // Vert pour MPPA Année Précédente
   ]
];
$styleArrayMABC_Previous = [
   'fill' => [
       'fillType' => Fill::FILL_SOLID,
       'startColor' => ['rgb' => '8064a2'] // Violet pour MABC Année Précédente
   ]
];
$styleArrayTotal_Current = [
   'fill' => [
       'fillType' => Fill::FILL_SOLID,
       'startColor' => ['rgb' => '8ebaee'] // Bleu clair pour TOTAL Année en cours
   ]
];
$styleArrayTotal_Previous = [
   'fill' => [
       'fillType' => Fill::FILL_SOLID,
       'startColor' => ['rgb' => 'b7cc8a'] // Rouge clair pour TOTAL Année Précédente
   ]
];

// Appliquer les styles aux cellules correspondantes
$sheet->getStyle('C4:N4')->applyFromArray($styleArrayMPPA_Current);
$sheet->getStyle('C5:N5')->applyFromArray($styleArrayMABC_Current);
$sheet->getStyle('C6:N6')->applyFromArray($styleArrayTotal_Current);
$sheet->getStyle('C12:N12')->applyFromArray($styleArrayMPPA_Current);
$sheet->getStyle('C13:N13')->applyFromArray($styleArrayMABC_Current);
$sheet->getStyle('C14:N14')->applyFromArray($styleArrayTotal_Current);
$sheet->getStyle('C19:N19')->applyFromArray($styleArrayMPPA_Current);
$sheet->getStyle('C20:N20')->applyFromArray($styleArrayMABC_Current);
$sheet->getStyle('C21:N21')->applyFromArray($styleArrayTotal_Current);

if ($includePreviousYear) {
   $sheet->getStyle('C7:N7')->applyFromArray($styleArrayMPPA_Previous);
   $sheet->getStyle('C8:N8')->applyFromArray($styleArrayMABC_Previous);
   $sheet->getStyle('C9:N9')->applyFromArray($styleArrayTotal_Previous);
   $sheet->getStyle('C15:N15')->applyFromArray($styleArrayMPPA_Previous);
   $sheet->getStyle('C16:N16')->applyFromArray($styleArrayMABC_Previous);
   $sheet->getStyle('C17:N17')->applyFromArray($styleArrayTotal_Previous);
   $sheet->getStyle('C22:N22')->applyFromArray($styleArrayMPPA_Previous);
   $sheet->getStyle('C23:N23')->applyFromArray($styleArrayMABC_Previous);
   $sheet->getStyle('C24:N24')->applyFromArray($styleArrayTotal_Previous);
}

                //-------------------------- Delay chart -------------------------------- // 
                $sheet->setCellValue('H67', "Délai d'activité annuelle")
      ->getStyle('H67')
      ->getFont()
      ->setSize(16)
      ->setColor(new Color(Color::COLOR_RED));


$mois = ['Délai', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre', 'TOTAL'];
$col = 68; // décalé de 68 lignes (62 + 6)
$colmonth = 'B';
foreach (range('A', 'Q') as $columnID) {
    $sheet->getColumnDimension($columnID)->setWidth(15); // Définir la largeur à 15 pour chaque colonne
}
for ($j = 0; $j <= 13; $j++) {
    $sheet->setCellValue($colmonth . 68, $mois[$j]); // ligne initiale 62 décalée de 6
    $colmonth++;
}

$cellRangesByColor = [
    'c0504d' => [ // rouge
        'C76','C75', 'I75','I76', 'O75','O76', 'C92','C93' // décalé de 6 lignes
    ],
    '4f81bd' => [ // bleu
        'B76','B75', 'H75','H76', 'N75','N76', 'B92','B93' // décalé de 6 lignes
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
    'B68:O72','A76','G76','M76','A93','B75:C76','H75:I76','N75:O76','N91:O92','B92:C93' // décalé de 6 lignes
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

//-------------------------- Transmission chart -------------------------------- // 
$sheet->setCellValue('A76', 'Transmission'); // A70 -> A76
$sheet->setCellValue('B75', '<= 3j / ' .$achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"]. "%"); // B69 -> B75
$sheet->setCellValue('C75', '> 3j / '.$achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"] . "%");  // C69 -> C75
$sheet->setCellValue('B76', $achats_delay_all[0]["CountAntInf3"]);  // B70 -> B76
$sheet->setCellValue('C76', $achats_delay_all[0]["CountAntSup3"]);  // C70 -> C76

$antLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$75', null, 12), // A69 -> A75
];

$antValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$76:$C$76', null, 2), // B70:C70 -> B76:C76
];

$antxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$75:$C$75', null, 4), // B69:C69 -> B75:C75
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
        
$antChart->setTopLeftPosition('B77'); // B71 -> B77
$antChart->setBottomRightPosition('F90'); // F84 -> F90
$sheet->addChart($antChart);

//-------------------------- Traitement chart -------------------------------- // 
$sheet->setCellValue('G76', 'Traitement'); // G70 -> G76
$sheet->setCellValue('H75', '<= 3j / ' . $achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"] . "%"); // H69 -> H75
$sheet->setCellValue('I75', '> 3j / ' . $achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"] . "%"); // I69 -> I75
$sheet->setCellValue('H76', $achats_delay_all[1]["CountBudgetInf3"]); // H70 -> H76
$sheet->setCellValue('I76', $achats_delay_all[1]["CountBudgetSup3"]); // I70 -> I76

$budgetLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$75', null, 12), // G69 -> G75
];

$budgetValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$76:$I$76', null, 2), // H70:I70 -> H76:I76
];

$budgetxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$75:$I$75', null, 4), // H69:I69 -> H75:I75
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

$budgetChart->setTopLeftPosition('H77'); // H71 -> H77
$budgetChart->setBottomRightPosition('L90'); // L84 -> L90
$sheet->addChart($budgetChart);

//-------------------------- Notification chart -------------------------------- // 
$sheet->setCellValue('M76', 'Notification'); // M70 -> M76
$sheet->setCellValue('N75', '<= 7j / ' .$achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"]. "%"); // N69 -> N75
$sheet->setCellValue('O75', '> 7j / '.$achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"]. "%");  // O69 -> O75
$sheet->setCellValue('N76', $achats_delay_all[2]["CountApproInf7"]); // N70 -> N76
$sheet->setCellValue('O76', $achats_delay_all[2]["CountApproSup7"]); // O70 -> O76

$approLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$75', null, 12), // M69 -> M75
];

$approValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$76:$O$76', null, 2), // N70:O70 -> N76:O76
];

$approxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$75:$O$75', null, 4), // N69:O69 -> N75:O75
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

$approChart->setTopLeftPosition('N77'); // N71 -> N77
$approChart->setBottomRightPosition('R90'); // R84 -> R90
$sheet->addChart($approChart);

//-------------------------- Total chart -------------------------------- // 
$sheet->setCellValue('A93', 'Délai total'); // A87 -> A93
$sheet->setCellValue('B92', '<= 15j / ' .$achats_delay_all[3]["Pourcentage_Delai_Inf_15_Jours"]. "%"); // B86 -> B92
$sheet->setCellValue('C92', '> à 15j / '.$achats_delay_all[3]["Pourcentage_Delai_Sup_15_Jours"]. "%");  // C86 -> C92
$sheet->setCellValue('B93', $achats_delay_all[3]["CountDelaiTotalInf15"]); // B87 -> B93
$sheet->setCellValue('C93', $achats_delay_all[3]["CountDelaiTotalSup15"]); // C87 -> C93

$totalLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$92', null, 12), // A86 -> A92
];

$totalValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$93:$C$93', null, 2), // B87:C87 -> B93:C93
];

$totalxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$92:$C$92', null, 4), // B86:C86 -> B92:C92
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
       
$totalChart->setTopLeftPosition('B94'); // B88 -> B94
$totalChart->setBottomRightPosition('F110'); // F104 -> F110
$sheet->addChart($totalChart);



//------------------------------ Tab % vol/val_type --------------------------//

$sheet->setCellValue('H111', 'Activités par type de marché') // H151 -> H111
      ->getStyle('H111') // H151 -> H111
      ->getFont()
      ->setSize(16)
      ->setColor(new Color(Color::COLOR_RED));
$sheet->setCellValue('C113', "MPPA"); // C153 -> C113
$sheet->setCellValue('D113', "MABC"); // D153 -> D113
$sheet->setCellValue('E113', "TOTAUX"); // E153 -> E113
$sheet->setCellValue('B114', "VALEUR"); // B154 -> B114
$sheet->setCellValue('B115', "NOMBRE"); // B155 -> B115
$sheet->setCellValue('B116', "MOYENNE"); // B156 -> B116
$sheet->setCellValue('B117', "% VALEUR"); // B157 -> B117
$sheet->setCellValue('B118', "% VOLUME"); // B158 -> B118
$sheet->setCellValue('C114', $result_achats[0]["somme_montant_type_1"]); // C155 -> C115
$sheet->setCellValue('D114', $result_achats[1]["somme_montant_type_0"]); // D154 -> D114
$sheet->setCellValue('E114', $result_achats[1]["somme_montant_type_0"] + $result_achats[0]["somme_montant_type_1"]); // E154 -> E114
$sheet->setCellValue('C115', $result_achats[0]["nombre_achats_type_1"]); // C155 -> C115
$sheet->setCellValue('D115', $result_achats[1]["nombre_achats_type_0"]); // D155 -> D115
$sheet->setCellValue('E115', $result_achats[1]["nombre_achats_type_0"] +  $result_achats[0]["nombre_achats_type_1"]); // E155 -> E115
$sheet->setCellValue('C116', $result_achats[0]["moyenne_montant_type_1"]); // C156 -> C116
$sheet->setCellValue('D116', $result_achats[1]["moyenne_montant_type_0"]); // D156 -> D116
$sheet->setCellValue('E116', $result_achats[1]["moyenne_montant_type_0"] +  $result_achats[0]["moyenne_montant_type_1"]); // E156 -> E116
$sheet->setCellValue('C117', $result_achats[0]["pourcentage_type_1_total"]); // C157 -> C117
$sheet->setCellValue('D117', $result_achats[1]["pourcentage_type_0_total"]); // D157 -> D117
$sheet->setCellValue('C118', $result_achats[0]["pourcentage_type_1"]); // C158 -> C118
$sheet->setCellValue('D118', $result_achats[1]["pourcentage_type_0"]); // D158 -> D118

//------------------------------ Tab montant_type --------------------------//
$sheet->setCellValue('C121', "Montant des MPPA"); // C161 -> C121
$sheet->setCellValue('B122', "X <= ". $parameter[0]->getFour1()); // B162 -> B122
$sheet->setCellValue('C122', $parameter[0]->getFour1()." < X <=".$parameter[0]->getFour2()); // C162 -> C122
$sheet->setCellValue('D122',  $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); // D162 -> D122
$sheet->setCellValue('E122', "X > ". $parameter[0]->getFour3()); // E162 -> E122
$sheet->setCellValue('B123', $result_achats_mounts[0]["nombre_achats_inf_four1"]); // B163 -> B123
$sheet->setCellValue('C123', $result_achats_mounts[0]["nombre_achats_four1_four2"]); // C163 -> C123
$sheet->setCellValue('D123',  $result_achats_mounts[0]["nombre_achats_four2_four3"]); // D163 -> D123
$sheet->setCellValue('E123', $result_achats_mounts[0]["nombre_achats_sup_four3"]); // E163 -> E123

$sheet->setCellValue('H121', "Montant des MABC"); // H161 -> H121
$sheet->setCellValue('G122', "X <= ". $parameter[0]->getFour1()); // G162 -> G122
$sheet->setCellValue('H122', $parameter[0]->getFour1()." < X <=".$parameter[0]->getFour2()); // H162 -> H122
$sheet->setCellValue('I122',  $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); // I162 -> I122
$sheet->setCellValue('J122', "X > ". $parameter[0]->getFour3()); // J162 -> J122
$sheet->setCellValue('G123', $result_achats_mounts[1]["nombre_achats_inf_four1"]); // G163 -> G123
$sheet->setCellValue('H123', $result_achats_mounts[1]["nombre_achats_four1_four2"]); // H163 -> H123
$sheet->setCellValue('I123',  $result_achats_mounts[1]["nombre_achats_four2_four3"]); // I163 -> I123
$sheet->setCellValue('J123', $result_achats_mounts[1]["nombre_achats_sup_four3"]); // J163 -> J123

$sheet->setCellValue('M121', "Montant des MABC + MPPA"); // M161 -> M121
$sheet->setCellValue('L122', "X <= ". $parameter[0]->getFour1()); // L162 -> L122
$sheet->setCellValue('M122',$parameter[0]->getFour1()." < X <=".$parameter[0]->getFour2()); // M162 -> M122
$sheet->setCellValue('N122',  $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); // N162 -> N122
$sheet->setCellValue('O122', "X > ". $parameter[0]->getFour3()); // O162 -> O122
$sheet->setCellValue('L123', $result_achats_mounts[0]["nombre_achats_inf_four1"] + $result_achats_mounts[1]["nombre_achats_inf_four1"]); // L163 -> L123
$sheet->setCellValue('M123', $result_achats_mounts[0]["nombre_achats_four1_four2"] + $result_achats_mounts[1]["nombre_achats_four1_four2"]); // M163 -> M123
$sheet->setCellValue('N123',  $result_achats_mounts[0]["nombre_achats_four2_four3"] + $result_achats_mounts[1]["nombre_achats_four2_four3"]); // N163 -> N123
$sheet->setCellValue('O123', $result_achats_mounts[0]["nombre_achats_sup_four3"] + $result_achats_mounts[1]["nombre_achats_sup_four3"]); // O163 -> O123

//------------------------------ chart montant_type --------------------------//

$mppaLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$121', null, 12), // C161 -> C121
];

$mppaValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$123:$E$123', null, 4), // B163:E163 -> B123:E123
];

$mppaxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$122:$E$122', null, 4), // B162:E162 -> B122:E122
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

$mppaChart->setTopLeftPosition('B124'); // B164 -> B124
$mppaChart->setBottomRightPosition('F139'); // F179 -> F139
$sheet->addChart($mppaChart);

//------------------------------ chart montant_type --------------------------//

$mabcLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$121', null, 12), // H161 -> H121
];

$mabcValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$G$123:$J$123', null, 4), // G163:J163 -> G123:J123
];

$mabcxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$122:$J$122', null, 4), // G162:J162 -> G122:J122
];
$mabcSeries = new DataSeries(
    DataSeries::TYPE_PIECHART, // plotType
    null, // plotGrouping (Pie charts don't have any grouping)
    range(0, count($mabcValues) - 1), // plotOrder
    $mabcLabels, // plotLabel,
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

$mabcChart->setTopLeftPosition('G124'); // G164 -> G124
$mabcChart->setBottomRightPosition('K139'); // K179 -> K139
$sheet->addChart($mabcChart);

//------------------------------ chart total_type --------------------------//
$totaltypeLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$121', null, 12), // M161 -> M121
];

$totaltypeValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$L$123:$O$123', null, 4), // L163:O163 -> L123:O123
];

$totaltypexAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$122:$M$122', null, 4), // L162:M162 -> L122:M122
];
$totaltypeSeries = new DataSeries(
    DataSeries::TYPE_PIECHART, // plotType
    null, // plotGrouping (Pie charts don't have any grouping)
    range(0, count($totaltypeValues) - 1), // plotOrder
    $totaltypeLabels, // plotLabel,
    $totaltypexAx, // plotCategory,
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

$totaltypeChart->setTopLeftPosition('L124'); // L164 -> L124
$totaltypeChart->setBottomRightPosition('P139'); // P179 -> P139
$sheet->addChart($totaltypeChart);

$sheet->setCellValue('H141', 'Activités des PME') // H181 -> H141
      ->getStyle('H141') // H181 -> H141
      ->getFont()
      ->setSize(16)
      ->setColor(new Color(Color::COLOR_RED));

$topValCol="D";
$topVolCol="K";

$sheet->setCellValue('B142', 'MPPA PME'); // B182 -> B142
$sheet->setCellValue('D142', 'PME'); // D182 -> D142
$sheet->setCellValue('E142','% PME'); // E182 -> E142
$sheet->setCellValue('C143','VALEUR'); // C183 -> C143
$sheet->setCellValue('C144', 'NOMBRE'); // C184 -> C144
$sheet->setCellValue('D143', $result_achatsPME[0]["ValeurPME"]); // D183 -> D143
$sheet->setCellValue('E143', $result_achatsPME[0]["ValeurPercentPME"]); // E183 -> E143
$sheet->setCellValue('D144', $result_achatsPME[0]["VolumePME"]); // D184 -> D144
$sheet->setCellValue('E144', $result_achatsPME[0]["VolumePercentPME"]); // E184 -> E144

$sheet->setCellValue('B145', 'TOP PME VALEUR'); // B185 -> B145
$sheet->setCellValue('C146','VALEUR'); // C186 -> C146
$sheet->setCellValue('C147', 'DEPARTEMENT'); // C187 -> C147

for($i=0;$i<count($result_achatsSumVal);$i++){
    $sheet->setCellValue($topValCol . 146, $result_achatsSumVal[$i]["somme_montant_achat"]); // 186 -> 146
    $sheet->setCellValue($topValCol . 147, $result_achatsSumVal[$i]["departement"]); // 187 -> 147
    $topValCol++;
}

$sheet->setCellValue('I145', 'TOP PME VOLUME'); // I185 -> I145
$sheet->setCellValue('J146','VOLUME'); // J186 -> J146
$sheet->setCellValue('J147', 'DEPARTEMENT'); // J187 -> J147

for($i=0;$i<count($result_achatsSumVol);$i++){
    $sheet->setCellValue($topVolCol . 146, $result_achatsSumVol[$i]["total_nombre_achats"]); // 186 -> 146
    $sheet->setCellValue($topVolCol . 147, $result_achatsSumVol[$i]["departement"]); // 187 -> 147
    $topVolCol++;
}
//------------------------------------- top dep val chart------------------------------------//
$depValLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$147', null, 5), // D187 -> D147
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$E$147', null, 5), // E187 -> E147
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$F$147', null, 5), // F187 -> F147
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$147', null, 5), // G187 -> G147
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$147', null, 5), // H187 -> H147
];

$depValxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$145', null, 5), // B185 -> B145
];
$depValValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$146', null, 5), // D186 -> D146
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$E$146', null, 5), // E186 -> E146
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$F$146', null, 5), // F186 -> F146
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$G$146', null, 5), // G186 -> G146
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$146', null, 5), // H186 -> H146
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

$depValChart->setTopLeftPosition('B149'); // B189 -> B149
$depValChart->setBottomRightPosition('I162'); // I202 -> I162
$sheet->addChart($depValChart);

//------------------------------------- top dep vol  chart------------------------------------//

$depVolLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$K$147', null, 5), // K187 -> K147
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$147', null, 5), // L187 -> L147
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$147', null, 5), // M187 -> M147
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$147', null, 5), // N187 -> N147
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$O$147', null, 5), // O187 -> O147
];

$depVolxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$I$145', null, 5), // I185 -> I145
];  
$depVolValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$K$146', null, 5), // K186 -> K146
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$L$146', null, 5), // L186 -> L146
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$M$146', null, 5), // M186 -> M146
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$146', null, 5), // N186 -> N146
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$O$146', null, 5), // O186 -> O146
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

$depVolChart->setTopLeftPosition('I149'); // I189 -> I149
$depVolChart->setBottomRightPosition('P162'); // P202 -> P162
$sheet->addChart($depVolChart);

//------------------------------------- activite appro pme  ------------------------------------//

$mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

$sheet->setCellValue('B168', 'NB MPPA PME'); // B208 -> B168
$sheet->setCellValue('B169', '% MPPA'); // B209 -> B169
$approCol='C';

for($i=0;$i<count($result_achatsSum);$i++){
    $sheet->setCellValue($approCol . 167, $mois[$i]); // 207 -> 167
    $sheet->setCellValue($approCol . 168, round($result_achatsSum[$i]["nombre_achats_pme"])); // 208 -> 168
    $sheet->setCellValue($approCol . 169, round($result_achatsSum[$i]["pourcentage_achats_type_marche_1"])); // 209 -> 169
    $approCol++;
}
$sheet->setCellValue('O167', "TOTAL"); // O207 -> O167
$sheet->setCellValue('O168',  '=SUM(C168:N168)'); // O208 -> O168
$sheet->setCellValue('O169',  '=CEILING(SUM(C169:N169) / 12, 1)' ); // O209 -> O169

$approPmeLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$168', null, 12), // B208 -> B168
];

$approPmexAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$167:$N$167', null, 12), // C207:N207 -> C167:N167
];
$approPmeValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$168:$N$168', null, 12), // C208:N208 -> C168:N168
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

$approPmeChart->setTopLeftPosition('B170'); // B210 -> B170
$approPmeChart->setBottomRightPosition('P183'); // P223 -> P183
$sheet->addChart($approPmeChart);



$filePath = $projectDir . '/public/nom_de_fichier.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->setIncludeCharts(true);
$writer->save($filePath);
        
        return $filePath; // ou retournez un objet BinaryFileResponse si vous le souhaitez
    }

}