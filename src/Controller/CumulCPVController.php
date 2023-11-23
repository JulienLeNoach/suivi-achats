<?php

namespace App\Controller;

use App\Entity\CPV;
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
        #[Route('/cumulcpv', name: 'cumul_cpv')]
        public function index(Request $request): Response
        {
            $form = $this->createForm(CumulCPVType::class);
            $form->handleRequest($request);
            $result_cpv= null;
            $page = $request->query->get('page', 1); 
            if ($form->isSubmitted() && $form->isValid()) {

                $result_cpv = $this->entityManager->getRepository(CPV::class)->showCPV($form,$page);
                $alertValue = $form["alertValue"]->getData();

                return $this->render('cumul_cpv/index.html.twig', [
                    'form' => $form->createView(),
                    'result_cpv' => $result_cpv,
                    'alertValue' => $alertValue,

                ]);
            }

            return $this->render('cumul_cpv/index.html.twig', [
                'form' => $form->createView(),
                'result_cpv' => $result_cpv,
                
            ]);
        }
    }
