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
        $data = [];
        $totalGlobalTTC = 0;
        $someTotalttc = 0 ;
        $perfix_invoice = $_ENV['PREFIX_FACT'];
        $articlesVendues = $this->articlesVendueRepository->getArticlesVendus();
        foreach ($articlesVendues as $key=> $art) {
            $data[$key]['date'] = $art->getCreatedAt();
            $data[$key]['num'] =$perfix_invoice.''. $art->getInvoice()->getNumero();
            $data[$key]['addedBy'] = $art->getAddedBy()->getLastName() .' '.$art->getAddedBy()->getFirstName();
            if ($art->getBl()) {
                $data[$key]['client'] = $art->getBl()->getCustomer()->getNom().' '.$art->getBl()->getCustomer()->getPrenom();
                //articles
                foreach ( $art->getBl()->getBonlivraisonArticles() as $index=> $a) {
                    $data[$key]['articles'][$index]['name'] = $a->getArticle()->getRef();
                    $data[$key]['articles'][$index]['puht'] = $a->getPuht();
                    $data[$key]['articles'][$index]['puhtnet'] = $a->getPuhtnet();
                    $data[$key]['articles'][$index]['totalht'] = $a->getTotalht();
                    $data[$key]['articles'][$index]['totalttc'] = $a->getTotalttc();
                    $data[$key]['articles'][$index]['qte'] = $a->getQte();
                    $someTotalttc = $someTotalttc + $a->getTotalttc();

                    $totalGlobalTTC = $totalGlobalTTC+ $someTotalttc ;
                }
                $data[$key]['sometotalttc'] = (float)$someTotalttc;
            }else{
                $data[$key]['client'] = $art->getInvoice()->getCustomer()->getNom().' '.$art->getInvoice()->getCustomer()->getPrenom();
                foreach ( $art->getInvoice()->getInvoiceArticles() as $index=> $a) {
                    $data[$key]['articles'][$index]['name'] = $a->getArticle()->getRef();
                    $data[$key]['articles'][$index]['puht'] = $a->getPuht();
                    $data[$key]['articles'][$index]['puhtnet'] = $a->getPuhtnet();
                    $data[$key]['articles'][$index]['totalht'] = $a->getTotalht();
                    $data[$key]['articles'][$index]['totalttc'] = $a->getTotalttc();
                    $data[$key]['articles'][$index]['qte'] = $a->getQte();

                    $someTotalttc = $someTotalttc + $a->getTotalttc();
                    $totalGlobalTTC = $totalGlobalTTC+ $someTotalttc ;
                }
                $data[$key]['sometotalttc'] = $someTotalttc;

            }
            $data[$key]['totalGlobalTTC'] = $totalGlobalTTC;







        }

//       dd($data);

        return $this->render('commercial/articlesVendue/index.html.twig', array('data' => $data, 'totalGlobalTTC' => $totalGlobalTTC));

    }

}