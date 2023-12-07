<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Form\DataExtractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DataExtractController extends AbstractController
{

    private $entityManager;
    private $pagination;
    private $statisticService;
    private $achatFactory;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    
    #[Route('/dataextract', name: 'data_extract')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(DataExtractType::class, null, [
        ]);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $achat = $this->entityManager->getRepository(Achat::class)->extractSearchAchat($form)->getResult();

             dd($achat);

        }
        return $this->render('data_extract/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
