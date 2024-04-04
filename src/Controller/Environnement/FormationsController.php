<?php

namespace App\Controller\Environnement;

use App\Entity\Formations;
use App\Form\FormationsType;
use App\Repository\FormationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/formations')]
#[IsGranted('ROLE_OPT_FORMATIONS')]

class FormationsController extends AbstractController
{
    #[Route('/', name: 'app_formations_index', methods: ['GET'])]
    public function index(FormationsRepository $formationsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search', '');
        $perPage = $request->query->get('perPage', 5);
        $sortField = $request->query->get('sortField', 'id');
        $sortDirection = $request->query->get('sortDirection', 'asc');
    
        $queryBuilder = $formationsRepository->createQueryBuilder('formations');
    
        if (!empty($searchTerm)) {
            $queryBuilder->andWhere(' formations.code_formation LIKE :searchTerm OR formations.libelle_formation LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    
        $queryBuilder->orderBy("formations.$sortField", $sortDirection); // Ajout du tri
        $queryBuilder->andWhere("formations.etat_formation = 1");
        $query = $queryBuilder->getQuery();
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $perPage
        );
    
        return $this->render('formations/index.html.twig', [
            'pagination' => $pagination,
            'searchTerm' => $searchTerm,
            'perPage' => $perPage,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[Route('/new', name: 'app_formations_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formations();
        $form = $this->createForm(FormationsType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_formations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formations/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formations_show', methods: ['GET'])]
    public function show(Formations $formation): Response
    {
        return $this->render('formations/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formations_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formations $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationsType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formations/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formations_delete', methods: ['POST'])]
    public function delete(Request $request, Formations $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formations_index', [], Response::HTTP_SEE_OTHER);
    }
}
