<?php


namespace App\Controller\Commercial;


use App\Repository\ArticleRepository;
use App\Repository\DevisArticleRepository;
use App\Repository\PrixRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route ;

class globalController extends AbstractController
{


    private $em;
    private $articleRepository;
    private $prixRepository;
    private $paginator;
    private $stockRepository;
    private $devisArticleRepository;

    /**
     * dashboardController constructor.
     * @param EntityManagerInterface $em
     * @param ArticleRepository $articleRepository
     * @param PaginatorInterface $paginator
     * @param StockRepository $stockRepository
     * @param PrixRepository $prixRepository
     */
    public function __construct(EntityManagerInterface $em,
                                ArticleRepository $articleRepository,
                                PaginatorInterface $paginator,
                                DevisArticleRepository $devisArticleRepository,
                                StockRepository $stockRepository,
                                PrixRepository $prixRepository)
    {
        $this->em = $em;
        $this->articleRepository = $articleRepository;
        $this->prixRepository = $prixRepository;
        $this->paginator = $paginator;
        $this->stockRepository = $stockRepository;
        $this->devisArticleRepository = $devisArticleRepository;

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("personelle/api/articles_fom_prix", name="api_get_articles_from_prix", options={"expose" = true})
     */
    public function getArticlesAndPrix() {
        $articles = $this->prixRepository->getArticleWithPrix();
        if ($articles) {
            $message = 'yes';
            $success = true ;
            $data = $articles ;
        }else{
            $data = '';
            $message = 'no';
            $success = false ;
        }

        return $this->json($data);

    }    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("personelle/api/get_article", name="perso_get_articles_byId", options={"expose" = true})
     */
        public
        function getArticleById(Request $request)
        {
            $article = $this->prixRepository->getArticleById($request->request->get('id_article'));
            return $this->json($article);

        }

    /**
     * @param Request $request
     * @Route("personelle/api/get_articles_devis", name="perso_get_articles_devis", options={"expose" = true})
     */
        public function getArticles(Request $request) {
            $id_devis = $request->get('id_devis');
            $articles_devi = $this->devisArticleRepository->findBy(array('devi' => $id_devis));

            $articles = [];
            foreach ($articles_devi as $key => $value) {
                $articles[] = $value->getArticle()->getId();
            }
            return $this->json($articles);

        }



}