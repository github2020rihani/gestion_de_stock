<?php


namespace App\Controller\api;


use App\Repository\ArticleRepository;
use App\Repository\PrixRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class dashboardController
 * @package App\Controller\api
 * @Route("/api/")
 */
class dashboardController extends AbstractController
{

    private $em;
    private $articleRepository;
    private $prixRepository;
    private $paginator;
    private $stockRepository;

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
                                StockRepository $stockRepository,
                                PrixRepository $prixRepository)
    {
        $this->em = $em;
        $this->articleRepository = $articleRepository;
        $this->prixRepository = $prixRepository;
        $this->paginator = $paginator;
        $this->stockRepository = $stockRepository;

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("get/articles", name="api_get_articles", options={"expose" = true})

     */
    public function getArticlesEpuise(Request $request) {
        $mot = $request->get('mot');
        $query = $this->stockRepository->getArticleWhereQteAndMot($mot);
        if ($query) {
            $success = 'true';
            $articles = $this->paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                6 /*limit per page*/
            );
            $message = $this->render('admin/dashboard/searchArticle.html.twig', array('articles' => $articles))->getContent();

        }else{
            $message = 'Aucun article a été trouver';
            $success = 'false';
        }
        return $this->json(array('message'=> $message, 'success' => $success));

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("get/articles_prix", name="api_get_articles_prix", options={"expose" = true})

     */
    public function getPrixArticles(Request $request) {
        $mot = $request->get('mot');
        $queryPrix = $this->prixRepository->getPrixArticlesWithMot($mot);
        if ($queryPrix) {
                $prixs = $this->paginator->paginate(
                    $queryPrix, /* query NOT result */
                    $request->query->getInt('page', 1), /*page number*/
                    6 /*limit per page*/
                );

            $message = $this->render('admin/dashboard/searchPrixArticle.html.twig', array('prixs' => $prixs))->getContent();
            $success = 'true';
        }else{
            $message = 'Aucun article a été trouver';
            $success = 'false';
        }
        return $this->json(array('message'=> $message, 'success' => $success));

    }


}