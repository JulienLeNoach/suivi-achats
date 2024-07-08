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

    public function purchaseStatisticsByMonth(array $currentYearMppa, array $previousYearMppa, array $currentYearMabc, array $previousYearMabc): array
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
    
        $currentYearData = [];
        $previousYearData = [];
    
        foreach ($months as $month) {
            $currentYearData[$month] = [
                'countmppa' => 0,
                'totalmontantmppa' => 0,
                'countmabc' => 0,
                'totalmontantmabc' => 0,
                'totalcount' => 0,
                'totalmontant' => 0,
            ];
            $previousYearData[$month] = [
                'countmppa' => 0,
                'totalmontantmppa' => 0,
                'countmabc' => 0,
                'totalmontantmabc' => 0,
                'totalcount' => 0,
                'totalmontant' => 0,
            ];
        }
    
        foreach ($currentYearMppa as $row) {
            $month = intval($row['month']);
            $totalmontant = $row['totalmontant'];
            $count = intval($row['count']);
            $currentYearData[$months[$month]]['countmppa'] += $count;
            $currentYearData[$months[$month]]['totalmontantmppa'] += $totalmontant;
        }
    
        foreach ($previousYearMppa as $row) {
            $month = intval($row['month']);
            $totalmontant = $row['totalmontant'];
            $count = intval($row['count']);
            $previousYearData[$months[$month]]['countmppa'] += $count;
            $previousYearData[$months[$month]]['totalmontantmppa'] += $totalmontant;
        }
    
        foreach ($currentYearMabc as $row) {
            $month = intval($row['month']);
            $totalmontant = $row['totalmontant'];
            $count = intval($row['count']);
            $currentYearData[$months[$month]]['countmabc'] += $count;
            $currentYearData[$months[$month]]['totalmontantmabc'] += $totalmontant;
        }
    
        foreach ($previousYearMabc as $row) {
            $month = intval($row['month']);
            $totalmontant = $row['totalmontant'];
            $count = intval($row['count']);
            $previousYearData[$months[$month]]['countmabc'] += $count;
            $previousYearData[$months[$month]]['totalmontantmabc'] += $totalmontant;
        }
    
        foreach ($currentYearData as &$data) {
            $data['totalcount'] = $data['countmppa'] + $data['countmabc'];
            $data['totalmontant'] = $data['totalmontantmppa'] + $data['totalmontantmabc'];
        }
    
        foreach ($previousYearData as &$data) {
            $data['totalcount'] = $data['countmppa'] + $data['countmabc'];
            $data['totalmontant'] = $data['totalmontantmppa'] + $data['totalmontantmabc'];
        }
    
        return [
            'current_year' => $currentYearData,
            'previous_year' => $previousYearData,
        ];
    }

    public function arrayMapChart($counts, $dataKey, $dataKey2)
{
    $mppa = [];
    $mabc = [];

    foreach ($counts as $count) {
        $mppa[] = $count[$dataKey];
        $mabc[] = $count[$dataKey2];
    }

    $mppa = array_map(function($value) {
        return round($value, 2);
    }, $mppa);

    $mabc = array_map(function($value) {
        return round($value, 2);
    }, $mabc);
    
    return ['mppa' => $mppa, 'mabc' => $mabc];
}
    



}