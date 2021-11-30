<?php

namespace App\Repository;

use App\Entity\Fournisseur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fournisseur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fournisseur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fournisseur[]    findAll()
 * @method Fournisseur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FournisseurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fournisseur::class);
    }


    public function findByCodeAndEmail($email ) {
        return $this->createQueryBuilder('f')
            ->where('f.email = :email')
            ->setParameter('email', $email)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function getLastFournisseur() {
        return $this->createQueryBuilder('f')
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
            ;

    }





    public function findByCode($code) {
        return $this->createQueryBuilder('f')
            ->where('f.code = :code')
            ->setParameter('code', $code)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByEmail($email) {
        return $this->createQueryBuilder('f')
            ->where('f.email = :email')
            ->setParameter('email', $email)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return Fournisseur[] Returns an array of Fournisseur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fournisseur
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
