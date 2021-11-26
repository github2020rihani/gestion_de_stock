<?php

namespace App\Repository;

use App\Entity\AchatArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AchatArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method AchatArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method AchatArticle[]    findAll()
 * @method AchatArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AchatArticle::class);
    }

    public function findAchatArticleByIdAricleAndIdAchat($id_article, $id_achat)
    {
        return $this->createQueryBuilder('a')
            ->where('a.article = :id_article')
            ->andWhere('a.Achat = :id_achat')
            ->setParameter('id_article', $id_article)
            ->setParameter('id_achat', $id_achat)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findArticleLastAchat($type_prix) {
        return $this->createQueryBuilder('a')
            ->select('a', 'art')
            ->leftJoin('a.article', 'art')
            ->where('a.typePrix =  :typePrix')
            ->setParameter('typePrix', $type_prix)
            ->getQuery()
            ->getResult()
            ;
    }    public function findArticleWithNewPrix($id_article ,$type_prix) {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.article =  :id_article')
            ->andWhere('a.typePrix =  :typePrix')
            ->setParameter('id_article', $id_article)
            ->setParameter('typePrix', $type_prix)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }


    // /**
    //  * @return AchatArticle[] Returns an array of AchatArticle objects
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
    public function findOneBySomeField($value): ?AchatArticle
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
