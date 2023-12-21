<?php

namespace App\Repository;

use App\Entity\AchatCounter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AchatCounter>
 *
 * @method AchatCounter|null find($id, $lockMode = null, $lockVersion = null)
 * @method AchatCounter|null findOneBy(array $criteria, array $orderBy = null)
 * @method AchatCounter[]    findAll()
 * @method AchatCounter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatCounterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AchatCounter::class);
    }

//    /**
//     * @return AchatCounter[] Returns an array of AchatCounter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

   public function findOneByYear($value): ?AchatCounter
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.year = :year')
           ->setParameter('year', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
}
