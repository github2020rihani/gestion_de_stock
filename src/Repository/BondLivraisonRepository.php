<?php

namespace App\Repository;

use App\Entity\BondLivraison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BondLivraison|null find($id, $lockMode = null, $lockVersion = null)
 * @method BondLivraison|null findOneBy(array $criteria, array $orderBy = null)
 * @method BondLivraison[]    findAll()
 * @method BondLivraison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BondLivraisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BondLivraison::class);
    }

    public function findLastBl() {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getLastBlWithCurrentYear($year) {
        return $this->createQueryBuilder('b')
            ->where('b.year = :year')
            ->setParameter('year', $year)
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return BondLivraison[] Returns an array of BondLivraison objects
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
    public function findOneBySomeField($value): ?BondLivraison
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
