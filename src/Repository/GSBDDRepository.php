<?php

namespace App\Repository;

use App\Entity\GSBDD;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GSBDD>
 *
 * @method GSBDD|null find($id, $lockMode = null, $lockVersion = null)
 * @method GSBDD|null findOneBy(array $criteria, array $orderBy = null)
 * @method GSBDD[]    findAll()
 * @method GSBDD[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GSBDDRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GSBDD::class);
    }

//    /**
//     * @return GSBDD[] Returns an array of GSBDD objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GSBDD
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
