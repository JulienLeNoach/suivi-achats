<?php

namespace App\Controller\Statistic;

use App\Form\StatisticType;
use App\Form\CreateExcelType;
use App\Repository\AchatRepository;
use App\Service\StatisticDelayService;
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

class StatisticDelayController extends AbstractController
{

    private $achatRepository;
    private $statisticDelayService;
    private $projectDir;

    public function __construct(AchatRepository $achatRepository,KernelInterface $kernel, StatisticDelayService $statisticDelayService)
    {

        $this->achatRepository = $achatRepository;
        $this->statisticDelayService = $statisticDelayService;
        $this->projectDir = $kernel->getProjectDir();

    }

    #[Route('/statistic/delay', name: 'app_statistic_delay')]
    public function index(Request $request, StatisticDelayService $statisticDelayService): Response
    {

        $form = $this->createForm(StatisticType::class, null, []);
        $form->handleRequest($request);        
        $achats[]=null;

        if ($form->isSubmitted() && $form->isValid()) {
            $achats_delay = $this->achatRepository->yearDelayDiff($form);
            // dd($achats_delay);
            $achats = $this->statisticDelayService->totalDelayPerMonth($achats_delay);
            $achats_delay_all = $this->achatRepository->yearDelayCount($form);
            
        // Récupérez les données achats
        $transStat = array_filter(array_values($achats[2]), 'is_numeric');
        $notStat = array_filter(array_values($achats[5]), 'is_numeric');
        $excelForm = $this->createForm(CreateExcelType::class); 

        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
            'excelForm' => $excelForm->createView(),
            'achats' => $achats,
            'transStat' => $transStat,
            'notStat' => $notStat,
            'achats_delay_all' => $achats_delay_all,

        ]);

        }
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
            'achats' => $achats,
        ]);
    }
/**
 * @Route("/statistic/delay/export_excel", name="app_statistic_delay_export_excel")
 */
public function exportExcel(Request $request, StatisticDelayService $statisticDelayService): Response
{
    // Traitez la requête pour obtenir les données nécessaires à l'export Excel
    // Supposons que les données sont passées via une requête GET ou POST
    $achats = $request->get('achats');
    $achats_delay_all = $request->get('achats_delay_all');


    // Convertir les données JSON en tableau PHP
    $achats = json_decode($achats, true);
    $achats_delay_all = json_decode($achats_delay_all, true);


    // Générer le fichier Excel
    $filePath = $statisticDelayService->createExcelFile($achats, $achats_delay_all);
    return new BinaryFileResponse($filePath);

}

}