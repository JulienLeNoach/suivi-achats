<?php

namespace App\Controller\Statistic;
use App\Form\StatisticType;
use App\Form\CreateExcelType;
use App\Repository\AchatRepository;
use App\Service\StatisticPMEService;
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
        public function index(Request $request, StatisticPMEService $statisticPMEService): Response
        { 
            
            $form = $this->createForm(StatisticType::class, null, []);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $result_achats = $this->achatRepository->statisticPMESum($form);
                $result_achatsSum = $this->achatRepository->statisticPMEMonth($form);
                $result_achatsSumVol = $this->achatRepository->statisticPMETopVol($form);
                $result_achatsSumVal = $this->achatRepository->statisticPMETopVal($form);
                $excelForm = $this->createForm(CreateExcelType::class); 
                if ($excelForm->isSubmitted() && $excelForm->isValid()) {
      
                    $filePath = $statisticPMEService->createExcelFile($result_achats, $result_achatsSum, $result_achatsSumVol, $result_achatsSumVal);
                    return new BinaryFileResponse($filePath);

                }
                return $this->render('statistic_pme/index.html.twig', [
                    'form' => $form->createView(),
                    'excelForm' => $excelForm->createView(),
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

        /**
 * @Route("/statistic/pme/export_excel", name="app_statistic_pme_export_excel")
 */
public function exportExcel(Request $request, StatisticPMEService $statisticPMEService): Response
{
    // Traitez la requête pour obtenir les données nécessaires à l'export Excel
    // Supposons que les données sont passées via une requête GET ou POST
    $result_achats = $request->get('result_achats');
    $result_achatsSum = $request->get('result_achatsSum');
    $result_achatsSumVol = $request->get('result_achatsSumVol');
    $result_achatsSumVal = $request->get('result_achatsSumVal');

    // Convertir les données JSON en tableau PHP
    $result_achats = json_decode($result_achats, true);
    $result_achatsSum = json_decode($result_achatsSum, true);
    $result_achatsSumVol = json_decode($result_achatsSumVol, true);
    $result_achatsSumVal = json_decode($result_achatsSumVal, true);

    // Générer le fichier Excel
    $filePath = $statisticPMEService->createExcelFile($result_achats, $result_achatsSum, $result_achatsSumVol, $result_achatsSumVal);
    return new BinaryFileResponse($filePath);
}
    }
