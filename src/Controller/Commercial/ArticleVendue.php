<?php


namespace App\Controller\Commercial;


use App\Repository\ArticlesVendueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleVendue
 * @package App\Controller\Commercial
 * @Route("/personelle/articles/vendues")
 */
class ArticleVendue extends AbstractController
{

    private $em;
    private $articlesVendueRepository;

    public function __construct(EntityManagerInterface $em, ArticlesVendueRepository $articlesVendueRepository)
    {
        $this->em = $em;
        $this->articlesVendueRepository = $articlesVendueRepository;

    }

    /**
     * @Route("/", name="perso_articles_Vendues")
     */
    public function indexArticleVendue()
    {
        $articlesVendues = $this->articlesVendueRepository->findAll();
        dd($articlesVendues);

    }

}