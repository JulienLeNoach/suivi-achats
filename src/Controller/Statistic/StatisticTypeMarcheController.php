<?php

namespace App\Controller\Statistic;

use App\Form\StatisticType;
use App\Form\CreateExcelType;
use App\Repository\AchatRepository;
use App\Repository\ParametresRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Service\StatisticTypeMarcheService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticTypeMarcheController extends AbstractController
{
    private $entityManager;
    private $achatRepository;
    private $parametresRepository;
    private $projectDir;

    public function __construct(EntityManagerInterface $entityManager,KernelInterface $kernel, AchatRepository $achatRepository,ParametresRepository $parametresRepository)
    {
        $this->entityManager = $entityManager;
        $this->achatRepository = $achatRepository;
        $this->parametresRepository = $parametresRepository;
        $this->projectDir = $kernel->getProjectDir();

    }


    #[Route('/statistic/typemarche', name: 'app_statistic_typemarche')]
    public function index(Request $request,StatisticTypeMarcheService $statisticDelayService): Response
    {
        $form = $this->createForm(StatisticType::class, null, []);

        $form->handleRequest($request);
        $result_achats = $this->achatRepository->searchAchatToStat($form);
        $result_achats_mounts = $this->achatRepository->searchAchatToStatMount($form);
        $parameter = $this->parametresRepository->findById(1);
        $excelForm = $this->createForm(CreateExcelType::class); 
        $excelForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $result_achats = $this->achatRepository->searchAchatToStat($form);
            $result_achats_mounts = $this->achatRepository->searchAchatToStatMount($form);
            $parameter = $this->parametresRepository->findById(1);
            $excelForm = $this->createForm(CreateExcelType::class); 

            return $this->render('statistic_type_marche/index.html.twig', [
                'excelForm' => $excelForm->createView(),
                'form' => $form->createView(),
                'result_achats' => $result_achats,
                'result_achats_mounts' => $result_achats_mounts,
                'parameter' => $parameter
    ]);

        }
        // if ($excelForm->isSubmitted() && $excelForm->isValid()) {
        //     $form = $this->createForm(StatisticType::class, null, []);

        // $form->handleRequest($request);
        //     $result_achats = $this->achatRepository->searchAchatToStat($form);
        //     $result_achats_mounts = $this->achatRepository->searchAchatToStatMount($form);
        //     $parameter = $this->parametresRepository->findById(1);
        //     $filePath = $statisticDelayService->generateExcelFile($result_achats, $result_achats_mounts, $parameter, $this->projectDir);
        //     return new BinaryFileResponse($filePath);
    
        // }
        return $this->render('statistic_type_marche/index.html.twig', [
            'excelForm' => $excelForm->createView(),
            'form' => $form->createView(),
            'result_achats' => $result_achats,
            'result_achats_mounts' => $result_achats_mounts,
            'parameter' => $parameter
                    
        ]);
    }
    /**
 * @Route("/statistic/typemarche/excel", name="app_statistic_typemarche_excel")
 */
public function exportExcel(Request $request, StatisticTypeMarcheService $statisticDelayService): Response
{
    // Créer et gérer le formulaire
    $result_achats = $request->get('result_achats');
    $result_achats_mounts = $request->get('result_achats_mounts');
    $parameter2 = $request->get('parameter2');
    $parameter3 = $request->get('parameter3');
    $parameter4 = $request->get('parameter4');

    // Convertir les données JSON en tableau PHP
    $result_achats = json_decode($result_achats, true);
    $result_achats_mounts = json_decode($result_achats_mounts, true);
    $parameter2 = json_decode($parameter2, true);
    $parameter3 = json_decode($parameter3, true);
    $parameter4 = json_decode($parameter4, true);

    
    $parameters = [
        'parameter2' => $parameter2,
        'parameter3' => $parameter3,
        'parameter4' => $parameter4,
    ];

        $filePath = $statisticDelayService->generateExcelFile($result_achats, $result_achats_mounts, $parameters, $this->projectDir);
        return new BinaryFileResponse($filePath);
    

    // Gérer le cas où le formulaire n'est pas soumis ou valide
    $this->addFlash('error', 'Le formulaire n\'est pas valide.');
    return $this->redirectToRoute('app_statistic_typemarche');
}
}
