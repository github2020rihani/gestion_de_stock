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
