<?php

namespace App\Service;

use App\Entity\Fournisseurs;
use Doctrine\ORM\EntityManagerInterface;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImportFournisseurs extends AbstractController
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
            $filePath = $file->getPathname();
            $extension = $file->getClientOriginalExtension();

            // Créer le lecteur approprié en fonction de l'extension
            switch (strtolower($extension)) {
                case 'xlsx':
                    $reader = ReaderEntityFactory::createXLSXReader();
                    break;
                
                case 'csv':
                    $reader = ReaderEntityFactory::createCSVReader();
                    break;
                default:
                    $this->addFlash('error', 'Type de fichier non supporté : ' . $extension);
                    return $this->redirectToRoute('fournisseurs');
            }

            $reader->open($filePath);

            $batchSize = 20;
            $entities = [];
            $rowIndex = 0;

            foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                // Traitement de la première feuille seulement
                if ($sheetIndex == 1) {
                    foreach ($sheet->getRowIterator() as $row) {
                        $rowIndex++;
                        if ($rowIndex < 4) continue; // Commencer à partir de la quatrième ligne

                        $rowData = $row->toArray();

                        if (!empty($rowData[0])) {
                            $numSiret = $rowData[0];
                            dd($$numSiret);

                            $existingNumSiret = $this->entityManager->getRepository(Fournisseurs::class)->findOneByNumSiret($numSiret);
                            if (!$existingNumSiret) {
                                $entity = new Fournisseurs();
                                $entity->setCodeService($service);
                                $entity->setNumSiret($numSiret);
                                $entity->setNumChorusFournisseur($rowData[1]);
                                $entity->setNomFournisseur($rowData[2]); // Assurez-vous de capturer le bon champ
                                $entity->setPme($rowData[3]); // Assurez-vous de capturer le bon champ
                                $entity->setEtatFournisseur(1);

                                $this->entityManager->persist($entity);
                                $entities[] = $entity;
                            }

                            if (count($entities) >= $batchSize) {
                                $this->entityManager->flush();
                                $this->entityManager->clear();
                                $entities = [];
                            }
                        }
                    }
                }
            }

            if (!empty($entities)) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }

            $reader->close();

            $this->addFlash('success', 'Importation réussie !');
            return $this->redirectToRoute('fournisseurs');
        }

        $this->addFlash('error', 'Veuillez sélectionner un fichier Excel à importer.');
        return $this->redirectToRoute('fournisseurs');
    }
}