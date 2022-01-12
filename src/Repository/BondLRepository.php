<?php

namespace App\Repository;

use App\Entity\BondL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BondL|null find($id, $lockMode = null, $lockVersion = null)
 * @method BondL|null findOneBy(array $criteria, array $orderBy = null)
 * @method BondL[]    findAll()
 * @method BondL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BondLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BondL::class);
    }

    // /**
    //  * @return BondL[] Returns an array of BondL objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BondL
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
