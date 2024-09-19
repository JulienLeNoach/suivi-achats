<?php

namespace App\Controller\Statistic;

use Dompdf\Dompdf;
use App\Form\StatisticType;
use App\Form\CreateExcelType;
use App\Repository\AchatRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Service\Statistic\StatisticDelay\CreateExcelDelay;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Statistic\StatisticDelay\StatisticDelayService;
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
        $achats = [];
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des valeurs de délai saisies
            
    
            $achats_delay = $this->achatRepository->getYearDelayDiff($form);
            $achats = $this->statisticDelayService->getDelayPerMonth($achats_delay);
            $achats_delay_all = $this->achatRepository->getYearDelayCount($form);
    
            $toPDF = [
                'criteria' => [
                    'Date' =>  $form["date"]->getData(),
                    'Fournisseur' =>  ($form["num_siret"]->getData() !== null) ? $form["num_siret"]->getData()->getNomFournisseur() : null,
                    'Utilisateur' =>  ($form["utilisateurs"]->getData() !== null) ? $form["utilisateurs"]->getData()->getNomConnexion() : null,
                    'Unité organique' =>  ($form["code_uo"]->getData() !== null) ? $form["code_uo"]->getData()->getLibelleUo() : null,
                    'CPV' =>  ($form["code_cpv"]->getData() !== null) ? $form["code_cpv"]->getData()->getLibelleCPV() : null,
                    'Formation ' =>  ($form["code_formation"]->getData() !== null) ? $form["code_formation"]->getData()->getLibelleFormation() : null,
                    'Taxe' =>  $form["tax"]->getData(),
                ],
                'achats' => $achats,
                'achats_delay_all' => $achats_delay_all,
            ];
            $session->set('toPDF', $toPDF);
    
            $excelForm = $this->createForm(CreateExcelType::class);
    
            return $this->render('statistic_delay/index.html.twig', [
                'form' => $form->createView(),
                'excelForm' => $excelForm->createView(),
                'achats' => $achats,
                'achats_delay_all' => $achats_delay_all,
                'toPDF' => $toPDF
            ]);
        }
    
        return $this->render('statistic_delay/index.html.twig', [
            'form' => $form->createView(),
            'achats' => $achats,
        ]);
    }
    

    // Ajoutez les autres méthodes du contrôleur ici...


    #[Route('/pdf/generator/stat_delay', name: 'pdf_generator_stat_delay')]
    public function pdf(SessionInterface $session): Response
    {
        $html =  $this->renderView('statistic_delay/stat_pdf.html.twig', [
            'criteria' => $session->get('toPDF')["criteria"],
            'achats' => $session->get('toPDF')["achats"],
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
public function exportExcel(Request $request, CreateExcelDelay $createExcelDelay): Response
{
    // Supposons que les données sont passées via une requête GET ou POST
    $achats = $request->get('achats');
    $achats_delay_all = $request->get('achats_delay_all');


    // Convertir les données JSON en tableau PHP
    $achats = json_decode($achats, true);
    $achats_delay_all = json_decode($achats_delay_all, true);


    // Générer le fichier Excel
    $filePath = $createExcelDelay->createExcelFile($achats, $achats_delay_all);
    return new BinaryFileResponse($filePath);

}
}