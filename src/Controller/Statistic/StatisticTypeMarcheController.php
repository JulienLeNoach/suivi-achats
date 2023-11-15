<?php

namespace App\Controller\Statistic;

use App\Form\StatisticType;
use App\Repository\AchatRepository;
use App\Repository\ParametresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticTypeMarcheController extends AbstractController
{
    private $entityManager;
    private $achatRepository;
    private $parametresRepository;

    public function __construct(EntityManagerInterface $entityManager, AchatRepository $achatRepository,ParametresRepository $parametresRepository)
    {
        $this->entityManager = $entityManager;
        $this->achatRepository = $achatRepository;
        $this->parametresRepository = $parametresRepository;

    }


    #[Route('/statistic/typemarche', name: 'app_statistic_typemarche')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(StatisticType::class, null, []);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result_achats = $this->achatRepository->searchAchatToStat($form);
            $result_achats_mounts = $this->achatRepository->searchAchatToStatMount($form);
            $parameter = $this->parametresRepository->findById(1);
            return $this->render('statistic_type_marche/index.html.twig', [
                'form' => $form->createView(),
                'result_achats' => $result_achats,
                'result_achats_mounts' => $result_achats_mounts,
                'parameter' => $parameter
    ]);
        }
        return $this->render('statistic_type_marche/index.html.twig', [
                    'form' => $form->createView(),
        ]);
    }
}
