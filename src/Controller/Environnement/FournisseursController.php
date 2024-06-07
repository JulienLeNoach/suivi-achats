<?php

namespace App\Controller\Environnement;

use App\Entity\Fournisseurs;
use App\Form\FournisseursType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FournisseursRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/fournisseurs')]
#[IsGranted('ROLE_OPT_FOURNISSEURS')]

class FournisseursController extends AbstractController
{
    #[Route('/', name: 'app_fournisseurs_index', methods: ['GET'])]
public function index(FournisseursRepository $fournisseursRepository, Request $request, PaginatorInterface $paginator): Response
{
    $searchTerm = $request->query->get('search', '');
    $perPage = $request->query->get('perPage', 5);
    $sortField = $request->query->get('sortField', 'id');
    $sortDirection = $request->query->get('sortDirection', 'asc');
    $activeFournisseur = $request->query->get('activeFournisseur');

    $queryBuilder = $fournisseursRepository->createQueryBuilder('fournisseurs');

    if (!empty($searchTerm)) {
        $queryBuilder->andWhere('fournisseurs.num_siret LIKE :searchTerm OR fournisseurs.nom_fournisseur LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');
    }

    if ($activeFournisseur !== null && $activeFournisseur === 'on') {
        $queryBuilder->andWhere("fournisseurs.etat_fournisseur = 1");
    }

    $queryBuilder->orderBy("fournisseurs.$sortField", $sortDirection); // Ajout du tri

    $query = $queryBuilder->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        $perPage
    );

    return $this->render('fournisseurs/index.html.twig', [
        'pagination' => $pagination,
        'searchTerm' => $searchTerm,
        'perPage' => $perPage,
        'sortField' => $sortField,
        'sortDirection' => $sortDirection,
        'activeFournisseur' => $activeFournisseur, // Passer la valeur de la case à cocher au template
    ]);
}
    #[Route('/new', name: 'app_fournisseurs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fournisseur = new Fournisseurs();
        $form = $this->createForm(FournisseursType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fournisseur);
            $entityManager->flush();

            return $this->redirectToRoute('app_fournisseurs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fournisseurs/new.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fournisseurs_show', methods: ['GET'])]
    public function show(Fournisseurs $fournisseur): Response
    {
        return $this->render('fournisseurs/show.html.twig', [
            'fournisseur' => $fournisseur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fournisseurs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fournisseurs $fournisseur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FournisseursType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fournisseurs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fournisseurs/edit.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fournisseurs_delete', methods: ['POST'])]
    public function delete(Request $request, Fournisseurs $fournisseur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fournisseur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($fournisseur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fournisseurs_index', [], Response::HTTP_SEE_OTHER);
    }
}
