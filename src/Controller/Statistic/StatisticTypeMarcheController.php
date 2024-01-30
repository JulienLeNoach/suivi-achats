<?php

namespace App\Controller\Statistic;

use Dompdf\Dompdf;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function index(Request $request, SessionInterface $session): Response
    {
        $errorMessage = 'Aucun résultat pour cette recherche.';

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
            $toPDF=[
                'criteria'=>[
                'Date' =>  $form["date"]->getData(),
                'Fournisseur' =>  ($form["num_siret"]->getData() !== null) ? $form["num_siret"]->getData()->getNomFournisseur() : null,
                'Utilisateur' =>  ($form["utilisateurs"]->getData() !== null) ? $form["utilisateurs"]->getData()->getNomConnexion() : null,
                'Unité organique' =>  ($form["code_uo"]->getData() !== null) ? $form["code_uo"]->getData()->getLibelleUo() : null,
                'CPV' =>  ($form["code_cpv"]->getData() !== null) ? $form["code_cpv"]->getData()->getLibelleCPV() : null,
                'Formation ' =>  ($form["code_formation"]->getData() !== null) ? $form["code_formation"]->getData()->getLibelleFormation() : null,
                'Taxe' =>  $form["tax"]->getData(),
                ],
                'result_achats' => $result_achats,
                'result_achats_mounts' => $result_achats_mounts,
                'parameter' => $parameter,

            ];
            $session->set('toPDF', $toPDF);

            $excelForm = $this->createForm(CreateExcelType::class); 
            if (empty($result_achats_mounts)) {
                return $this->render('statistic_type_marche/index.html.twig', [
                    'excelForm' => $excelForm->createView(),
                    'form' => $form->createView(),
                    'result_achats' => $result_achats,
                    'result_achats_mounts' => $result_achats_mounts,
                    'parameter' => $parameter,
                    'toPDF' => $toPDF,
                    'errorMessage' => $errorMessage,
                ]);
            }
            return $this->render('statistic_type_marche/index.html.twig', [
                'excelForm' => $excelForm->createView(),
                'form' => $form->createView(),
                'result_achats' => $result_achats,
                'result_achats_mounts' => $result_achats_mounts,
                'parameter' => $parameter,
                'toPDF' => $toPDF,
                'errorMessage' => $errorMessage,

    ]);

        }

        return $this->render('statistic_type_marche/index.html.twig', [
            'excelForm' => $excelForm->createView(),
            'form' => $form->createView(),
            'result_achats' => $result_achats,
            'result_achats_mounts' => $result_achats_mounts,
            'parameter' => $parameter,
            'errorMessage' => $errorMessage,

                    
        ]);
    }
    #[Route('/pdf/generator/stat_type_marche', name: 'pdf_generator_stat_type_marche')]
    public function pdf(SessionInterface $session): Response
    {
        
        $html =  $this->renderView('statistic_type_marche/stat_pdf.html.twig', [
            'criteria' => $session->get('toPDF')["criteria"],
            'result_achats' => $session->get('toPDF')["result_achats"],
            'result_achats_mounts' => $session->get('toPDF')["result_achats_mounts"],
            'parameter' => $session->get('toPDF')["parameter"],

        ]);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
         
        $dompdf->stream('stat_type', array('Attachment' => 0));
        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
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
