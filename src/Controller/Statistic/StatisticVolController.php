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
use Symfony\Component\HttpFoundation\Session\Session;
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

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mpttaEtat = 1;
            $mabcEtat = 0;
            $counts1 = [];
            $counts2 = [];
            $counts1 = $this->achatRepository->getPurchaseCountAndTotalAmount($mpttaEtat,$form);
            $counts1 = $this->statisticService->totalPerMonth($counts1);
            $counts2 = $this->achatRepository->getPurchaseCountAndTotalAmount($mabcEtat,$form);
            $counts2 = $this->statisticService->totalPerMonth($counts2);
            $purchaseCountByMonth = $this->statisticService->purchaseCountByMonth($counts1,$counts2);
            $purchaseTotalAmountByMonth = $this->statisticService->purchaseTotalAmountByMonth($counts1,$counts2);
            $delayVolVal = $this->achatRepository->volValDelay($form);
            $chartData = $this->statisticService->arrayMapChart( $counts1, $counts2, 'count');
            $chartData2 = $this->statisticService->arrayMapChart($counts1, $counts2, 'totalmontant');
            $datasets1 = $chartData['datasets'];
            $datasets2 = $chartData['datasets2'];
            $datasets3 = $chartData2['datasets'];
            $datasets4 = $chartData2['datasets2'];
            
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
                'counts1' => $counts1,
                'counts2' => $counts2,
                'purchaseCountByMonth' => $purchaseCountByMonth,
                'purchaseTotalAmountByMonth' => $purchaseTotalAmountByMonth,
                'delayVolVal'=>$delayVolVal,
            ];
            $session->set('toPDF', $toPDF);


                return $this->render('statistic/index.html.twig', [
                    'form' => $form->createView(),
                    'excelForm' => $excelForm->createView(),
                    'counts1' => $counts1,
                    'counts2' => $counts2,
                    'purchaseCountByMonth' => $purchaseCountByMonth,
                    'purchaseTotalAmountByMonth' => $purchaseTotalAmountByMonth,
                    'delayVolVal'=>$delayVolVal,
                    'datasets1' => $datasets1,
                    'datasets2' => $datasets2,
                    'datasets3' => $datasets3,
                    'datasets4' => $datasets4,
                    'toPDF' => $toPDF,
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
            'counts1' => $session->get('toPDF')["counts1"],
            'counts2' => $session->get('toPDF')["counts2"],
            'purchaseCountByMonth' => $session->get('toPDF')["purchaseCountByMonth"],
            'purchaseTotalAmountByMonth' => $session->get('toPDF')["purchaseTotalAmountByMonth"],
            'delayVolVal'=>$session->get('toPDF')["delayVolVal"],

        ]);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
         
        $dompdf->stream('cumul_cpv', array('Attachment' => 0));
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
    $datasets1 = $request->get('datasets1');
    $datasets2 = $request->get('datasets2');
    $datasets3 = $request->get('datasets3');
    $datasets4 = $request->get('datasets4');

    // Convertir les données JSON en tableau PHP
    $datasets1 = json_decode($datasets1, true);
    $datasets2 = json_decode($datasets2, true);
    $datasets3 = json_decode($datasets3, true);
    $datasets4 = json_decode($datasets4, true);

    // Générer le fichier Excel
    $filePath = $statisticService->generateExcelFile($datasets1, $datasets2, $datasets3, $datasets4, $this->projectDir);
    return new BinaryFileResponse($filePath);
}
}
