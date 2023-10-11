<?php

namespace App\Repository;

use App\Entity\DroitsDAcces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DroitsDAcces>
 *
 * @method DroitsDAcces|null find($id, $lockMode = null, $lockVersion = null)
 * @method DroitsDAcces|null findOneBy(array $criteria, array $orderBy = null)
 * @method DroitsDAcces[]    findAll()
 * @method DroitsDAcces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DroitsDAccesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DroitsDAcces::class);
    }

    public function save(DroitsDAcces $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DroitsDAcces $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DroitsDAcces[] Returns an array of DroitsDAcces objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DroitsDAcces
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
