<?php

namespace App\Controller\superAdmin;

use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/super_admin/stock")
 */
class StockController extends AbstractController
{

    private $em;
    private $stockRepository;

    public function __construct(EntityManagerInterface $em, StockRepository $stockRepository)
    {
        $this->em = $em;
        $this->stockRepository = $stockRepository;

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="index_stock")
     */
    public function index()
    {
        $stocks = $this->stockRepository->findAll();
        return $this->render('superAdmin/stock/index.html.twig', array('stocks' => $stocks));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/maj/stock", name="maj_stock" , options={"expose" =true})
     */
    public function updateQteArticle(Request $request)
    {
        $newQte = $request->get('qte');
        $type = $request->get('type');
        $article = $request->get('id_article');

        $articleStocked = $this->stockRepository->findArticleInStockById((int)$article);
        if ($articleStocked && $articleStocked[0]) {

            if ($type == 'add'){
                $articleStocked[0]->setQte((int)$articleStocked[0]->getQte() + (int) $newQte);
            }
            else{
                if ((int) $newQte > (int)$articleStocked[0]->getQte()) {
                    $message = 'La quantité est supérieur de la quantité du base ';
                    return $this->json(array('message' => $message, 'success' => false));
                }else{
                    $articleStocked[0]->setQte((int)$articleStocked[0]->getQte() - (int) $newQte);
                }
            }
            $this->em->persist($articleStocked[0]);
            $this->em->flush();

            $message = 'La quantité a été modifier';
            $success = true;
        } else {
            $message = 'Aucun article trouver dans le stock ';
            $success =false;

        }

        return $this->json(array('message' => $message, 'success' => $success));

    }


}