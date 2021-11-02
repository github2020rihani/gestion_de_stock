<?php

namespace App\Repository;

use App\Entity\Devis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }

    public function getLastDevis() {
        return $this->createQueryBuilder('d')
            ->orderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    public function findDetailDevi($idDevis) {
        return $this->createQueryBuilder('d')
            ->select('d', 'devisArticle', 'article', 'client', 'city', 'country')
            ->leftJoin('d.devisArticles', 'devisArticle')
            ->leftJoin('devisArticle.article', 'article')
            ->leftJoin('d.client', 'client')
            ->leftJoin('client.city', 'city')
            ->leftJoin('client.country', 'country')
            ->where('d.id = :id_devi')
            ->setParameter('id_devi' , $idDevis)
            ->getQuery()
            ->getArrayResult()
            ;
    }
    public function findDetailDeviAndStock($idDevis) {
        return $this->createQueryBuilder('d')
            ->select('d', 'devisArticle', 'article', 'client', 'city', 'country', 'prix')
            ->leftJoin('d.devisArticles', 'devisArticle')
            ->leftJoin('devisArticle.article', 'article')
            ->leftJoin('article.prixes', 'prix')
            ->leftJoin('d.client', 'client')
            ->leftJoin('client.city', 'city')
            ->leftJoin('client.country', 'country')
            ->where('d.id = :id_devi')
            ->setParameter('id_devi' , $idDevis)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    // /**
    //  * @return Devis[] Returns an array of Devis objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Devis
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
