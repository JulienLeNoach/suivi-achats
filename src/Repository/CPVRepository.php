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
    public function showCPV($form)
    {
        $date = $form["date"];
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('cpv.libelle_cpv')
            ->addSelect('SUM(achat.montant_achat) AS somme_montants')
            ->addSelect('cpv.mt_cpv')
            ->addSelect('cpv.mt_cpv_auto')
            ->addSelect('(cpv.mt_cpv_auto - SUM(achat.montant_achat)) AS reliquat')
            ->from('App\Entity\Achat', 'achat')

            ->join('achat.code_cpv', 'cpv')
            ->where("YEAR(achat.date_saisie) = :date")
            ->setParameter('date',$date)
            ->andWhere("cpv.etat_cpv = :etat")
            ->setParameter('etat', 1)
            ->groupBy('cpv.libelle_cpv, cpv.mt_cpv, cpv.mt_cpv_auto')
            ->orderBy('somme_montants', 'DESC');
    
        $query = $queryBuilder->getQuery();
        $result = $query->getResult();
    
        return $query;
    }

}
