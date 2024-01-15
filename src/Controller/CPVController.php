<?php

namespace App\Controller;

use App\Entity\CPV;
use App\Form\CPVType;
use App\Repository\CPVRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/cpv')]
#[IsGranted('ROLE_OPT_CPV')]

class CPVController extends AbstractController
{
    #[Route('/', name: 'cpv', methods: ['GET'])]
    public function index(CPVRepository $cPVRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search', '');
        $perPage = $request->query->get('perPage', 5);
        $sortField = $request->query->get('sortField', 'id');
        $sortDirection = $request->query->get('sortDirection', 'asc');
    
        $queryBuilder = $cPVRepository->createQueryBuilder('cpv');
    
        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('cpv.code_cpv LIKE :searchTerm OR cpv.libelle_cpv LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    
        $queryBuilder->orderBy("cpv.$sortField", $sortDirection); // Ajout du tri
    
        $query = $queryBuilder->getQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $perPage
        );
    
        return $this->render('cpv/index.html.twig', [
            'pagination' => $pagination,
            'searchTerm' => $searchTerm,
            'perPage' => $perPage,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[Route('/new', name: 'app_c_p_v_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cPV = new CPV();
        $form = $this->createForm(CPVType::class, $cPV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cPV);
            $entityManager->flush();

            return $this->redirectToRoute('cpv', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cpv/new.html.twig', [
            'c_p_v' => $cPV,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_c_p_v_show', methods: ['GET'])]
    public function show(CPV $cPV): Response
    {
        return $this->render('cpv/show.html.twig', [
            'c_p_v' => $cPV,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_c_p_v_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CPV $cPV, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CPVType::class, $cPV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('cpv', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cpv/edit.html.twig', [
            'c_p_v' => $cPV,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_c_p_v_delete', methods: ['POST'])]
    public function delete(Request $request, CPV $cPV, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cPV->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cPV);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cpv', [], Response::HTTP_SEE_OTHER);
    }
}
