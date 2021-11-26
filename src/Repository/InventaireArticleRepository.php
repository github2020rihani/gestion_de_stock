<?php

namespace App\Repository;

use App\Entity\InventaireArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InventaireArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method InventaireArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method InventaireArticle[]    findAll()
 * @method InventaireArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InventaireArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventaireArticle::class);
    }


    public function findInvByNum($num, $id_article) {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.inventaire', 'inv')
            ->where('inv.numero = :num')
            ->andWhere('i.article = :id_art')
            ->setParameter('num', $num)
            ->setParameter('id_art', $id_article)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getDataInventaire($id_inv) {
        return $this->createQueryBuilder('i')
            ->select('i', 'article')
            ->leftJoin('i.article', 'article')
            ->where('i.inventaire = :id_inv')
            ->setParameter('id_inv', $id_inv)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    // /**
    //  * @return InventaireArticle[] Returns an array of InventaireArticle objects
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
    public function findOneBySomeField($value): ?InventaireArticle
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
