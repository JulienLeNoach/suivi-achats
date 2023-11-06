<?php

namespace App\Controller\Statistic;

use App\Form\StatisticType;
use App\Repository\AchatRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticDelayController extends AbstractController
{

    private $achatRepository;

    public function __construct(AchatRepository $achatRepository)
    {

        $this->achatRepository = $achatRepository;

    }

    #[Route('/statistic/delay', name: 'app_statistic_delay')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(StatisticType::class, null, []);
        $form->handleRequest($request);        
        
        if ($form->isSubmitted() && $form->isValid()) {
        if ($form->get('recherche')->isClicked()) {
        // Récupérez les données achats
        $achats = $this->achatRepository->yearDelayAchat($form);
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
            'achats' => $achats,
        ]);
        }
    }

    
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
