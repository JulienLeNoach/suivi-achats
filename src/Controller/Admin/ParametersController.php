<?php

namespace App\Controller\Admin;

use App\Entity\Parametres;
use App\Form\ParametersType;
use App\Repository\ParametresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ParametersController extends AbstractController
{

    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    
    #[Route('/parameters', name: 'parameters')]
    public function index(Request $request,ParametresRepository $parametresRepository): Response
    {
        $parametres = $this->entityManager->getRepository(Parametres::class)->find(1);
        $form = $this->createForm(ParametersType::class,$parametres);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $parametresRepository->save($parametres, true);
    
            $this->addFlash('success', 'Paramètres sauvegardés');
        }

        return $this->render('parameters/index.html.twig', [
            'form' => $form->createView(),
            'parametres' => $parametres,
        ]);
    }
}
