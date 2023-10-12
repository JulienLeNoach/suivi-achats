<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\Achat;
use App\Form\AchatType;
use App\Form\ValidType;
use App\Form\AddAchatType;
use App\Form\ImprimerType;
use App\Factory\AchatFactory;
use App\Form\AchatSearchType;
use App\Service\StatisticService;
use App\Repository\AchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Knp\Component\Pager\Pagination\SlidingPaginationInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private $entityManager;
    private $pagination;
    private $statisticService;
    private $achatFactory;

    public function __construct(EntityManagerInterface $entityManager,StatisticService $statisticService,AchatFactory $achatFactory)
    {
        $this->entityManager = $entityManager;
        $this->statisticService = $statisticService;
        $this->achatFactory = $achatFactory;

    }

    // Cette fonction gère une requête de recherche avec un formulaire.
    // Elle récupère les données soumises dans le formulaire, construit
    // une requête de recherche en fonction de ces données, exécute la
    // requête et renvoie le résultat sous forme d'une vue Twig rendue
    // avec les données du formulaire et les résultats de la recherche.

    #[Route('/search', name: 'app_search')]
    public function index(Request $request, EntityManagerInterface $entityManager,PaginatorInterface $paginator,SessionInterface $session): Response
    {


        // $allAchats = $this->entityManager->getRepository(Achat::class)->findAll();

        $form = $this->createForm(AchatSearchType::class, null, [
            // 'allAchats' => $allAchats,
        ]);

        $form->handleRequest($request);

        $pagination = null; // Initialiser $pagination à null
        if($form->isSubmitted() && $form->isValid()){

            $query = $this->entityManager->getRepository(Achat::class)->searchAchat($form, $paginator);
            $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10);
            $currentUrl = $request->getUri();
            $session->set('current_url', $currentUrl);
        }


        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            // 'allAchats' => $allAchats,
        ]);
    }

// 
#[Route('/result_achat/{id}', name: 'achat_result')]
public function show(Request $request,$id,SessionInterface $session): Response
{
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);

    $form = $this->createForm(ImprimerType::class, null, []);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if ($form->get('print')->isClicked()) {

            $html = $this->render('search/pdf_template.html.twig', [
                'result_achat' => $result_achat,
                'form' => $form->createView(),
            ]);

            return $this->statisticService->generatePDF($html);
        }
        if ($form->get('return')->isClicked()) {
            $currentUrl = $session->get('current_url');

                return $this->redirect($currentUrl);
        
    }
}
    return $this->render('search/result_achat.html.twig', [
        'result_achat' => $result_achat,
        'form' => $form->createView(),

    ]);
}
#[Route('/ajout_achat', name: 'ajout_achat')]
public function add(Request $request,SessionInterface $session): Response
{
    
    $achat = $this->achatFactory->create();
    $form = $this->createForm(AddAchatType::class, $achat);
    $form->handleRequest($request);
    $currentUrl = $session->get('current_url');



    if ($form->isSubmitted() && $form->isValid()) {

        if ($form->get('Valider')->isClicked()) {

        $query = $this->entityManager->getRepository(Achat::class)->add($achat);
        $this->addFlash('success', 'Nouvel achat n° ' . $achat->getId() . " sauvegardé");
        return $this->redirect($currentUrl);


    }if ($form->get('return')->isClicked()) {

                return $this->redirect($currentUrl);
        
    }
}
    return $this->render('achat/addAchat.html.twig', [
        'form' => $form->createView(),

    ]);
}
#[Route('/valid_achat/{id}', name: 'valid_achat')]
public function valid(Request $request,$id,SessionInterface $session): Response
{
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);

    $form = $this->createForm(ValidType::class, null, []);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        if ($form->get('Valider')->isClicked()) {

        $query = $this->entityManager->getRepository(Achat::class)->valid($request, $id);
        $this->addFlash('success', "L'achat n° $id est validé");

        return $this->redirectToRoute('valid_achat', ['id' => $id]);
        }if ($form->get('return')->isClicked()) {
            $currentUrl = $session->get('current_url');

                return $this->redirect($currentUrl);
        
    }
}
    return $this->render('achat/valid_achat.html.twig', [
        'result_achat' => $result_achat,
        'form' => $form->createView(),

    ]);
}

#[Route('/annul_achat/{id}', name: 'annul_achat')]
public function cancel($id, Request $request,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $query = $this->entityManager->getRepository(Achat::class)->cancel($id);

    $this->addFlash('success', 'Achat n° ' . $id . "annulé");

    return $this->redirect($currentUrl);
}

#[Route('/reint_achat/{id}', name: 'reint_achat')]
public function reint($id, Request $request,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $query = $this->entityManager->getRepository(Achat::class)->reint($id);

            $this->addFlash('success', 'Achat n° ' . $id . " réintégré");


    return $this->redirect($currentUrl);
}

#[Route('/edit_achat/{id}', name: 'edit_achat', methods: ['GET', 'POST'])]
public function edit(Request $request, $id, Achat $achat,  AchatRepository $achatRepository,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');

    $form = $this->createForm(AchatType::class, $achat);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if ($form->get('return')->isClicked()) {
            $currentUrl = $session->get('current_url');

                return $this->redirect($currentUrl);
        
    }
        $achatRepository->edit($achat, true);

        $this->addFlash('success', 'Achat n° ' . $id . " modifié");
        return $this->redirect($currentUrl);
    }
    return $this->renderForm('achat/edit_achat_id.html.twig', [
        'achat' => $achat,
        'form' => $form,
    ]);
}
}