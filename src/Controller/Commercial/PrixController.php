<?php


namespace App\Controller\Commercial;


use App\Repository\AchatArticleRepository;
use App\Repository\PrixRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/personelle/prix")
 */
class PrixController extends AbstractController
{
    private $em;
    private $achatArticleRepository;
    private $prixRepository;


    public function __construct(EntityManagerInterface $em, AchatArticleRepository $achatArticleRepository,
                                PrixRepository $prixRepository)
    {
        $this->em = $em;
        $this->achatArticleRepository = $achatArticleRepository;
        $this->prixRepository = $prixRepository;


    }

    /**
     * @Route("/" , name="perso_index_prix")
     */
    public function index()
    {
        $prixs = $this->prixRepository->findAll();
        return $this->render('commercial/prix/index.html.twig' , ['prixs' => $prixs]);
    }

    /**
     * @Route("/details" , name="perso_detail_articles")
     */
    public function details()
    {
        $prixs = $this->prixRepository->findAll();
        return $this->render('commercial/prix/details.html.twig' , ['prixs' => $prixs]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/confirme/prix", name="confirmeUpdatePrix" , options={"expose" =true})
     */
    public function confirmeUpdatePrix(Request $request, StockRepository $stockRepository) {
        $id_article_prix = $request->get('id_article_prix');
        $pachatht =(float)number_format($request->get('pachatht'),3) ;
        $pventettc =(float)number_format($request->get('pventettc'),3) ;
        $articleInPrix = $this->prixRepository->findByIdArticle($id_article_prix);
        if ($articleInPrix[0]){
            //update prin in table prix
            $articleInPrix[0]->setPuAchaHT($pachatht);
            $articleInPrix[0]->setPhAchatTTC((float)number_format(($pachatht * $_ENV['TVA_ARTICLE']), 3));
            $articleInPrix[0]->setPuVenteTTC($pventettc);
            $articleInPrix[0]->setPuVenteHT((float)number_format(($pventettc / $_ENV['TVA_ARTICLE']), 3));
            $articleInPrix[0]->setTaux((float)number_format((($pventettc-$articleInPrix[0]->getPhAchatTTC()) / $articleInPrix[0]->getPhAchatTTC()) * 100 , 2 ));
            $this->em->persist($articleInPrix[0]);
            $this->em->flush();

            //update stock inventaire
            $articleStocked = $stockRepository->findArticleInStockById($id_article_prix);
            if ($articleStocked[0]) {
                $articleStocked[0]->setInventer(false);
                $this->em->persist($articleStocked[0]);
                $this->em->flush();

            }else{
                $message = 'Article \'existe pas';
                $status = "false";
            }
            $message = 'Les prix de cet article a été modifier avec succès';
            $status = "true";

        }else{
            $message = 'Article \'existe pas';
            $status = "false";
        }
        return $this->json(array('message' => $message, 'status' => $status));

    }

}