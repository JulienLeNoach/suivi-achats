<?php

namespace App\Repository;

use App\Entity\CPV;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<CPV>
 *
 * @method CPV|null find($id, $lockMode = null, $lockVersion = null)
 * @method CPV|null findOneBy(int $criteria, array $orderBy = null)
 * @method CPV[]    findAll()
 * @method CPV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CPVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CPV::class);
    }

    public function findOneByCodeCpv($id): ?CPV
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function showCPV($form)
    {
        $date = $form["date"];
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('cpv.libelle_cpv')
            ->addSelect('SUM(achat.montant_achat) AS somme_montants')
            ->addSelect('cpv.mt_cpv_auto')
            ->addSelect('(cpv.mt_cpv_auto - SUM(achat.montant_achat)) AS reliquat')
            ->from('App\Entity\Achat', 'achat')
            ->join('achat.code_cpv', 'cpv')
            ->where("YEAR(achat.date_saisie) = :date")
            ->setParameter('date',$date)
            ->andWhere("cpv.etat_cpv = :etat")
            ->setParameter('etat', 1)
            ->groupBy('cpv.libelle_cpv, cpv.mt_cpv_auto')
            ->orderBy('somme_montants', 'DESC');
        $query = $queryBuilder->getQuery();
        return $query;
    }
    
    public function showCPVwithId($id)
    {
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('cpv.libelle_cpv')
            ->addSelect('SUM(achat.montant_achat) AS somme_montants')
            ->addSelect('cpv.mt_cpv_auto')
            ->addSelect('(40000 - SUM(achat.montant_achat)) AS reliquat')
            ->from('App\Entity\Achat', 'achat')
            ->join('achat.code_cpv', 'cpv')
            ->where("YEAR(achat.date_saisie) = :date")
            ->setParameter('date',2024)
            ->andWhere("cpv.etat_cpv = :etat")
            ->setParameter('etat', 1)
            ->andWhere("cpv.code_cpv = :id")
            ->setParameter('id', $id)
            
            ->groupBy('cpv.libelle_cpv, cpv.mt_cpv_auto')
            ->orderBy('somme_montants', 'DESC');
        $query = $queryBuilder->getQuery()->getSingleResult();
        return $query;
    }
    
    public function getTotalMontantCPV($cpvId, $id)
{
    $entityManager = $this->getEntityManager();
    
    // Extraire l'année de la date_saisie pour le cpvId spécifique
    $yearQuery = $entityManager->createQueryBuilder()
        ->select('YEAR(achat.date_saisie) AS year')
        ->from('App\Entity\Achat', 'achat')
        ->where('achat.code_cpv = :cpvId')
        ->andWhere('achat.id = :achatId')
        ->setParameter('cpvId', $cpvId)
        ->setParameter('achatId', $id)
        ->setMaxResults(1)
        ->getQuery();
        
    // Obtenir l'année de la date_saisie
    $year = $yearQuery->getSingleScalarResult();

    // Construire la requête principale avec l'année extraite
    $queryBuilder = $entityManager->createQueryBuilder();
    $queryBuilder
        ->select(
            'SUM(achat.montant_achat) AS computation', 
            'cpv.mt_cpv_auto AS mt_cpv', 
            '(cpv.mt_cpv_auto) AS reliquat'
        )
        ->from('App\Entity\Achat', 'achat')
        ->join('achat.code_cpv', 'cpv')
        ->where('cpv.id = :cpvId')
        ->andWhere('achat.etat_achat IN (:statuses)')
        ->setParameter('statuses', [0, 2])
        ->andWhere('YEAR(achat.date_saisie) = :year')
        ->setParameter('cpvId', $cpvId)
        ->setParameter('year', $year);

    // Déboguer le résultat
            // dd($queryBuilder->getQuery()->getSingleResult());
    return $queryBuilder->getQuery()->getSingleResult();
}

    
    public function getTotalMontantCPVwithoutId($cpvId)
    {
        $entityManager = $this->getEntityManager();
        
        // Extraire l'année de la date_saisie pour le cpvId spécifique
        $yearQuery = $entityManager->createQueryBuilder()
            ->select('YEAR(achat.date_saisie) AS year')
            ->from('App\Entity\Achat', 'achat')
            ->where('achat.code_cpv = :cpvId')
            ->setParameter('cpvId', $cpvId)
            ->setMaxResults(1)
            ->getQuery();
        dd($yearQuery->getSingleScalarResult());
        // Obtenir l'année de la date_saisie
        $year = $yearQuery->getSingleScalarResult();
        // Construire la requête principale avec l'année extraite
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('SUM(achat.montant_achat) AS computation, cpv.mt_cpv_auto AS mt_cpv, cpv.mt_cpv_auto - SUM(achat.montant_achat) AS reliquat')
            ->from('App\Entity\Achat', 'achat')
            ->join('achat.code_cpv', 'cpv')
            ->where('cpv.id = :cpvId')
            ->andWhere('YEAR(achat.date_saisie) = :year')
            ->setParameter('cpvId', $cpvId)
            ->setParameter('year', $year);
    
            dd($queryBuilder->getQuery()->getSingleResult());
            return $queryBuilder->getQuery()->getSingleResult();
    }
    public function edit(CPV $cpv, bool $flush = false): void
    {
        $this->getEntityManager()->persist($cpv);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
