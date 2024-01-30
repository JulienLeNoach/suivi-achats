<?php

namespace App\Controller;

use App\Form\CRAnnuelType;
use App\Service\CRAnnuelService;
use App\Repository\AchatRepository;
use App\Service\StatisticDelayService;
use App\Service\StatisticVolValService;
use App\Repository\ParametresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

            $mpttaEtat = 1;
            $mabcEtat = 0;
            $counts1 = [];
            $counts2 = [];
            $counts1 = $this->achatRepository->getPurchaseCountAndTotalAmount($mpttaEtat,$form);
            $counts1 = $this->statisticService->totalPerMonth($counts1);
            $counts2 = $this->achatRepository->getPurchaseCountAndTotalAmount($mabcEtat,$form);
            $counts2 = $this->statisticService->totalPerMonth($counts2);
            $chartData = $this->statisticService->arrayMapChart( $counts1, $counts2, 'count');
            $chartData2 = $this->statisticService->arrayMapChart($counts1, $counts2, 'totalmontant');
            $datasets1 = $chartData['datasets'];
            $datasets2 = $chartData['datasets2'];
            $datasets3 = $chartData2['datasets'];
            $datasets4 = $chartData2['datasets2'];

            $achats_delay = $this->achatRepository->yearDelayDiff($form);
            $achats = $this->statisticDelayService->totalDelayPerMonth($achats_delay);
            $achats_delay_all = $this->achatRepository->yearDelayCount($form);

            $result_achats = $this->achatRepository->searchAchatToStat($form);
            $result_achats_mounts = $this->achatRepository->searchAchatToStatMount($form);
            $parameter = $this->parametresRepository->findById(1);

            $result_achatsPME = $this->achatRepository->statisticPMESum($form);
            $result_achatsSum = $this->achatRepository->statisticPMEMonth($form);
            $result_achatsSumVol = $this->achatRepository->statisticPMETopVol($form);
            $result_achatsSumVal = $this->achatRepository->statisticPMETopVal($form);
            $errorMessage = null;

            if (empty($result_achatsSumVal)) {
                $errorMessage = 'Aucun résultat pour cette recherche.';
                return $this->render('cr_annuel/index.html.twig', [
                    'form' => $form->createView(),
                    'result_achatsSumVal' => $result_achatsSumVal,
                    'errorMessage' => $errorMessage,
                ]);
            }
            $filePath = $this->crAnnuelService->generateExcelFile($datasets1, $datasets2, $datasets3, $datasets4, $this->projectDir, $achats, $achats_delay_all,$result_achats,
                                                                $result_achats_mounts, $parameter,$result_achatsPME, $result_achatsSum, $result_achatsSumVol, $result_achatsSumVal);
            return new BinaryFileResponse($filePath);
        }
        return $this->render('cr_annuel/index.html.twig', [
            'form' => $form->createView(),
            'errorMessage' => $errorMessage,

        ]);
    }
}
