<?php

namespace App\Controller\Environnement;

use App\Entity\CPV;
use App\Form\CPVType;
use App\Service\ImportCPV;
use App\Form\ImportExcelType;
use App\Repository\CPVRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/cpv')]
#[IsGranted('ROLE_OPT_CPV')]

class CPVController extends AbstractController
{


    private $entityManager;
    private $importCPV;

    public function __construct(EntityManagerInterface $entityManager,ImportCPV $importCPV)
    {

        $this->entityManager = $entityManager;
        $this->importCPV = $importCPV;

    }
    #[Route('/update_all_cpv', name: 'update_all_cpv', methods: ['POST'])]
    public function updateAllCpv(Request $request, CPVRepository $cpvRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données envoyées par la requête
        $data = json_decode($request->getContent(), true);
        $newAmount = $data['amount'];
    
        // Mettre à jour tous les CPV avec le montant donné
        $cpvs = $cpvRepository->findAll();
        foreach ($cpvs as $cpv) {
            $cpv->setMtCpvAuto($newAmount);
            $entityManager->persist($cpv);
        }
    
        $entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }
    #[Route('/', name: 'cpv', methods: ['GET','POST'])]
    public function index(CPVRepository $cPVRepository, Request $request,Security $security, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search', '');
        $perPage = $request->query->get('perPage', 5);
        $sortField = $request->query->get('sortField', 'id');
        $sortDirection = $request->query->get('sortDirection', 'asc');
        $activeCpv = $request->query->get('activeCpv');

        $queryBuilder = $cPVRepository->createQueryBuilder('cpv');

        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('cpv.code_cpv LIKE :searchTerm OR cpv.libelle_cpv LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
        if ($activeCpv !== null && $activeCpv === 'on') {
            $queryBuilder->andWhere("cpv.etat_cpv = 1");
        }        $queryBuilder->orderBy("cpv.$sortField", $sortDirection); // Ajout du tri
    
        $query = $queryBuilder->getQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $perPage
        );
        $form = $this->createForm(ImportExcelType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('excel_file')->getData();
            $this->importCPV->importDataFromExcel($request,$file);
    }
    return $this->render('cpv/index.html.twig', [
        'pagination' => $pagination,
        'searchTerm' => $searchTerm,
        'perPage' => $perPage,
        'sortField' => $sortField,
        'sortDirection' => $sortDirection,
        'form' => $form->createView(),
        'activeCpv' => $activeCpv
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
