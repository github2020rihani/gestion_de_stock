<?php

namespace App\Repository;

use App\Entity\InvoiceArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InvoiceArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceArticle[]    findAll()
 * @method InvoiceArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceArticle::class);
    }
    public function getArticleInvoiceByIdArticle($id_article) {
        return $this->createQueryBuilder('ia')
            ->where('ia.article = :id_article')
            ->setParameter('id_article', $id_article)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return InvoiceArticle[] Returns an array of InvoiceArticle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InvoiceArticle
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
