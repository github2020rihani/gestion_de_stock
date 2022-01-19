<?php

namespace App\Repository;

use App\Entity\Achat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Achat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achat[]    findAll()
 * @method Achat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achat::class);
    }

    public function findAllAchat()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    public function getLastAchatWithCurrentYear($year) {
        return $this->createQueryBuilder('a')
            ->where('a.year = :year')
            ->setParameter('year', $year)
            ->setMaxResults(1)
            ->orderBy('a.id', 'desc')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    public function getDetailAchat($id)
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'aa', 'article', 'f')
            ->leftJoin('a.fournisseur', 'f')
            ->leftJoin('a.achatArticles', 'aa')
            ->leftJoin('aa.article', 'article')
            ->where('a.id = :id_achat')
            ->setParameter('id_achat', $id)
            ->getQuery()
            ->getArrayResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Achat
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
