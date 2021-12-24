<?php

namespace App\Repository;

use App\Entity\ArticlesVendue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticlesVendue|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticlesVendue|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticlesVendue[]    findAll()
 * @method ArticlesVendue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesVendueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticlesVendue::class);
    }


    public function getArticlesVendus()
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'bl' , 'invoice', 'artbl', 'c')
            ->leftJoin('a.bl', 'bl')
            ->leftJoin('a.invoice', 'invoice')
            ->leftJoin('invoice.invoiceArticles', 'artinv')
            ->leftJoin('bl.bonlivraisonArticles', 'artbl')
            ->leftJoin('bl.customer', 'c')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return ArticlesVendue[] Returns an array of ArticlesVendue objects
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
    public function findOneBySomeField($value): ?ArticlesVendue
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
