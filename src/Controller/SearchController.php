<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\Achat;
use App\Form\AchatType;
use App\Form\ValidType;
use App\Form\ImprimerType;
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

    public function __construct(EntityManagerInterface $entityManager,StatisticService $statisticService)
    {
        $this->entityManager = $entityManager;
        $this->statisticService = $statisticService;
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

            // if ($request->isXmlHttpRequest()) {

            //     $partialView = $this->renderView('search/partial_results.html.twig', [
            //         'pagination' => $pagination,
            //     ]);
        
            //     $responseData = ['html' => $partialView];

            //     return new JsonResponse($responseData);
            // }


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
    if (!$result_achat) {
        $this->redirectToRoute('app_search');
    }

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
                // dd($currentUrl);
            // if ($currentUrl) {
                return $this->redirect($currentUrl);
        
            // }
    }
}
    return $this->render('search/result_achat.html.twig', [
        'result_achat' => $result_achat,
        'form' => $form->createView(),

    ]);
}

#[Route('/valid_achat/{id}', name: 'valid_achat')]
public function valid(Request $request,$id,SessionInterface $session): Response
{
    $result_achat = $this->entityManager->getRepository(Achat::class)->findOneById($id);
    if (!$result_achat) {
        $this->redirectToRoute('app_search');
    }

    $form = $this->createForm(ValidType::class, null, []);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        if ($form->get('Valider')->isClicked()) {
        $val = $request->request->get('val');
        $not = $request->request->get('not');
        $ej = $request->request->get('ej');
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder->update(Achat::class, 'u')
            ->set('u.etat_achat', ':etat_achat')
            ->set('u.date_validation', ':date_validation')
            ->set('u.date_notification', ':date_notification')
            ->set('u.numero_ej', ':numero_ej')
            ->where('u.id = :id')
            ->setParameter('etat_achat', 2)
            ->setParameter('date_validation', $val)
            ->setParameter('date_notification', $not)
            ->setParameter('numero_ej', $ej)
            ->setParameter('id', $id)
            ->getQuery();
        $result = $query->execute();
        $this->addFlash('success', "L'achat n° $id est validé");
        return $this->redirectToRoute('valid_achat', ['id' => $id]);
        }if ($form->get('return')->isClicked()) {
            $currentUrl = $session->get('current_url');
                // dd($currentUrl);
            // if ($currentUrl) {
                return $this->redirect($currentUrl);
        
            // }
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

    $queryBuilder = $this->entityManager->createQueryBuilder();
    $query = $queryBuilder->update(Achat::class, 'u')
        ->set('u.etat_achat', ':etat_achat')
        ->where('u.id = :id')
        ->setParameter('etat_achat', 1)
        ->setParameter('id', $id)
        ->getQuery();
    $result = $query->execute();
    $this->addFlash('success', 'Achat n° ' . $id . "annulé");

    return $this->redirect($currentUrl);
}

#[Route('/reint_achat/{id}', name: 'reint_achat')]
public function reint($id, Request $request,SessionInterface $session): Response
{
    $currentUrl = $session->get('current_url');
    $queryBuilder = $this->entityManager->createQueryBuilder();
            $query = $queryBuilder->update(Achat::class, 'u')
                ->set('u.etat_achat', ':etat_achat')
                ->where('u.id = :id')
                ->setParameter('etat_achat', 0)
                ->setParameter('id', $id)
                ->getQuery();
            $result = $query->execute();
            $this->addFlash('success', 'Achat n° ' . $id . "réintégré");


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
        
            // }
    }
        $achatRepository->edit($achat, true);

        $this->addFlash('success', 'Achat n° ' . $id . "modifié");
        return $this->redirect($currentUrl);
    }
    return $this->renderForm('achat/edit_achat_id.html.twig', [
        'achat' => $achat,
        'form' => $form,
    ]);
}
}