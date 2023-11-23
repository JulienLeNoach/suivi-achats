<?php

namespace App\Repository;

use App\Entity\CPV;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<CPV>
 *
 * @method CPV|null find($id, $lockMode = null, $lockVersion = null)
 * @method CPV|null findOneBy(array $criteria, array $orderBy = null)
 * @method CPV[]    findAll()
 * @method CPV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CPVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CPV::class);
    }
    public function showCPV($form,$page)
    {
        // $data = $form->getData();
        $date = $form["date"]->getData();
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT 
        cpv.libelle_cpv,
        SUM(achat.montant_achat) AS somme_montants,
        cpv.mt_cpv,
        cpv.mt_cpv_auto,
        (cpv.mt_cpv_auto - SUM(achat.montant_achat)) AS reliquat
    FROM 
        achat
    JOIN 
        cpv ON achat.code_cpv_id = cpv.id
    WHERE 
        YEAR(achat.date_saisie) = $date
    GROUP BY 
        cpv.libelle_cpv, cpv.mt_cpv, cpv.mt_cpv_auto 
    ORDER BY 
    somme_montants DESC";

        $stmt = $conn->prepare($sql);
        $resultSet = $conn->executeQuery($sql);
        $result = $resultSet->fetchAllAssociative();
    

    
        return $result;


      

    }

}
