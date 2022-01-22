<?php

namespace App\Repository;

use App\Entity\Payemet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Payemet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payemet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payemet[]    findAll()
 * @method Payemet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayemetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payemet::class);
    }


    public function findPayementByIdInvoice($id_invoice) {
        return $this->createQueryBuilder('p')
            ->select('p','i', 'bl','bla', 'c', 'ci', 'co', 'a', 'devi', 'user')
            ->leftJoin('p.invoice', 'i')
            ->leftJoin('i.bonLivraison', 'bl')
            ->leftJoin('i.creadetBy', 'user')
            ->leftJoin('bl.devi', 'devi')
            ->leftJoin('bl.bonlivraisonArticles', 'bla')
            ->leftJoin('bla.article', 'a')
            ->leftJoin('bl.customer', 'c')
            ->leftJoin('c.city', 'ci')
            ->leftJoin('c.country', 'co')
            ->andWhere('p.invoice = :val')
            ->setParameter('val', $id_invoice)
            ->getQuery()
            ->getArrayResult()[0]
            ;
    }


    public function getAllByDate($date) {
        return $this->createQueryBuilder('p')
            ->where('p.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Payemet[] Returns an array of Payemet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Payemet
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
