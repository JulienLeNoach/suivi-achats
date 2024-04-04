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

    public function purchaseStatisticsByMonth(array $result1, array $result2): array
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
    
        $purchaseData = [];
    
        foreach ($months as $month) {
            $purchaseData[$month] = [
                'countmppa' => 0,
                'totalmontantmppa' => 0,
                'countmabc' => 0,
                'totalmontantmabc' => 0,
                'totalcount' => 0
            ];
        }
    
        foreach ($result1 as $row) {
            $month = intval($row['month']);
            $totalmontant = $row['totalmontant'];
            $count = intval($row['count']);
            $purchaseData[$months[$month]]['countmppa'] += $count;
            $purchaseData[$months[$month]]['totalmontantmppa'] += $totalmontant;
        }
    
        foreach ($result2 as $row) {
            $month = intval($row['month']);
            $totalmontant = $row['totalmontant'];
            $count = intval($row['count']);
            $purchaseData[$months[$month]]['countmabc'] += $count;
            $purchaseData[$months[$month]]['totalmontantmabc'] += $totalmontant;
        }
    
        foreach ($purchaseData as &$data) {
            $data['totalcount'] = $data['countmppa'] + $data['countmabc'];
            $data['totalmotant'] = $data['totalmontantmabc'] + $data['totalmontantmppa'];
        }
    
        return $purchaseData;
    }

    public function arrayMapChart($counts, $dataKey,$dataKey2)
    {
        foreach ($counts as $count1) {
            $mppa[] = $count1[$dataKey];
        }
        foreach ($counts as $count2) {
            $mabc[] = $count2[$dataKey2];
        }

        foreach ($counts as $count1) {
            $mppa[] = $count1[$dataKey];
        }
        foreach ($counts as $count2) {
            $mabc[] = $count2[$dataKey2];
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