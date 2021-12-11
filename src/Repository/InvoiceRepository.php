<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function getLastInvoiceWithCurrentYear($year)
    {
        return $this->createQueryBuilder('i')
            ->where('i.year = :year')
            ->setParameter('year', $year)
            ->orderBy('i.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findInvoiceByIdBl($idBl) {
        return $this->createQueryBuilder('i')
            ->where('i.bonLivraison = :id_bl')
            ->setParameter('id_bl', $idBl)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getDataInvoice($id) {
        return $this->createQueryBuilder('i')
            ->select('i', 'bl','bla', 'c', 'ci', 'co', 'a', 'devi', 'user')
            ->leftJoin('i.bonLivraison', 'bl')
            ->leftJoin('i.creadetBy', 'user')
            ->leftJoin('bl.devi', 'devi')
            ->leftJoin('bl.bonlivraisonArticles', 'bla')
            ->leftJoin('bla.article', 'a')
            ->leftJoin('bl.customer', 'c')
            ->leftJoin('c.city', 'ci')
            ->leftJoin('c.country', 'co')
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult()
            ;
    }
    public function getDataInvoiceSeul($id) {
        return $this->createQueryBuilder('i')
            ->select('i', 'ia', 'c', 'ci', 'co', 'a' ,'user')
            ->leftJoin('i.invoiceArticles', 'ia')
            ->leftJoin('i.creadetBy', 'user')

            ->leftJoin('ia.article', 'a')
            ->leftJoin('i.customer', 'c')
            ->leftJoin('c.city', 'ci')
            ->leftJoin('c.country', 'co')
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult()
            ;
    }




    // /**
    //  * @return Invoice[] Returns an array of Invoice objects
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
    public function findOneBySomeField($value): ?Invoice
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
