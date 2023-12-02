<?php

namespace App\Controller\Statistic;
use App\Form\StatisticType;
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
                    if ($form->get('excel')->isClicked()) {

      
                        $filePath = $statisticPMEService->createExcelFile($result_achats, $result_achatsSum, $result_achatsSumVol, $result_achatsSumVal);
                        return new BinaryFileResponse($filePath);
                }
                return $this->render('statistic_pme/index.html.twig', [
                    'form' => $form->createView(),
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
    }
