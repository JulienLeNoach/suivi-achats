<?php

namespace App\Controller\Statistic;

use App\Form\StatisticType;
use App\Repository\AchatRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticPMEController extends AbstractController
{

    private $achatRepository;

    public function __construct(AchatRepository $achatRepository)
    {
        $this->achatRepository = $achatRepository;

    }

    
    #[Route('/statisticpme', name: 'app_statisticpme')]
    public function index(Request $request): Response
    { 
        
        $form = $this->createForm(StatisticType::class, null, []);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result_achats = $this->achatRepository->statisticPMESum($form);
            $result_achatsSum = $this->achatRepository->statisticPMEMonth($form);
            $result_achatsSumVol = $this->achatRepository->statisticPMETopVol($form);
            $result_achatsSumVal = $this->achatRepository->statisticPMETopVal($form);
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
