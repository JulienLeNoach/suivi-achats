<?php

namespace App\Repository;

use App\Entity\JustifAchat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JustifAchat>
 *
 * @method JustifAchat|null find($id, $lockMode = null, $lockVersion = null)
 * @method JustifAchat|null findOneBy(array $criteria, array $orderBy = null)
 * @method JustifAchat[]    findAll()
 * @method JustifAchat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JustifAchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JustifAchat::class);
    }

//    /**
//     * @return JustifAchat[] Returns an array of JustifAchat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JustifAchat
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
