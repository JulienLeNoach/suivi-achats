<?php

namespace App\Controller\Statistic;

use Dompdf\Dompdf;
use App\Form\StatisticType;
use App\Form\CreateExcelType;
use App\Repository\AchatRepository;
use App\Service\StatisticDelayService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticDelayController extends AbstractController
{

    private $achatRepository;
    private $statisticDelayService;
    private $projectDir;

    public function __construct(AchatRepository $achatRepository,KernelInterface $kernel, StatisticDelayService $statisticDelayService)
    {

        $this->achatRepository = $achatRepository;
        $this->statisticDelayService = $statisticDelayService;
        $this->projectDir = $kernel->getProjectDir();

    }

    #[Route('/statistic/delay', name: 'app_statistic_delay')]
    public function index(Request $request, SessionInterface $session): Response
    {

        $form = $this->createForm(StatisticType::class, null, []);
        $form->handleRequest($request);        
        $achats[]=null;

        if ($form->isSubmitted() && $form->isValid()) {
            $achats_delay = $this->achatRepository->yearDelayDiff($form);
            $achats = $this->statisticDelayService->totalDelayPerMonth($achats_delay);
            $achats_delay_all = $this->achatRepository->yearDelayCount($form);
            
        $transStat = array_filter(array_values($achats[2]), 'is_numeric');
        if (isset($achats[5]) && is_array($achats[5])) {
            $notStat = array_filter(array_values($achats[5]), 'is_numeric');
        } else {
            // La clé 5 n'existe pas ou n'est pas un tableau, gérer l'erreur ici
            // Par exemple, vous pouvez définir $notStat comme un tableau vide ou lancer une exception
            $notStat = [];
            // ou
            // throw new \Exception("La clé 5 n'existe pas ou n'est pas un tableau.");
        }
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
            'achats' => $achats,
            'transStat' => $transStat,
            'notStat' => $notStat,
            'achats_delay_all' => $achats_delay_all,
        ];
        $session->set('toPDF', $toPDF);
        $excelForm = $this->createForm(CreateExcelType::class); 
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
            'excelForm' => $excelForm->createView(),
            'achats' => $achats,
            'transStat' => $transStat,
            'notStat' => $notStat,
            'achats_delay_all' => $achats_delay_all,
            'toPDF' => $toPDF

        ]);

        }
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
            'achats' => $achats,
        ]);
    }

    #[Route('/pdf/generator/stat_delay', name: 'pdf_generator_stat_delay')]
    public function pdf(SessionInterface $session): Response
    {
        
        $html =  $this->renderView('statistic_delay/stat_pdf.html.twig', [
            'criteria' => $session->get('toPDF')["criteria"],
            'achats' => $session->get('toPDF')["achats"],
            'transStat' => $session->get('toPDF')["transStat"],
            'notStat' => $session->get('toPDF')["notStat"],
            'achats_delay_all' => $session->get('toPDF')["achats_delay_all"],

        ]);
    
        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);
        $dompdf->render();
         
        $dompdf->stream('stat_delay', array('Attachment' => 0));
        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
/**
 * @Route("/statistic/delay/export_excel", name="app_statistic_delay_export_excel")
 */
public function exportExcel(Request $request, StatisticDelayService $statisticDelayService): Response
{
    // Supposons que les données sont passées via une requête GET ou POST
    $achats = $request->get('achats');
    $achats_delay_all = $request->get('achats_delay_all');


    // Convertir les données JSON en tableau PHP
    $achats = json_decode($achats, true);
    $achats_delay_all = json_decode($achats_delay_all, true);


    // Générer le fichier Excel
    $filePath = $statisticDelayService->createExcelFile($achats, $achats_delay_all);
    return new BinaryFileResponse($filePath);

}
}