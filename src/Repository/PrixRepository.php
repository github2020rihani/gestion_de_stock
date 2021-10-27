<?php

namespace App\Repository;

use App\Entity\Prix;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Prix|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prix|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prix[]    findAll()
 * @method Prix[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrixRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prix::class);
    }


    public function findByIdArticle($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.article = :id_article')
            ->setParameter('id_article', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getPrixArticlesWithMot($value)
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'article')
            ->leftJoin('p.article', 'article')
            ->where('article.ref LIKE :ref')
            ->setParameter('ref', '%'.$value.'%')
            ->getQuery()
            ->getResult()
            ;
    }

}
