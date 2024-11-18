<?php

namespace App\Controller;

use App\Entity\GSBDD;
use App\Form\GSBDDType;
use App\Repository\GSBDDRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/g/s/b/d/d')]
// #[IsGranted('ROLE_GSB_MANAGER')]
class GSBDDController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_g_s_b_d_d_index', methods: ['GET'])]
    public function index(GSBDDRepository $gSBDDRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search', '');
        $perPage = $request->query->get('perPage', 5);
        $sortField = $request->query->get('sortField', 'id');
        $sortDirection = $request->query->get('sortDirection', 'asc');
        $activeGSBDD = $request->query->get('activeGSBDD');
        $user = $this->security->getUser();

        $queryBuilder = $gSBDDRepository->createQueryBuilder('gsbdd');

        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('gsbdd.libelle_gsbdd LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
        $queryBuilder->andWhere('gsbdd.code_service = '.$user->getCodeService()->getId());

        if ($activeGSBDD !== null && $activeGSBDD === 'on') {
            $queryBuilder->andWhere("gsbdd.etat_gsbdd = 1");
        }

        $queryBuilder->orderBy("gsbdd.$sortField", $sortDirection);

        $query = $queryBuilder->getQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $perPage
        );

        return $this->render('gsbdd/index.html.twig', [
            'pagination' => $pagination,
            'searchTerm' => $searchTerm,
            'perPage' => $perPage,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'activeGSBDD' => $activeGSBDD,
        ]);
    }

    #[Route('/new', name: 'app_g_s_b_d_d_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gSBDD = new GSBDD();
        $form = $this->createForm(GSBDDType::class, $gSBDD);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gSBDD);
            $entityManager->flush();

            return $this->redirectToRoute('app_g_s_b_d_d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gsbdd/new.html.twig', [
            'g_s_b_d_d' => $gSBDD,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_g_s_b_d_d_show', methods: ['GET'])]
    public function show(GSBDD $gSBDD): Response
    {
        return $this->render('gsbdd/show.html.twig', [
            'g_s_b_d_d' => $gSBDD,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_g_s_b_d_d_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GSBDD $gSBDD, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GSBDDType::class, $gSBDD);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_g_s_b_d_d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gsbdd/edit.html.twig', [
            'g_s_b_d_d' => $gSBDD,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_g_s_b_d_d_delete', methods: ['POST'])]
    public function delete(Request $request, GSBDD $gSBDD, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gSBDD->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gSBDD);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_g_s_b_d_d_index', [], Response::HTTP_SEE_OTHER);
    }
}
