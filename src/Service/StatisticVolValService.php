<?php namespace App\Service;

use Dompdf\Dompdf;
use App\Repository\AchatRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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
    public function createChart($counts1, $counts2, $dataKey)
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


    public function generateExcel(array $counts1, array $counts2)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $col = 1; 
        $col2 = 1;
        $sheet->setCellValueByColumnAndRow($col, 1, 'Volume');
        $sheet->setCellValueByColumnAndRow($col, 2, 'MABC');
        $sheet->setCellValueByColumnAndRow($col, 3, 'MPPA');
        $sheet->setCellValueByColumnAndRow($col, 4, 'TOTAL');
        $sheet->setCellValueByColumnAndRow(14, 1, 'Total');

        $col++;
        foreach ($counts1 as $month => $rowData) {
            $value1 = $rowData['count']; 

            $value2 = $counts2[$month]['count']; 

            $sheet->setCellValueByColumnAndRow($col, 1, $month);

            $sheet->setCellValueByColumnAndRow($col, 2, $value1);

            $sheet->setCellValueByColumnAndRow($col, 3, $value2);

            $sum = $value1 + $value2;
            $sheet->setCellValueByColumnAndRow($col, 4, $sum);

            $col++;
        }


        $sumFormula = "=SUM(B2:M2)";
        $sheet->setCellValueByColumnAndRow($col, 2, $sumFormula);

        $sumFormula = "=SUM(B3:M3)";
        $sheet->setCellValueByColumnAndRow($col, 3, $sumFormula);
        $sumFormula = "=SUM(B4:M4)";
        $sheet->setCellValueByColumnAndRow($col, 4, $sumFormula);
        $sheet->setCellValueByColumnAndRow($col2, 6, 'Valeur (HT)');
        $sheet->setCellValueByColumnAndRow($col2, 7, 'MABC');
        $sheet->setCellValueByColumnAndRow($col2, 8, 'MPPA');
        $sheet->setCellValueByColumnAndRow($col2, 9, 'TOTAL');
        $sheet->setCellValueByColumnAndRow(14, 6, 'Total');

        $col2++;

        foreach ($counts1 as $month => $rowData) {
            $value1 = $rowData['totalmontant']; 
            $value2 = $counts2[$month]['totalmontant']; 

            $sheet->setCellValueByColumnAndRow($col2, 6, $month);

            $sheet->setCellValueByColumnAndRow($col2, 7, $value1);

            $sheet->setCellValueByColumnAndRow($col2, 8, $value2);

            $sum = $value1 + $value2;
            $sheet->setCellValueByColumnAndRow($col2, 9, $sum);

            $col2++;
        }
        $sumFormula = "=SUM(B7:M7)";
        $sheet->setCellValueByColumnAndRow($col, 7, $sumFormula);

        $sumFormula = "=SUM(B8:M8)";
        $sheet->setCellValueByColumnAndRow($col, 8, $sumFormula);
        $sumFormula = "=SUM(B9:M9)";
        $sheet->setCellValueByColumnAndRow($col, 9, $sumFormula);
        $writer = new Xlsx($spreadsheet);

        $fileName = 'activite_annuelle.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit();
    }

    public function generatePDF($html): Response
    {

    
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
    
        // Save the PDF content to a file instead of displaying it in the browser
        $downloadDirectory = $this->getParameter('kernel.project_dir') . '/public/downloads';
        file_put_contents($downloadDirectory, $dompdf->output());
    
        $response = new BinaryFileResponse($downloadDirectory);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'generated_pdf_' . date('Y-m-d_H-i-s') . '.pdf'
        
        );
        return $response;
    }
    public function generatePDF2(array $counts1, array $counts2, array $combinedCounts, array $combinedCountsMontant): Response
    {
        $html = $this->render('statistic/pdfgenerator.html.twig', [
            'counts1' => $counts1,
            'counts2' => $counts2,
            'combinedCounts' => $combinedCounts,
            'combinedCountsMontant' => $combinedCountsMontant,
        ]);
    
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
    
        // Save the PDF content to a file instead of displaying it in the browser
        $downloadDirectory = $this->getParameter('kernel.project_dir') . '/public/downloads';
        file_put_contents($downloadDirectory, $dompdf->output());
    
        $response = new BinaryFileResponse($downloadDirectory);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'generated_pdf_' . date('Y-m-d_H-i-s') . '.pdf'
        
        );
        return $response;
    }
}