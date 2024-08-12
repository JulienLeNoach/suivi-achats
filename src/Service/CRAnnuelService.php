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
                                    $result_achats, $result_achats_mounts, $parameter,$result_achatsPME, $result_achatsSum, $result_achatsSumVol, $result_achatsSumVal,$totalAchatPerMonthUnder2K)
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
   'B62:O71', 'B87:D88', 'G87:I88', 'L87:N88',
   'B108:D109', 'G108:I109', 'L108:N109',
   'B129:D130', 'B151:E156', 'B159:E161', 'G159:J161',
   'L159:O161', 'B180:E185', 'I183:K185',
   'B205:O207',
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

   // Dossiers inférieurs à 2 000,00 € TTC
   $sheet->setCellValue($col . '18', $moi);
   $sheet->setCellValue($col . '19', $totalAchatPerMonthUnder2K['type_marche_1']['current_year'][$index]['count']);
   $sheet->setCellValue($col . '20', $totalAchatPerMonthUnder2K['type_marche_0']['current_year'][$index]['count']);
   $sheet->setCellValue($col . '21', $totalAchatPerMonthUnder2K['type_marche_1']['current_year'][$index]['count'] + $totalAchatPerMonthUnder2K['type_marche_0']['current_year'][$index]['count']);
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
                $sheet->setCellValue('H62', 'Activités appro délais')
      ->getStyle('H62')
      ->getFont()
      ->setSize(16)
      ->setColor(new Color(Color::COLOR_RED));

$mois = ['Délai','Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre','TOTAL'];
$col = 65;
$colmonth='B';

for($j=0;$j<=13;$j++){
    $sheet->setCellValue($colmonth . 64, $mois[$j]);
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
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$67', null, 12), 
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$70', null, 12), // Mois
];

$notTransxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$64:$N$64', null, 12), // 'Valeurs'
];
$notTransValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$67:$N$67', null, 12), // Valeurs pour datasets1
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$70:$N$70', null, 12), // Valeurs pour datasets2
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

$approVolChart->setTopLeftPosition('B74');
$approVolChart->setBottomRightPosition('P88');
$sheet->addChart($approVolChart);

//-------------------------- Ant GSBDD chart -------------------------------- // 
$sheet->setCellValue('B' . 90, 'Ant. GSBDD');
$sheet->setCellValue('C' . 89, '<= 3j / ' .$achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"]. "%");
$sheet->setCellValue('D' . 89, '> 3j / '.$achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"] . "%");  
$sheet->setCellValue('C' . 90, $achats_delay_all[0]["CountAntInf3"]); 
$sheet->setCellValue('D' . 90, $achats_delay_all[0]["CountAntSup3"]); 

$antLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$90', null, 12), 

];

$antValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$90:$D$90', null, 2),
];

$antxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$89:$D$89', null, 4), // 'Valeurs'
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

$antChart->setTopLeftPosition('B91');
$antChart->setBottomRightPosition('F108');
$sheet->addChart($antChart);

//-------------------------- Budget chart -------------------------------- // 

$sheet->setCellValue('G' . 90, 'Budget');
$sheet->setCellValue('H' . 89, '<= 3j / ' .$achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"] . "%");
$sheet->setCellValue('I' . 89, '> 3j / '. $achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"] . "%");  
$sheet->setCellValue('H' . 90, $achats_delay_all[1]["CountBudgetInf3"]); 
$sheet->setCellValue('I' . 90, $achats_delay_all[1]["CountBudgetSup3"]); 

$budgetLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$90', null, 12), 

];

$budgetValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$90:$I$90', null, 2),
];

$budgetxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$89:$I$89', null, 4), // 'Valeurs'
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

$budgetChart->setTopLeftPosition('G91');
$budgetChart->setBottomRightPosition('K108');
$sheet->addChart($budgetChart);

//-------------------------- APPRO chart -------------------------------- // 

$sheet->setCellValue('L' . 90, 'APPRO');
$sheet->setCellValue('M' . 89, '<= 7j / ' .$achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"]. "%");
$sheet->setCellValue('N' . 89, '> 7j / '.$achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"]. "%");  
$sheet->setCellValue('M' . 90, $achats_delay_all[2]["CountApproInf7"]); 
$sheet->setCellValue('N' . 90, $achats_delay_all[2]["CountApproSup7"]); 

$approLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$90', null, 12), 

];

$approValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$M$90:$N$90', null, 2),
];

$approxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$89:$Nx$89', null, 4), // 'Valeurs'
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

$approChart->setTopLeftPosition('L91');
$approChart->setBottomRightPosition('P108');
$sheet->addChart($approChart);

//-------------------------- Fin chart -------------------------------- // 

$sheet->setCellValue('B' . 111, 'Fin');
$sheet->setCellValue('C' . 110, '< 7j / ' .$achats_delay_all[3]["Pourcentage_Delai_Inf_7_Jours_Fin"] . "%");
$sheet->setCellValue('D' . 110, '> 7j / '.$achats_delay_all[3]["Pourcentage_Delai_Sup_7_Jours_Fin"] . "%");  
$sheet->setCellValue('C' . 111, $achats_delay_all[3]["CountFinInf7"]); 
$sheet->setCellValue('D' . 111, $achats_delay_all[3]["CountFinSup7"]); 

$finLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$110', null, 12), 

];

$finValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$111:$D$111', null, 2),
];

$finxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$110:$D$110', null, 4), // 'Valeurs'
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

$finChart->setTopLeftPosition('B112');
$finChart->setBottomRightPosition('F129');
$sheet->addChart($finChart);

//-------------------------- Chorus formul. chart -------------------------------- // 

$sheet->setCellValue('G' . 111, 'Chorus formul.');
$sheet->setCellValue('H' . 110, '<= 10j / ' .$achats_delay_all[4]["Pourcentage_Delai_Inf_10_Jours_Chorus"] . "%");
$sheet->setCellValue('I' . 110, '> à 10j / '.$achats_delay_all[4]["Pourcentage_Delai_Sup_10_Jours_Chorus"] . "%");  
$sheet->setCellValue('H' . 111, $achats_delay_all[4]["CountChorusFormInf10"]); 
$sheet->setCellValue('I' . 111, $achats_delay_all[4]["CountChorusFormSup10"]); 

$chorLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$110', null, 12), 

];

$chorValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$111:$I$111', null, 2),
];

$chorxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$110:$I$110', null, 4), // 'Valeurs'
];
$chorSeries = new DataSeries(
    DataSeries::TYPE_PIECHART, // plotType
    null, // plotGrouping (Pie charts don't have any grouping)
    range(0, count($chorValues) - 1), // plotOrder
    $chorLabels, // plotLabel,
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

$chorChart->setTopLeftPosition('G112');
$chorChart->setBottomRightPosition('K129');
$sheet->addChart($chorChart);

//-------------------------- PFAF chart -------------------------------- // 

$sheet->setCellValue('L' . 111, 'PFAF');
$sheet->setCellValue('M' . 110,  '<= 14j / ' .$achats_delay_all[5]["Pourcentage_Delai_Inf_14_Jours_Pfaf"] . "%");
$sheet->setCellValue('N' . 110, '> à 14j / '.$achats_delay_all[5]["Pourcentage_Delai_Sup_14_Jours_Pfaf"] . "%");  
$sheet->setCellValue('M' . 111, $achats_delay_all[5]["CountPfafInf14"]); 
$sheet->setCellValue('N' . 111, $achats_delay_all[5]["CountPfafSup14"]); 

$pfafLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$111', null, 12), 

];

$pfafValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$M$111:$N$111', null, 2),
];

$pfafxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$110:$N$110', null, 4), // 'Valeurs'
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

$pfafChart->setTopLeftPosition('L112');
$pfafChart->setBottomRightPosition('P129');
$sheet->addChart($pfafChart);

//-------------------------- Total chart -------------------------------- // 

$sheet->setCellValue('B' . 132, 'Délai total');
$sheet->setCellValue('C' . 131,  '<= 15j / ' .$achats_delay_all[6]["Pourcentage_Delai_Inf_15_Jours"]. "%");
$sheet->setCellValue('D' . 131, '> à 15j / '.$achats_delay_all[6]["Pourcentage_Delai_Sup_15_Jours"]. "%");  
$sheet->setCellValue('C' . 132, $achats_delay_all[6]["CountDelaiTotalInf15"]); 
$sheet->setCellValue('D' . 132, $achats_delay_all[6]["CountDelaiTotalSup15"]); 

$totalLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$131', null, 12), 

];

$totalValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$132:$D$132', null, 2),
];

$totalxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$131:$D$131', null, 4), // 'Valeurs'
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

$totalChart->setTopLeftPosition('B133');
$totalChart->setBottomRightPosition('F150');
$sheet->addChart($totalChart);

//------------------------------ Tab % vol/val_type --------------------------//

$sheet->setCellValue('H151', 'Activités par type de marché')
      ->getStyle('H151')
      ->getFont()
      ->setSize(16)
      ->setColor(new Color(Color::COLOR_RED));
$sheet->setCellValue('C153', "MPPA"); 
$sheet->setCellValue('D153', "MABC"); 
$sheet->setCellValue('E153', "TOTAUX");
$sheet->setCellValue('B154', "VALEUR"); 
$sheet->setCellValue('B155', "NOMBRE"); 
$sheet->setCellValue('B156', "MOYENNE"); 
$sheet->setCellValue('B157', "% VALEUR");
$sheet->setCellValue('B158', "% VOLUME"); 
$sheet->setCellValue('C155', $result_achats[0]["somme_montant_type_1"]); 
$sheet->setCellValue('D154', $result_achats[1]["somme_montant_type_0"]); 
$sheet->setCellValue('E154', $result_achats[1]["somme_montant_type_0"] + $result_achats[0]["somme_montant_type_1"]); 
$sheet->setCellValue('C155', $result_achats[0]["nombre_achats_type_1"]); 
$sheet->setCellValue('D155', $result_achats[1]["nombre_achats_type_0"]); 
$sheet->setCellValue('E155', $result_achats[1]["nombre_achats_type_0"] +  $result_achats[0]["nombre_achats_type_1"]); 
$sheet->setCellValue('C156', $result_achats[0]["moyenne_montant_type_1"]); 
$sheet->setCellValue('D156', $result_achats[1]["moyenne_montant_type_0"]); 
$sheet->setCellValue('E156', $result_achats[1]["moyenne_montant_type_0"] +  $result_achats[0]["moyenne_montant_type_1"]); 
$sheet->setCellValue('C157', $result_achats[0]["pourcentage_type_1_total"]); 
$sheet->setCellValue('D157', $result_achats[1]["pourcentage_type_0_total"]); 
$sheet->setCellValue('C158', $result_achats[0]["pourcentage_type_1"]); 
$sheet->setCellValue('D158', $result_achats[1]["pourcentage_type_0"]);

//------------------------------ Tab montant_type --------------------------//
$sheet->setCellValue('C161', "Montant des MPPA");  
$sheet->setCellValue('B162', "X <= ". $parameter[0]->getFour2()); 
$sheet->setCellValue('C162', $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); 
$sheet->setCellValue('D162',  $parameter[0]->getFour3()." < X <=".$parameter[0]->getFour4()); 
$sheet->setCellValue('E162', "X > ". $parameter[0]->getFour4()); 
$sheet->setCellValue('B163', $result_achats_mounts[0]["nombre_achats_inf_four1"]); 
$sheet->setCellValue('C163', $result_achats_mounts[0]["nombre_achats_four1_four2"]); 
$sheet->setCellValue('D163',  $result_achats_mounts[0]["nombre_achats_four2_four3"]); 
$sheet->setCellValue('E163', $result_achats_mounts[0]["nombre_achats_sup_four3"]); 

$sheet->setCellValue('H161', "Montant des MABC"); 
$sheet->setCellValue('G162', "X <= ". $parameter[0]->getFour2()); 
$sheet->setCellValue('H162', $parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); 
$sheet->setCellValue('I162',  $parameter[0]->getFour3()." < X <=".$parameter[0]->getFour4()); 
$sheet->setCellValue('J162', "X > ". $parameter[0]->getFour4()); 
$sheet->setCellValue('G163', $result_achats_mounts[1]["nombre_achats_inf_four1"]); 
$sheet->setCellValue('H163', $result_achats_mounts[1]["nombre_achats_four1_four2"]); 
$sheet->setCellValue('I163',  $result_achats_mounts[1]["nombre_achats_four2_four3"]); 
$sheet->setCellValue('J163', $result_achats_mounts[1]["nombre_achats_sup_four3"]); 

$sheet->setCellValue('M161', "Montant des MABC + MPPA"); 
$sheet->setCellValue('L162', "X <= ". $parameter[0]->getFour2()); 
$sheet->setCellValue('M162',$parameter[0]->getFour2()." < X <=".$parameter[0]->getFour3()); 
$sheet->setCellValue('N162',  $parameter[0]->getFour3()." < X <=".$parameter[0]->getFour4()); 
$sheet->setCellValue('O162', "X > ". $parameter[0]->getFour4()); 
$sheet->setCellValue('L163', $result_achats_mounts[0]["nombre_achats_inf_four1"] + $result_achats_mounts[1]["nombre_achats_inf_four1"]); 
$sheet->setCellValue('M163', $result_achats_mounts[0]["nombre_achats_four1_four2"] + $result_achats_mounts[1]["nombre_achats_four1_four2"]); 
$sheet->setCellValue('N163',  $result_achats_mounts[0]["nombre_achats_four2_four3"] + $result_achats_mounts[1]["nombre_achats_four2_four3"]); 
$sheet->setCellValue('O163', $result_achats_mounts[0]["nombre_achats_sup_four3"] + $result_achats_mounts[1]["nombre_achats_sup_four3"]);

//------------------------------ chart montant_type --------------------------//

$mppaLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$161', null, 12), 

];

$mppaValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$163:$E$163', null, 4),
];

$mppaxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$162:$E$162', null, 4), // 'Valeurs'
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

$mppaChart->setTopLeftPosition('B164');
$mppaChart->setBottomRightPosition('F179');
$sheet->addChart($mppaChart);

//------------------------------ chart montant_type --------------------------//

$mabcLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$161', null, 12), 

];

$mabcValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$G$163:$J$163', null, 4),
];

$mabcxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$162:$J$162', null, 4), // 'Valeurs'
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

$mabcChart->setTopLeftPosition('G164');
$mabcChart->setBottomRightPosition('K179');
$sheet->addChart($mabcChart);

//------------------------------ chart total_type --------------------------//
$totaltypeLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$161', null, 12), 

];

$totaltypeValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$L$163:$O$163', null, 4),
];

$totaltypexAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$162:$M$162', null, 4), // 'Valeurs'
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

$totaltypeChart->setTopLeftPosition('L164');
$totaltypeChart->setBottomRightPosition('P179');
$sheet->addChart($totaltypeChart);

$sheet->setCellValue('H181', 'Activités des PME')
      ->getStyle('H181')
      ->getFont()
      ->setSize(16)
      ->setColor(new Color(Color::COLOR_RED));

$topValCol="D";
$topVolCol="K";

$sheet->setCellValue('B182', 'MPPA PME');
$sheet->setCellValue('D182', 'PME');
$sheet->setCellValue('E182','% PME');
$sheet->setCellValue('C183','VALEUR');
$sheet->setCellValue('C184', 'NOMBRE');
$sheet->setCellValue('D183', $result_achatsPME[0]["ValeurPME"]);
$sheet->setCellValue('E183', $result_achatsPME[0]["ValeurPercentPME"]);
$sheet->setCellValue('D184', $result_achatsPME[0]["VolumePME"]);
$sheet->setCellValue('E184', $result_achatsPME[0]["VolumePercentPME"]);

$sheet->setCellValue('B185', 'TOP PME VALEUR');
$sheet->setCellValue('C186','VALEUR');
$sheet->setCellValue('C187', 'DEPARTEMENT');

for($i=0;$i<count($result_achatsSumVal);$i++){
    $sheet->setCellValue($topValCol . 186, $result_achatsSumVal[$i]["somme_montant_achat"]);
    $sheet->setCellValue($topValCol . 187, $result_achatsSumVal[$i]["departement"]);
    $topValCol++;
}

$sheet->setCellValue('I185', 'TOP PME VOLUME');
$sheet->setCellValue('J186','VOLUME');
$sheet->setCellValue('J187', 'DEPARTEMENT');

for($i=0;$i<count($result_achatsSumVol);$i++){
    $sheet->setCellValue($topVolCol . 186, $result_achatsSumVol[$i]["total_nombre_achats"]);
    $sheet->setCellValue($topVolCol . 187, $result_achatsSumVol[$i]["departement"]);
    $topVolCol++;
}
//------------------------------------- top dep val chart------------------------------------//
$depValLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$187', null, 5),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$E$187', null, 5), 
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$F$187', null, 5), 
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$G$187', null, 5), 
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$H$187', null, 5), 

];

$depValxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$185', null, 5), // 'Valeurs'
];
$depValValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$186', null, 5), // Valeurs pour datasets1
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$E$186', null, 5), // Valeurs pour datasets1
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$F$186', null, 5), // Valeurs pour datasets1
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$G$186', null, 5), // Valeurs pour datasets1
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$H$186', null, 5), // Valeurs pour datasets1
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

$depValChart->setTopLeftPosition('B189');
$depValChart->setBottomRightPosition('I202');
$sheet->addChart($depValChart);

//------------------------------------- top dep vol  chart------------------------------------//

$depVolLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$K$187', null, 5),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$L$187', null, 5), 
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$M$187', null, 5), 
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$N$187', null, 5), 
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$O$187', null, 5), 

];

$depVolxAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$I$185', null, 5), // 'Valeurs'
];  
$depVolValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$K$186', null, 5), // Valeurs pour datasets1
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$L$186', null, 5), // Valeurs pour datasets1
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$M$186', null, 5), // Valeurs pour datasets1
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$N$186', null, 5), // Valeurs pour datasets1
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$O$186', null, 5), // Valeurs pour datasets1
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

$depVolChart->setTopLeftPosition('I189');
$depVolChart->setBottomRightPosition('P202');
$sheet->addChart($depVolChart);



        //------------------------------------- activite appro pme  ------------------------------------//

        $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

$sheet->setCellValue('B208', 'NB MPPA PME');
$sheet->setCellValue('B209', '% MPPA');
$approCol='C';

for($i=0;$i<count($result_achatsSum);$i++){

    $sheet->setCellValue($approCol . 207, $mois[$i]);
    $sheet->setCellValue($approCol . 208, $result_achatsSum[$i]["nombre_achats_pme"]);
    $sheet->setCellValue($approCol . 209, $result_achatsSum[$i]["pourcentage_achats_type_marche_1"]);
    $approCol++;
}
$sheet->setCellValue('O207', "TOTAL");
$sheet->setCellValue('O208',  '=SUM(C208:N208)');
$sheet->setCellValue('O209',  '=SUM(C209:N209) / 12' );


$approPmeLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$208', null, 12), 
];

$approPmexAx = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$207:$N$207', null, 12), // 'Valeurs'
];
$approPmeValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$208:$N$208', null, 12), // Valeurs pour datasets2
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

$approPmeChart->setTopLeftPosition('B210');
$approPmeChart->setBottomRightPosition('P223');
$sheet->addChart($approPmeChart);


$filePath = $projectDir . '/public/nom_de_fichier.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->setIncludeCharts(true);
$writer->save($filePath);
        
        return $filePath; // ou retournez un objet BinaryFileResponse si vous le souhaitez
    }

}