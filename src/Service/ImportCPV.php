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
            $worksheet = $spreadsheet->getSheet(1);
            $rowIndex = 7; // Commencer à partir de la troisième ligne
            $highestRow = $worksheet->getHighestRow();
            // Récupérer les données et les enregistrer en base de données
            for ($rowIndex; $rowIndex <= $highestRow; $rowIndex++) {
                $rowData = $worksheet->rangeToArray('A' . $rowIndex . ':' . 'Z' . $rowIndex, NULL, TRUE, FALSE)[0];
                
                // Vérifier si la première colonne contient des caractères
                if (!empty($rowData[0])) {
                    $codeCpv = $rowData[0];
                    
                    // Vérifier si le code_cpv existe déjà en base de données
                    $existingCpv = $this->entityManager->getRepository(CPV::class)->findOneByCodeCpv($codeCpv);
                    
                    if (!$existingCpv) {
                        $entity = new CPV();
                        $entity->setCodeCpv($codeCpv);
                        $entity->setLibelleCpv($rowData[1]);
                        $entity->setEtatCpv(1);
                        $entity->setCodeService($service);
                        $entity->setMtCpvAuto(90000);
    
                        // Enregistrement d'autres propriétés ...
                        $this->entityManager->persist($entity);
                    } else {
                        $existingCpv->setMtCpvAuto(90000);
                        continue;
                    }
                } else {
                    // Passez à la prochaine ligne si la première colonne est vide
                    continue;
                }
            }
            $this->entityManager->flush();
    
            $this->addFlash('success', 'Importation réussie !');
            return $this->redirectToRoute('cpv');
        }
        // Gérer le cas où aucun fichier n'est soumis
        $this->addFlash('error', 'Veuillez sélectionner un fichier Excel à importer.');
        return $this->redirectToRoute('cpv');
    }
}
