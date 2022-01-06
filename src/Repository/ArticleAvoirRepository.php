<?php

namespace App\Repository;

use App\Entity\ArticleAvoir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleAvoir|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleAvoir|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleAvoir[]    findAll()
 * @method ArticleAvoir[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleAvoirRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleAvoir::class);
    }

    // /**
    //  * @return ArticleAvoir[] Returns an array of ArticleAvoir objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArticleAvoir
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
