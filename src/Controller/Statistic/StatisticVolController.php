<?php

namespace App\Controller\Statistic;

// ...

use Dompdf\Dompdf;
use App\Form\StatisticType;
use App\Form\CreateExcelType;
use App\Repository\AchatRepository;
use App\Service\StatisticVolValService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
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
        $mppaMtTotal = [];
        $mabcMtTotal = [];
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $mppaMtTotal = $this->achatRepository->getPurchaseCountAndTotalAmount($mppaEtat,$form);
            $mabcMtTotal = $this->achatRepository->getPurchaseCountAndTotalAmount($mabcEtat,$form);
            $VolValStat = $this->statisticService->purchaseStatisticsByMonth($mppaMtTotal,$mabcMtTotal);

            $delayVolVal = $this->achatRepository->volValDelay($form);

            $chartDataCount = $this->statisticService->arrayMapChart( $VolValStat, 'countmppa','countmabc');
            $chartDataTotal = $this->statisticService->arrayMapChart($VolValStat, 'totalmontantmppa','totalmontantmabc');
            $chartDataCountMppa = $chartDataCount['mppa'];
            $chartDataCountMabc = $chartDataCount['mabc'];
            $chartDataTotalMppa = $chartDataTotal['mppa'];
            $chartDataTotalMabc = $chartDataTotal['mabc'];
            
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
                'delayVolVal'=>$delayVolVal,
                'VolValStat' => $VolValStat,

            ];
            $session->set('toPDF', $toPDF);


                return $this->render('statistic/index.html.twig', [
                    'form' => $form->createView(),
                    'excelForm' => $excelForm->createView(),
                    'delayVolVal'=>$delayVolVal,
                    'chartDataCountMppa' => $chartDataCountMppa,
                    'chartDataCountMabc' => $chartDataCountMabc,
                    'chartDataTotalMppa' => $chartDataTotalMppa,
                    'chartDataTotalMabc' => $chartDataTotalMabc,
                    'toPDF' => $toPDF,
                    'VolValStat' => $VolValStat,
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
        
        $html =  $this->renderView('statistic/stat_pdf.html.twig', [
            'criteria' => $session->get('toPDF')["criteria"],
            'delayVolVal'=>$session->get('toPDF')["delayVolVal"],
            'VolValStat'=>$session->get('toPDF')["VolValStat"],


        ]);
        $dompdf = new Dompdf();
        $dompdf->setPaper('A3', 'landscape');

        $dompdf->loadHtml($html);
        $dompdf->render();
         
        $dompdf->stream('stat_vol', array('Attachment' => 0));
        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

/**
 * @Route("/statistic/vol/export_excel", name="app_statistic_vol_export_excel")
 */
public function exportExcel(Request $request, StatisticVolValService $statisticService): Response
{
    // Traitez la requête pour obtenir les données nécessaires à l'export Excel
    // Supposons que les données sont passées via une requête GET ou POST
    $chartDataCountMppa = $request->get('chartDataCountMppa');
    $chartDataCountMabc = $request->get('chartDataCountMabc');
    $chartDataTotalMppa = $request->get('chartDataTotalMppa');
    $chartDataTotalMabc = $request->get('chartDataTotalMabc');

    // Convertir les données JSON en tableau PHP
    $chartDataCountMppa = json_decode($chartDataCountMppa, true);
    $chartDataCountMabc = json_decode($chartDataCountMabc, true);
    $chartDataTotalMppa = json_decode($chartDataTotalMppa, true);
    $chartDataTotalMabc = json_decode($chartDataTotalMabc, true);

    // Générer le fichier Excel
    $filePath = $statisticService->generateExcelFile($chartDataCountMppa, $chartDataCountMabc, $chartDataTotalMppa, $chartDataTotalMabc, $this->projectDir);
    return new BinaryFileResponse($filePath);
}
}
