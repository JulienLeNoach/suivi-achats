<?php

namespace App\Service;

use App\Entity\CPV;
use App\Repository\AchatRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use Symfony\Component\HttpKernel\KernelInterface;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImportCPV  extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {

        $this->entityManager = $entityManager;
        $this->security = $security;
    }


    public function importDataFromExcel(Request $request, $file)
    {
        $user = $this->security->getUser();
        $service = $user->getCodeService();
        if ($file) {
            // Lire le fichier Excel
            $spreadsheet = IOFactory::load($file);

            // Récupérer la première feuille du classeur
            $worksheet = $spreadsheet->getActiveSheet();
            $rowIndex = 3; // Commencer à partir de la troisième ligne
            $highestRow = $worksheet->getHighestRow();
            // Récupérer les données et les enregistrer en base de données
            for ($rowIndex; $rowIndex <= $highestRow; $rowIndex++) {
                $rowData = $worksheet->rangeToArray('B' . $rowIndex . ':' . 'Z' . $rowIndex, NULL, TRUE, FALSE)[0];



                $entity = new CPV();
                $entity->setCodeCpv($rowData[0]);
                $entity->setLibelleCpv($rowData[1]);
                $entity->setEtatCpv($rowData[2]);
                $entity->setCodeService($service);
                $entity->setMtCpvAuto($rowData[4]);

                // Enregistrement d'autres propriétés ...
                $this->entityManager->persist($entity);
            }
            $this->entityManager->flush();

            $this->addFlash('success', 'Importation réussie !');
            return $this->redirectToRoute('cpv');
        }
        // Gérer le cas où aucun fichier n'est soumis
        $this->addFlash('error', 'Veuillez sélectionner un fichier Excel à importer.');
        return $this->redirectToRoute('route_name');
    }
}