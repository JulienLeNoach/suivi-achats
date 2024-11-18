<?php

namespace App\Service;

use App\Entity\CPV;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImportCPV extends AbstractController
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
            try {
                // Vérifier que le fichier est bien un Excel
                $this->validateFileType($file);

                // Lire le fichier Excel
                $spreadsheet = IOFactory::load($file);
                $worksheet = $spreadsheet->getSheet(0);

                // Valider la structure du fichier (A6 = "Code CPV", B6 = "Libellé CPV")
                $this->validateExcelStructure($worksheet);

                $this->processRows($worksheet,$user->getCodeService());

                $this->addFlash('success', 'Importation réussie !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Le fichier demandé n\'est pas le bon.');
            }

            return $this->redirectToRoute('cpv');
        }

        $this->addFlash('error', 'Veuillez sélectionner un fichier Excel à importer.');
        return $this->redirectToRoute('cpv');
    }

    /**
     * Vérifie que le fichier est bien un Excel.
     *
     * @param mixed $file
     * @throws \Exception
     */
    private function validateFileType($file)
    {
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ])) {
            throw new \Exception('Type de fichier invalide');
        }
    }

    /**
     * Vérifie la structure du fichier Excel (A1 = "Code CPV", B1 = "Libellé CPV").
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @throws \Exception
     */
    private function validateExcelStructure($worksheet)
    {
        $cellA6 = trim((string) $worksheet->getCell('A1')->getValue());
        $cellB6 = trim((string) $worksheet->getCell('B1')->getValue());

        if ($cellA6 !== 'Code CPV' || $cellB6 !== 'Libellé CPV') {
            throw new \Exception('Structure du fichier invalide');
        }
    }

    /**
     * Traite les lignes du fichier Excel.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @param string $service
     */
    private function processRows($worksheet, string $service)
    {
        $rowIndex = 7;
        $highestRow = $worksheet->getHighestRow();
        $user = $this->security->getUser();

        for (; $rowIndex <= $highestRow; $rowIndex++) {
            $rowData = $worksheet->rangeToArray('A' . $rowIndex . ':B' . $rowIndex, null, true, false)[0];

            if (!empty($rowData[0])) {
                $codeCpv = $rowData[0];
                $existingCpv = $this->entityManager->getRepository(CPV::class)->findOneByCodeCpv($codeCpv);

                if (!$existingCpv) {
                    $entity = new CPV();
                    $entity->setCodeCpv($codeCpv);
                    $entity->setLibelleCpv($rowData[1]);
                    $entity->setEtatCpv(1);
                    $entity->setCodeService($user->getCodeService());
                    $entity->setMtCpvAuto(40000);
                    $entity->setPremierSeuil(30000);

                    $this->entityManager->persist($entity);
                } else {
                    $existingCpv->setMtCpvAuto(40000);
                }
            }
        }

        $this->entityManager->flush();
    }
}
