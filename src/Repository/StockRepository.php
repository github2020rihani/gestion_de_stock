<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function findArticleInStockById($id_article) {
        return $this->createQueryBuilder('s')
            ->where('s.article = :id_article')
            ->setParameter('id_article', $id_article)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findArticleJoinPrix($id_article) {
        return $this->createQueryBuilder('s')
            ->select('s', 'a', 'p')
            ->leftJoin('s.article', 'a')
            ->leftJoin('a.prixes', 'p')
            ->where('s.article = :id_article')
            ->setParameter('id_article', $id_article)
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return stock[] Returns an array of stock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?stock
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
