<?php


namespace App\Controller\superAdmin;


use App\Entity\Achat;
use App\Repository\ArticleRepository;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super_admin/achat")
 */
class AchatController extends AbstractController
{

    private $em ;
    private $fournisseurRepository ;
    private $articleRepository ;
    public function __construct(EntityManagerInterface $em,
FournisseurRepository $fournisseurRepository, ArticleRepository $articleRepository)
    {
        $this->em = $em;
        $this->fournisseurRepository = $fournisseurRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param Request $request
     * @Route("/add", name="add_achat")
     */
    public function new(Request $request) {
        $achat = new Achat();
        $fournisseurs = $this->fournisseurRepository->findAll();
        $articles = $this->articleRepository->findAll();


        return $this->render('superAdmin/Achat/new.html.twig',[
            'article' => '',
            'fournisseurs' => $fournisseurs,
            'articles' => $articles,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/get/articles", name="get_articles", options={"expose" = true})
     */
    public function getArticles() {
        $articles = $this->articleRepository->findArticles();
        return $this->json($articles);

    }   /**
     * @param Request $request
     * @Route("/get/articlesById", name="get_articles_byId", options={"expose" = true})
     */
    public function getArticleById(Request $request) {
        $article = $this->articleRepository->getArticleById($request->request->get('id_article'));
        return $this->json($article);

    }
}