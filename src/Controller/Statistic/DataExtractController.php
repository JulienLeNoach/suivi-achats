<?php

namespace App\Controller\Statistic;

use App\Entity\Achat;
use App\Form\DataExtractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Service\Statistic\CreateExcelDataExtract;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DataExtractController extends AbstractController
{
    private $entityManager;
    private $createExcelDataExtract;

    public function __construct(EntityManagerInterface $entityManager, CreateExcelDataExtract $createExcelDataExtract)
    {
        $this->entityManager = $entityManager;
        $this->createExcelDataExtract = $createExcelDataExtract;
    }

    #[Route('/dataextract', name: 'data_extract')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(DataExtractType::class);
        $form->handleRequest($request);
        $achats = [];
        $errorMessage = null;

        if ($form->isSubmitted()) {
            $achats = $this->entityManager->getRepository(Achat::class)->extractSearchAchat($form)->getResult();
            $selectedYear = $form->get('date')->getData();

            if (empty($achats)) {
                $errorMessage = 'Aucun résultat pour cette recherche.';
                return $this->render('data_extract/index.html.twig', [
                    'form' => $form->createView(),
                    'achats' => $achats,
                    'errorMessage' => $errorMessage,
                ]);
            }

            return $this->exportExcel($achats, $selectedYear);
        }

        return $this->render('data_extract/index.html.twig', [
            'form' => $form->createView(),
            'achats' => $achats,
            'errorMessage' => $errorMessage,
        ]);
    }

    private function exportExcel(array $achats, string $selectedYear): BinaryFileResponse
    {
        $filePath = $this->createExcelDataExtract->createExcelFile($achats, $selectedYear);
        return new BinaryFileResponse($filePath);
    }
}