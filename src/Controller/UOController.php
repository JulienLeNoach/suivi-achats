<?php

namespace App\Controller;

use App\Entity\UO;
use App\Form\UOType;
use App\Repository\UORepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/uo')]
#[IsGranted('ROLE_OPT_UO')]

class UOController extends AbstractController
{
    #[Route('/', name: 'app_u_o_index', methods: ['GET'])]
    public function index(UORepository $uORepository,Request $request, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search', '');
        $perPage = $request->query->get('perPage', 5);
        $sortField = $request->query->get('sortField', 'id');
        $sortDirection = $request->query->get('sortDirection', 'asc');
    
        $queryBuilder = $uORepository->createQueryBuilder('uo');
    
        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('uo.code_uo LIKE :searchTerm OR uo.libelle_uo LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    
        $queryBuilder->orderBy("uo.$sortField", $sortDirection); // Ajout du tri
    
        $query = $queryBuilder->getQuery();
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $perPage
        );
    
        return $this->render('uo/index.html.twig', [
            'pagination' => $pagination,
            'searchTerm' => $searchTerm,
            'perPage' => $perPage,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[Route('/new', name: 'app_u_o_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uO = new UO();
        $form = $this->createForm(UOType::class, $uO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($uO);
            $entityManager->flush();

            return $this->redirectToRoute('app_u_o_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('uo/new.html.twig', [
            'u_o' => $uO,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_u_o_show', methods: ['GET'])]
    public function show(UO $uO): Response
    {
        return $this->render('uo/show.html.twig', [
            'u_o' => $uO,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_u_o_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UO $uO, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UOType::class, $uO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_u_o_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('uo/edit.html.twig', [
            'u_o' => $uO,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_u_o_delete', methods: ['POST'])]
    public function delete(Request $request, UO $uO, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$uO->getId(), $request->request->get('_token'))) {
            $entityManager->remove($uO);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_u_o_index', [], Response::HTTP_SEE_OTHER);
    }
}
