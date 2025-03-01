<?php

namespace App\Controller\Statistic;

use App\Form\CRAnnuelType;
use App\Service\CRAnnuelService;
use App\Repository\AchatRepository;
use App\Repository\ParametresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Service\Statistic\VolVal\StatisticVolValService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Service\Statistic\StatisticDelay\StatisticDelayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CRAnnuelController extends AbstractController
{

    private $entityManager;
    private $achatRepository;
    private $projectDir;
    private $statisticService;
    private $crAnnuelService;
    private $statisticDelayService;
    private $parametresRepository;

    
    public function __construct(EntityManagerInterface $entityManager, AchatRepository $achatRepository, KernelInterface $kernel,
     StatisticVolValService $statisticService, CRAnnuelService $crAnnuelService,StatisticDelayService $statisticDelayService,
     ParametresRepository $parametresRepository)
    {
        $this->entityManager = $entityManager;
        $this->parametresRepository = $parametresRepository;
        $this->statisticService = $statisticService;
        $this->achatRepository = $achatRepository;
        $this->crAnnuelService = $crAnnuelService;
        $this->statisticDelayService = $statisticDelayService;
        $this->projectDir = $kernel->getProjectDir();
    }
    #[Route('/crannuel', name: 'cr_annuel')]
    public function index(Request $request): Response
    {
        $errorMessage = null;
        $form = $this->createForm(CRAnnuelType::class);
        $form->handleRequest($request);
        $result_achatsSumVal[]=null;
        if ($form->isSubmitted()) {
            $selectedYear = $form->get('date')->getData();

            $mppaEtat = 1;
            $mabcEtat = 0;
             $mppaMtTotal = $this->achatRepository->getPurchaseCountAndTotalAmount($mppaEtat, $form);
                $mabcMtTotal = $this->achatRepository->getPurchaseCountAndTotalAmount($mabcEtat, $form);
                $totalAchatPerMonthUnder2K = $this->achatRepository->getTotalAchatUnder2K($form);

                $volValStat = $this->statisticService->purchaseStatisticsByMonth(
                    $mppaMtTotal['current_year'], 
                    $mppaMtTotal['previous_year'], 
                    $mabcMtTotal['current_year'], 
                    $mabcMtTotal['previous_year']
                );
        
                $delayVolVal = $this->achatRepository->getVolValDelay($form);
        
                $chartDataCountCurrent = $this->statisticService->arrayMapChart($volValStat['current_year'], 'countmppa', 'countmabc');
                $chartDataCountPrevious = $this->statisticService->arrayMapChart($volValStat['previous_year'], 'countmppa', 'countmabc');
                $chartDataTotalCurrent = $this->statisticService->arrayMapChart($volValStat['current_year'], 'totalmontantmppa', 'totalmontantmabc');
                $chartDataTotalPrevious = $this->statisticService->arrayMapChart($volValStat['previous_year'], 'totalmontantmppa', 'totalmontantmabc');
            
    
            $delayVolVal = $this->achatRepository->getVolValDelay($form);
    
            $chartDataCountCurrent = $this->statisticService->arrayMapChart($volValStat['current_year'], 'countmppa', 'countmabc');
            $chartDataTotalCurrent = $this->statisticService->arrayMapChart($volValStat['current_year'], 'totalmontantmppa', 'totalmontantmabc');

            $achats_delay = $this->achatRepository->getYearDelayDiff($form);
            $achats = $this->statisticDelayService->getDelayPerMonth($achats_delay);
            $achats_delay_all = $this->achatRepository->getYearDelayCount($form);

            $result_achats = $this->achatRepository->getPurchaseByType($form);
            $result_achats_mounts = $this->achatRepository->getPurchaseByTypeMount($form);
            $parameter = $this->parametresRepository->findById(1);

            $result_achatsPME = $this->achatRepository->getPMESum($form);
            $result_achatsSum = $this->achatRepository->getPMEMonthSum($form);
            $result_achatsSumVol = $this->achatRepository->getPMETopVol($form);
            $result_achatsSumVal = $this->achatRepository->getPMETopVal($form);
            $errorMessage = null;

            if (empty($result_achatsSumVal)) {
                $errorMessage = 'Aucun résultat pour cette recherche.';
                return $this->render('cr_annuel/index.html.twig', [
                    'form' => $form->createView(),
                    'result_achatsSumVal' => $result_achatsSumVal,
                    'errorMessage' => $errorMessage,
                ]);
            }
            $filePath = $this->crAnnuelService->generateExcelFile($chartDataCountCurrent, $chartDataCountPrevious, $chartDataTotalCurrent, $chartDataTotalPrevious, $this->projectDir, $achats, $achats_delay_all,$result_achats,
                                                                $result_achats_mounts, $parameter,$result_achatsPME, $result_achatsSum, $result_achatsSumVol, $result_achatsSumVal,$totalAchatPerMonthUnder2K,$selectedYear);
            return new BinaryFileResponse($filePath);
        }
        return $this->render('cr_annuel/index.html.twig', [
            'form' => $form->createView(),
            'errorMessage' => $errorMessage,

        ]);
    }
}
