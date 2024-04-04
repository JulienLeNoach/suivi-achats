<?php

namespace App\Controller\Statistic;
use Dompdf\Dompdf;
use App\Form\StatisticType;
use App\Form\CreateExcelType;
use App\Repository\AchatRepository;
use App\Service\Statistic\PME\CreateExcelPME;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class StatisticPMEController extends AbstractController
    {

        private $achatRepository;
        private $projectDir;

        public function __construct(AchatRepository $achatRepository,KernelInterface $kernel)
        {
            $this->achatRepository = $achatRepository;
            $this->projectDir = $kernel->getProjectDir();


        }

        
        #[Route('/statisticpme', name: 'app_statisticpme')]
        public function index(Request $request, SessionInterface $session): Response
        { 
            
            $form = $this->createForm(StatisticType::class, null, []);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $result_achats = $this->achatRepository->getPMESum($form);
                $result_achatsSum = $this->achatRepository->getPMEMonthSum($form);
                $result_achatsSumVol = $this->achatRepository->getPMETopVol($form);
                $result_achatsSumVal = $this->achatRepository->getPMETopVal($form);
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
                    'result_achatsSum' => $result_achatsSum,
                    'result_achatsSumVol' => $result_achatsSumVol,
                    'result_achatsSumVal' => $result_achatsSumVal,
    
                ];
                $session->set('toPDF', $toPDF);

                $excelForm = $this->createForm(CreateExcelType::class); 

                return $this->render('statistic_pme/index.html.twig', [
                    'form' => $form->createView(),
                    'excelForm' => $excelForm->createView(),
                    'result_achats'=>$result_achats,
                    'result_achatsSum'=>$result_achatsSum,
                    'result_achatsSumVol'=>$result_achatsSumVol,
                    'result_achatsSumVal'=>$result_achatsSumVal,
                    'toPDF' => $toPDF


        ]);
            }
            return $this->render('statistic_pme/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        #[Route('/pdf/generator/stat_pme', name: 'pdf_generator_stat_pme')]
        public function pdf(SessionInterface $session): Response
        {
            
            $html =  $this->renderView('statistic_pme/stat_pdf.html.twig', [
                'criteria' => $session->get('toPDF')["criteria"],
                'result_achats' => $session->get('toPDF')["result_achats"],
                'result_achatsSum' => $session->get('toPDF')["result_achatsSum"],
                'result_achatsSumVol' => $session->get('toPDF')["result_achatsSumVol"],
                'result_achatsSumVal' => $session->get('toPDF')["result_achatsSumVal"],
            ]);
            $dompdf = new Dompdf();
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->loadHtml($html);
            $dompdf->render();
             
            $dompdf->stream('stat_pme', array('Attachment' => 0));
            return new Response('', 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }
        /**
 * @Route("/statistic/pme/export_excel", name="app_statistic_pme_export_excel")
 */
public function exportExcel(Request $request, CreateExcelPME $createExcelPME): Response
{
    // Traitez la requête pour obtenir les données nécessaires à l'export Excel
    // Supposons que les données sont passées via une requête GET ou POST
    $result_achatsPME = $request->get('result_achats');
    $result_achatsSum = $request->get('result_achatsSum');
    $result_achatsSumVol = $request->get('result_achatsSumVol');
    $result_achatsSumVal = $request->get('result_achatsSumVal');

    // Convertir les données JSON en tableau PHP
    $result_achatsPME = json_decode($result_achatsPME, true);
    $result_achatsSum = json_decode($result_achatsSum, true);
    $result_achatsSumVol = json_decode($result_achatsSumVol, true);
    $result_achatsSumVal = json_decode($result_achatsSumVal, true);

    // Générer le fichier Excel
    $filePath = $createExcelPME->createExcelFile($result_achatsPME, $result_achatsSum, $result_achatsSumVol, $result_achatsSumVal);
    return new BinaryFileResponse($filePath);
}
    }
