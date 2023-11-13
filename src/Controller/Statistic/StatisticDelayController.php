<?php

namespace App\Controller\Statistic;

use App\Form\StatisticType;
use App\Repository\AchatRepository;
use App\Service\StatisticDelayService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticDelayController extends AbstractController
{

    private $achatRepository;
    private $statisticDelayService;

    public function __construct(AchatRepository $achatRepository, StatisticDelayService $statisticDelayService)
    {

        $this->achatRepository = $achatRepository;
        $this->statisticDelayService = $statisticDelayService;

    }

    #[Route('/statistic/delay', name: 'app_statistic_delay')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(StatisticType::class, null, []);
        $form->handleRequest($request);        
        $achats_delay = $this->achatRepository->yearDelayAchat($form);
        $achats = $this->statisticDelayService->totalDelayPerMonth($achats_delay);
        $achats_delay_all = $this->achatRepository->yearDelayAchat2($form);

        if ($form->isSubmitted() && $form->isValid()) {
           
        if ($form->get('recherche')->isClicked()) {
        // Récupérez les données achats
        // dd($achats);
        $transStat = array_filter(array_values($achats[2]), 'is_numeric');
        $notStat = array_filter(array_values($achats[5]), 'is_numeric');
        // dd($achats[5]);
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
            'achats' => $achats,
            'transStat' => $transStat,
            'notStat' => $notStat,
            'achats_delay_all' => $achats_delay_all,

        ]);
        }

}
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}