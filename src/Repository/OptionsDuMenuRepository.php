<?php

namespace App\Repository;

use App\Entity\OptionsDuMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OptionsDuMenu>
 *
 * @method OptionsDuMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method OptionsDuMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method OptionsDuMenu[]    findAll()
 * @method OptionsDuMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OptionsDuMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OptionsDuMenu::class);
    }

    public function save(OptionsDuMenu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OptionsDuMenu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return OptionsDuMenu[] Returns an array of OptionsDuMenu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OptionsDuMenu
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
