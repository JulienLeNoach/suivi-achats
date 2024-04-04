<?php

namespace App\Controller\Statistic;

use Dompdf\Dompdf;
use App\Entity\CPV;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
    use App\Form\CumulCPVType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class CumulCPVController extends AbstractController
    {

        private $entityManager;


        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;

        }
        #[Route('/pdf/generator/cpv', name: 'pdf_generator_cpv')]
        public function pdf(SessionInterface $session): Response
        {
            $form = $this->createForm(CumulCPVType::class);
    
            $criteriaCPV=  $session->get('criteriaCPV');
            $result_cpv = $this->entityManager->getRepository(CPV::class)->showCPV($criteriaCPV)->getResult();
            $html =  $this->renderView('cumul_cpv/pdf_export.html.twig', [
                'result_cpv' => $result_cpv,
                'criteriaCPV' => $criteriaCPV,
            ]);
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
             
            $dompdf->stream('cumul_cpv', array('Attachment' => 0));
            return new Response('', 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        #[Route('/cumulcpv', name: 'cumul_cpv')]
        public function index(Request $request,SessionInterface $session): Response
        {
            $form = $this->createForm(CumulCPVType::class);
            $form->handleRequest($request);
            $result_cpv= null;
            $errorMessage = null;

            $offset = $request->query->getInt('offset', 0); // Décalage pour le chargement infini
            $alertValue = $form["alertValue"]->getData();
            if ($form->isSubmitted() && $form->isValid()) {
                $criteriaCPV=[
                    'alertValue' =>  $form["alertValue"]->getData(),
                    'date' => $form->get('date')->getData(),
                ];
                $session->set('criteriaCPV', $criteriaCPV);

                $result_cpv = $this->entityManager->getRepository(CPV::class)->showCPV($criteriaCPV);
                $result_cpv = $result_cpv->setFirstResult($offset)->getResult();

                $alertValue = $form["alertValue"]->getData();


                if (empty($result_cpv)) {
                    $errorMessage = 'Aucun résultat pour cette recherche.';
                    return $this->render('cumul_cpv/index.html.twig', [
                        'form' => $form->createView(),
                        'result_cpv' => $result_cpv,
                        'errorMessage' => $errorMessage,
                        'alertValue' => $alertValue,

                    ]);
                }

                return $this->render('cumul_cpv/index.html.twig', [
                    'form' => $form->createView(),
                    'result_cpv' => $result_cpv,
                    'alertValue' => $alertValue,
                    'errorMessage' => $errorMessage,


                ]);
            }
               
            return $this->render('cumul_cpv/index.html.twig', [
                'form' => $form->createView(),
                'result_cpv' => $result_cpv,
                'errorMessage' => $errorMessage,

                
            ]);
        }
    }
