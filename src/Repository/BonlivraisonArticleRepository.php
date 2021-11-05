<?php

namespace App\Repository;

use App\Entity\BonlivraisonArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BonlivraisonArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method BonlivraisonArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method BonlivraisonArticle[]    findAll()
 * @method BonlivraisonArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BonlivraisonArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BonlivraisonArticle::class);
    }

    // /**
    //  * @return BonlivraisonArticle[] Returns an array of BonlivraisonArticle objects
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
    public function findOneBySomeField($value): ?BonlivraisonArticle
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
