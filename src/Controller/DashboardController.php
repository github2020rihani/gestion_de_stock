<?php


namespace App\Controller;

use App\Repository\PrixRepository;
use App\Repository\StockRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractController
{

    private $em;
    private $stockRepository;
    private $paginator;
    private $prixRepository;
    private $userRepository;

    public function __construct(EntityManagerInterface $em,
                                PrixRepository $prixRepository,
                                UserRepository $userRepository,
                                StockRepository $stockRepository, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->stockRepository = $stockRepository;
        $this->paginator = $paginator;
        $this->prixRepository = $prixRepository;
        $this->userRepository = $userRepository;

    }

    /**
     * @Route("/super_admin", name="dashboard_super_admin")
     */
    public function dashboard(Request $request)
    {

        $query = $this->stockRepository->findBy([], ['dateEntree'=>'DESC']);
        $stocks  = $this->paginator->paginate($query, $request->query->get('page', 1), 3);
        $query2 = $this->prixRepository->findAll();
        $prixs  = $this->paginator->paginate($query2, $request->query->get('page', 1), 3);
        $users = $this->userRepository->findUserByDepartementSaufSuperAdmin($this->getUser());

        return $this->render('dashboard/dashboard_super_admin.html.twig', [
            'stocks' => $stocks,
            'prixs' => $prixs,
            'users' =>$users
        ]);
    }

    /**
     * @Route("/admin", name="dashboard_admin")
     */
    public function dashboardAdmin()
    {

        return $this->render('dashboard/dashboard_admin.html.twig');
    }

    /**
     * @Route("/responsable", name="dashboard_responsable")
     */
    public function dashboardResponsable()
    {

        return $this->render('dashboard/dashboard_responsable.html.twig');
    }

    /**
     * @Route("/gerant", name="dashboard_gerant")
     */
    public function dashboardGerant()
    {

        return $this->render('dashboard/dashboard_gerant.html.twig');
    }

    /**
     * @Route("/achat", name="dashboard_achat")
     */
    public function dashboardAchat(Request$request)
    {
        $articles = '';
        $prixs = '';
        $query = $this->stockRepository->getArticleWhereQte();
        $queryPrix = $this->prixRepository->findAll();
        if ($query){
            $articles = $this->paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                6 /*limit per page*/
            );
        }
        if ($queryPrix){
            $prixs = $this->paginator->paginate(
                $queryPrix, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                6 /*limit per page*/
            );
        }

            return $this->render('dashboard/dashboard_achat.html.twig', array('articles' => $articles, 'prixs' => $prixs));
    }
}
