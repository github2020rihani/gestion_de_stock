<?php


namespace App\Controller\Commercial;


use App\Entity\BondLivraison;
use App\Entity\BonlivraisonArticle;
use App\Entity\Devis;
use App\Entity\Invoice;
use App\Repository\ArticleRepository;
use App\Repository\BondLivraisonRepository;
use App\Repository\BonlivraisonArticleRepository;
use App\Repository\ClientRepository;
use App\Repository\DevisArticleRepository;
use App\Repository\DevisRepository;
use App\Repository\PrixRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\NodeVisitor\EscaperNodeVisitor;

/**
 * Class BondLivraisonController
 * @package App\Controller\Commercial
 * @Route("personelle/BL")
 */
class BondLivraisonController extends AbstractController
{
    private $em;
    private $articleRepository;
    private $devisArticleRepository;
    private $devisRepository;
    private $bondLivraisonRepository;
    private $bonlivraisonArticleRepository;
    private $clientRepository;
    private $prixRepository;

    /**
     * BondLivraisonController constructor.
     * @param EntityManagerInterface $em
     * @param ArticleRepository $articleRepository
     * @param DevisArticleRepository $devisArticleRepository
     * @param DevisRepository $devisRepository
     * @param BondLivraisonRepository $bondLivraisonRepository
     * @param BonlivraisonArticleRepository $bonlivraisonArticleRepository
     */
    public function __construct(EntityManagerInterface $em,
                                ArticleRepository $articleRepository,
                                DevisArticleRepository $devisArticleRepository,
                                DevisRepository $devisRepository,
                                BondLivraisonRepository $bondLivraisonRepository,
                                ClientRepository $clientRepository,
                                PrixRepository $prixRepository,
                                BonlivraisonArticleRepository $bonlivraisonArticleRepository)
    {
        $this->em = $em;
        $this->articleRepository = $articleRepository;
        $this->devisArticleRepository = $devisArticleRepository;
        $this->devisRepository = $devisRepository;
        $this->bondLivraisonRepository = $bondLivraisonRepository;
        $this->bonlivraisonArticleRepository = $bonlivraisonArticleRepository;
        $this->clientRepository = $clientRepository;
        $this->prixRepository = $prixRepository;
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/transfert/{id}", name="perso_transfert_devi")
     */
    public function transfertDeviToBL(Request $request, Devis $id)
    {
        $devis = $this->devisRepository->findDetailDeviAndStock($id);
        $totalHt = 0;
        $totalRemise = 0;
        $totalttcGlobal = 0;
        if ($devis && $devis[0]) {

            foreach ($devis[0]['devisArticles'] as $key => $devArticle) {
                $totalHtaricle = (float)$devArticle['article']['prixes'][0]['puVenteHT'] * $devArticle['qte'];
                $puttcArticle = (float)$devArticle['article']['prixes'][0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE'];
                $totalttcArticle = (float)$puttcArticle * $devArticle['qte'];

                $devis[0]['devisArticles'][$key]['totalHtArticle'] = $totalHtaricle;
                $devis[0]['devisArticles'][$key]['puttcArticle'] = $puttcArticle;
                $devis[0]['devisArticles'][$key]['totalttcArticle'] = $totalttcArticle;
                $totalHt = $totalHt + (float)$totalHtaricle;
                $totalRemise = $totalRemise + (float)$devArticle['article']['remise'];
                $totalttcGlobal = $totalttcGlobal + (float)$totalHt + 0.19;
            }
        }
        return $this->render('commercial/bondLivraison/transfertDeviToBl.html.twig', array(
            'devis' => $devis[0],
            'totalHTglobal' => $totalHt,
            'totalRemiseGlobal' => $totalRemise,
            'totalttcGlobal' => $totalttcGlobal,
            'taxe' => $_ENV['TVA_ARTICLE_PERCENT']));
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="perso_index_bl")
     */
    public function index()
    {
        $bls = $this->bondLivraisonRepository->findAll();
        return $this->render('commercial/bondLivraison/index.html.twig', array('bls' => $bls, 'perfix_bl' => $_ENV['PREFIX_BL']));

    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/add", name="perso_add_bl")
     */
    public function add()
    {
        $customers = $this->clientRepository->findAll();
        return $this->render('commercial/bondLivraison/add.html.twig', array('customers' => $customers));

    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{id}", name="perso_edit_bl")
     */
    public function edit(BondLivraison $id)
    {
        $customers = $this->clientRepository->findAll();
        return $this->render('commercial/bondLivraison/edit.html.twig', array('customers' => $customers, 'bl' => $id));

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/detail/{id}", name="perso_detail_bl")
     */
    public function detail(BondLivraison $id)
    {
        return $this->render('commercial/bondLivraison/detail.html.twig', array( 'bl' => $id, 'perfix_bl' => $_ENV['PREFIX_BL']));

    }

    /**
     * @param Request $request
     * @Route("/api/saveBL/facture/", name="perso_api_saveBlAndFacture_bl", options={"expose" = true})
     */
    public function saveBlAndFacture(Request $request)
    {
        $id_devis = $request->get('id_devis');
        $type_payement = $request->get('type_payement');
        $devis = $this->devisRepository->findDetailDeviAndStock($id_devis);
        $totalHt = 0;
        $totalRemise = 0;
        $totalttcGlobal = 0;
        if ($devis && $devis[0]) {
            $customer = $this->clientRepository->find($devis[0]['client']['id']);


            //save bl
            $bl = new BondLivraison();
            $bl->setCustomer($customer);
            $bl->setNumero($devis[0]['numero']);
            $bl->setCreatedBy($this->getUser());
            $bl->setExistDevi(true);
            $bl->setTypePayement($type_payement);
            $bl->setDevi($this->devisRepository->find($devis[0]['id']));
            $this->em->persist($bl);
            $this->em->flush();



            foreach ($devis[0]['devisArticles'] as $key => $devArticle) {
                $totalHtaricle = (float)$devArticle['article']['prixes'][0]['puVenteHT'] * $devArticle['qte'];
                $puttcArticle = (float)$devArticle['article']['prixes'][0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE'];
                $totalttcArticle = (float)$puttcArticle * $devArticle['qte'];
                $totalHt = $totalHt + (float)$totalHtaricle;
                $totalRemise = $totalRemise + (float)$devArticle['article']['remise'];
                $totalttcGlobal = $totalttcGlobal + (float)$totalHt + 0.19;

                //save article bl
                $article_bl = new BonlivraisonArticle();
                $article_bl->setBonLivraison($bl);
                $article_bl->setArticle($this->articleRepository->find($devArticle['article']['id']));
                $article_bl->setQte($devArticle['qte']);
                $article_bl->setPuht($devArticle['article']['prixes'][0]['puVenteHT']);
                $article_bl->setPuhtnet($devArticle['article']['prixes'][0]['puVenteHT']);
                $article_bl->setRemise($devArticle['article']['remise']);
                $article_bl->setTaxe($_ENV['TVA_ARTICLE_PERCENT']);
                $article_bl->setTotalht((float)(number_format($totalHtaricle, 3)));
                $article_bl->setPuttc((float)(number_format($puttcArticle, 3)));
                $article_bl->setTotalttc((float)(number_format($totalttcArticle, 3)));
                $this->em->persist($article_bl);
                $this->em->flush();
            }
            $bl->setTotalHT((float)(number_format($totalHt, 3)));
            $bl->setTotalRemise((float)(number_format($totalRemise, 3)));
            $bl->setTotalTVA($_ENV['TVA_ARTICLE_PERCENT'] / 100);
            $bl->setTotalTTC((float)(number_format($totalttcGlobal, 3)));
            $this->em->persist($bl);
            $this->em->flush();

            //save invoice
            $totalInvoice = $totalttcGlobal + $_ENV['TIMBRE'];
            $invoice = new Invoice();
            $invoice->setBonLivraison($bl);
            $invoice->setNumero($devis[0]['numero']);
            $invoice->setTotalTTC((float)(number_format($totalInvoice, 3)));
            $invoice->setTimbre($_ENV['TIMBRE']);
            $invoice->setCreadetBy($this->getUser());
            $this->em->persist($invoice);
            $this->em->flush();

            //update status devis
          $updateDevis =  $this->devisRepository->find($devis[0]['id']);
          $updateDevis->setStatus(1);
          $this->em->persist($updateDevis);
          $this->em->flush();


            $message = 'Bon de livraison et une facture a été enregistrer';
            $status = true;


        }else{
            $message = 'Aucun devi a été trouver';
            $status = false ;
        }

        return $this->json(array('status' => $status , 'message' => $message));
    }


}