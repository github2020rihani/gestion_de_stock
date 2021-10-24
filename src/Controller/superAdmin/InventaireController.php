<?php


namespace App\Controller\superAdmin;


use App\Repository\InventaireArticleRepository;
use App\Repository\InventaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super_admin/inventaires")
 */
class InventaireController extends AbstractController
{

    private $em ;
    private $inventaireRepository;
    public function __construct(EntityManagerInterface $em, InventaireRepository $inventaireRepository)
    {
        $this->em = $em ;
        $this->inventaireRepository = $inventaireRepository;
    }

    /**
     * @Route("/", name="index_inventaire")
     */
    public function index(PaginatorInterface $paginator, Request $request, InventaireArticleRepository $inventaireArticleRepository) {

        $invtaires = $this->inventaireRepository->findAll();
        $lastInventaire = $this->inventaireRepository->findLastInventaire();
        $query = $inventaireArticleRepository->getDataInventaire($lastInventaire[0]->getId());
        $dataInv = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            1 /*limit per page*/
        );
//        $dataInv =


        return $this->render('superAdmin/inventaire/index.html.twig', array('inventaires' => $invtaires, 'lastInv'=> $dataInv));
    }
}