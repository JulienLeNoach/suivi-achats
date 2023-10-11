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


}
