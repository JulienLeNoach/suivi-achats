<?php

namespace App\Controller\Statistic;

// ...

use Dompdf\Dompdf;
use App\Form\StatisticType;
use App\Form\CreateExcelType;
use App\Repository\AchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Service\Statistic\VolVal\CreateExcelVolVal;
use App\Service\Statistic\VolVal\StatisticVolValService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//La méthode showStat de la classe, qui est associée à la route '/statistic',
//est la fonction principale pour afficher les statistiques.
//D'abord, elle crée un formulaire à l'aide de la classe ValidEditAchatTyê
// et gère la requête HTTP entrante. Si le formulaire est soumis et validé,
// elle récupère la somme des achats pour deux types spécifiques
// (mpttaEtat et mabcEtat) via la méthode getCountsByDateAndType du dépôt AchatRepository.
//Ensuite, elle combine ces sommes par mois en un seul tableau, en incluant le total pour chaque mois.



class StatisticVolController extends AbstractController
{
    private $entityManager;
    private $achatRepository;
    private $projectDir;
    private $statisticService;
    public function __construct(EntityManagerInterface $entityManager, AchatRepository $achatRepository, KernelInterface $kernel, StatisticVolValService $statisticService)
    {
        $this->entityManager = $entityManager;
        $this->statisticService = $statisticService;
        $this->achatRepository = $achatRepository;
        $this->projectDir = $kernel->getProjectDir();
    }

        #[Route('/statistic/vol', name: 'app_statistic_vol')]
        public function showStat(Request $request, SessionInterface $session): Response
        {
            $form = $this->createForm(StatisticType::class, null, []);
            $excelForm = $this->createForm(CreateExcelType::class); 
            $mppaEtat = 1;
            $mabcEtat = 0;
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $mppaMtTotal = $this->achatRepository->getPurchaseCountAndTotalAmount($mppaEtat, $form);
                $mabcMtTotal = $this->achatRepository->getPurchaseCountAndTotalAmount($mabcEtat, $form);
        
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
        
                // dd($chartDataCountCurrent, $chartDataCountPrevious, $chartDataTotalCurrent, $chartDataTotalPrevious);
                $anne_precedente=$form["annee_precedente"]->getData();
                $toPDF = [
                    'criteria' => [
                        'Date' => $form["date"]->getData(),
                        'Fournisseur' => ($form["num_siret"]->getData() !== null) ? $form["num_siret"]->getData()->getNomFournisseur() : null,
                        'Utilisateur' => ($form["utilisateurs"]->getData() !== null) ? $form["utilisateurs"]->getData()->getNomConnexion() : null,
                        'Unité organique' => ($form["code_uo"]->getData() !== null) ? $form["code_uo"]->getData()->getLibelleUo() : null,
                        'CPV' => ($form["code_cpv"]->getData() !== null) ? $form["code_cpv"]->getData()->getLibelleCPV() : null,
                        'Formation ' => ($form["code_formation"]->getData() !== null) ? $form["code_formation"]->getData()->getLibelleFormation() : null,
                        'Taxe' => $form["tax"]->getData(),
                    ],
                    'delayVolVal' => $delayVolVal,
                    'volValStat' => $volValStat,
                    'chartDataCountCurrent' => $chartDataCountCurrent,
                    'chartDataCountPrevious' => $chartDataCountPrevious,
                    'chartDataTotalCurrent' => $chartDataTotalCurrent,
                    'chartDataTotalPrevious' => $chartDataTotalPrevious,
                    'annee_precedente'=>$anne_precedente,
                ];
                $session->set('toPDF', $toPDF);
        
                return $this->render('statistic/index.html.twig', [
                    'form' => $form->createView(),
                    'excelForm' => $excelForm->createView(),
                    'delayVolVal' => $delayVolVal,
                    'chartDataCountCurrent' => $chartDataCountCurrent,
                    'chartDataCountPrevious' => $chartDataCountPrevious,
                    'chartDataTotalCurrent' => $chartDataTotalCurrent,
                    'chartDataTotalPrevious' => $chartDataTotalPrevious,
                    'toPDF' => $toPDF,
                    'volValStat' => $volValStat,
                    'annee_precedente'=>$anne_precedente,
                ]);
            }
        
            return $this->render('statistic/index.html.twig', [
                'form' => $form->createView(),
                'excelForm' => $excelForm->createView(),
            ]);
        }
    




        #[Route('/pdf/generator/stat_vol', name: 'pdf_generator_stat_vol')]
        public function pdf(SessionInterface $session): Response
        {
            $toPDF = $session->get('toPDF');
            
            $html =  $this->renderView('statistic/stat_pdf.html.twig', [
                'criteria' => $toPDF["criteria"],
                'delayVolVal' => $toPDF["delayVolVal"],
                'VolValStat' => $toPDF["volValStat"],
                'annee_precedente' => $toPDF["annee_precedente"],
            ]);
        
            $dompdf = new Dompdf();
            $dompdf->setPaper('A3', 'landscape');
            $dompdf->loadHtml($html);
            $dompdf->render();
            $dompdf->stream('stat_vol', ['Attachment' => 0]);
        
            return new Response('', 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }
        

/**
 * @Route("/statistic/vol/export_excel", name="app_statistic_vol_export_excel")
 */
public function exportExcel(Request $request, CreateExcelVolVal $createExcelVolVal): Response
{
    // Traitez la requête pour obtenir les données nécessaires à l'export Excel
    $chartDataCountCurrent = $request->get('chartDataCountCurrent');
    $chartDataCountPrevious = $request->get('chartDataCountPrevious');
    $chartDataTotalCurrent = $request->get('chartDataTotalCurrent');
    $chartDataTotalPrevious = $request->get('chartDataTotalPrevious');

    // Convertir les données JSON en tableau PHP
    $chartDataCountCurrent = json_decode($chartDataCountCurrent, true);
    $chartDataCountPrevious = json_decode($chartDataCountPrevious, true);
    $chartDataTotalCurrent = json_decode($chartDataTotalCurrent, true);
    $chartDataTotalPrevious = json_decode($chartDataTotalPrevious, true);

    // Générer le fichier Excel
    $filePath = $createExcelVolVal->generateExcelFile(
        $chartDataCountCurrent, 
        $chartDataCountPrevious, 
        $chartDataTotalCurrent, 
        $chartDataTotalPrevious, 
        $this->getParameter('kernel.project_dir')
    );

    return new BinaryFileResponse($filePath);
}

}
