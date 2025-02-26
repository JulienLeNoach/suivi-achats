<?php

namespace App\Controller;

use App\Entity\GSBDD;
use App\Form\GSBDDType;
use App\Repository\GSBDDRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/g/s/b/d/d')]
class GSBDDController extends AbstractController
{
    #[Route('/', name: 'app_g_s_b_d_d_index', methods: ['GET'])]
    public function index(GSBDDRepository $gSBDDRepository): Response
    {
        return $this->render('gsbdd/index.html.twig', [
            'g_s_b_d_ds' => $gSBDDRepository->findAll(),
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
