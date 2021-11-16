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
use App\Repository\InvoiceRepository;
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
    private $invoiceRepository;

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
                                InvoiceRepository $invoiceRepository,
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
        $this->invoiceRepository = $invoiceRepository;
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
                $totalHtaricle = round((float)$devArticle['article']['prixes'][0]['puVenteHT'] * $devArticle['qte']);
                $puttcArticle = (float)$devArticle['article']['prixes'][0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE'];
                $totalttcArticle = round((float)$puttcArticle * $devArticle['qte']);

                $devis[0]['devisArticles'][$key]['totalHtArticle'] = $totalHtaricle;
                $devis[0]['devisArticles'][$key]['puttcArticle'] = $puttcArticle;
                $devis[0]['devisArticles'][$key]['totalttcArticle'] = $totalttcArticle;
                $totalHt = $totalHt + (float)$totalHtaricle;
                $totalRemise = $totalRemise + (float)$devArticle['article']['remise'];
            }
            $totalttcGlobal = $totalttcGlobal + (float)$totalHt + 0.19;

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
    public function add(Request $request)
    {
        $customers = $this->clientRepository->findAll();
        $tva = $_ENV['TVA_ARTICLE_PERCENT'];
        $tva_percent = $_ENV['TVA_ARTICLE'];
        //get last bl
        $year = date('Y');
        $lastBl = $this->bondLivraisonRepository->getLastBlWithCurrentYear($year);
        if ($lastBl) {
            $lastId = 000 + $lastBl->getId() + 1;
            $numero_bl = '000' . $lastId;
        } else {
            $numero_bl = '0001';
        }
        $totalHt = 0;
        $totalRemise = 0;
        $totalttcGlobal = 0;

        if ($request->isMethod('POST')) {
            //gt type payement and id articles and qte of articles
            $id_articles = $request->get('article');
            $qte_article = $request->get('qte');
            $id_customer = $request->get('customers');
            $type_payement = $request->get('typePayement');
            $customer = $this->clientRepository->find($id_customer);
            //save new Bl
            $bl = new BondLivraison();
            $bl->setCustomer($customer);
            $bl->setNumero($numero_bl);
            $bl->setYear($year);
            $bl->setCreatedBy($this->getUser());
            $bl->setExistDevi(false);
            $bl->setStatus(0);
            $bl->setTypePayement($type_payement);
            $this->em->persist($bl);
            $this->em->flush();


            foreach ($id_articles as $key => $id_art) {
                $articleExiste = $this->prixRepository->getArticleById($id_art);
                $totalHtaricle = (float)$articleExiste[0]['puVenteHT'] * $qte_article[$key];
                $puttcArticle = (float)$articleExiste[0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE'];
                $totalttcArticle = (float)$puttcArticle * $qte_article[$key];
                $totalHt = $totalHt + (float)$totalHtaricle;
                $totalRemise = $totalRemise + (float)$articleExiste[0]['article']['remise'];
                $totalttcGlobal = $totalttcGlobal + (float)$totalHt;


                //save article bl
                $article_bl = new BonlivraisonArticle();
                $article_bl->setBonLivraison($bl);
                $article_bl->setArticle($this->articleRepository->find($articleExiste[0]['article']['id']));
                $article_bl->setQte($qte_article[$key]);
                $article_bl->setPuht($articleExiste[0]['puVenteHT']);
                $article_bl->setPuhtnet($articleExiste[0]['puVenteHT']);
                $article_bl->setRemise($articleExiste[0]['article']['remise']);
                $article_bl->setTaxe($articleExiste[0]['tva']);
                $article_bl->setTotalht((float)(number_format($totalHtaricle, 3)));
                $article_bl->setPuttc((float)(number_format($puttcArticle, 3)));
                $article_bl->setTotalttc((float)(number_format($totalttcArticle, 3)));
                $this->em->persist($article_bl);
                $this->em->flush();
            }

            $bl->setTotalHT((float)(number_format($totalHt, 3)));
            $bl->setTotalRemise((float)(number_format($totalRemise, 3)));
            $bl->setTotalTVA($_ENV['TVA_ARTICLE_PERCENT'] / 100);
            $bl->setTotalTTC((float)(number_format($totalHt + 0.19, 3)));
            $this->em->persist($bl);
            $this->em->flush();

            $this->addFlash('success', 'Ajout effectué avec succés');
            return $this->redirectToRoute('perso_index_bl');


        }
        return $this->render('commercial/bondLivraison/add.html.twig', array('customers' => $customers, 'tva' => $tva));

    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{id}", name="perso_edit_bl")
     */
    public function edit(BondLivraison $id, Request $request)
    {
        $customers = $this->clientRepository->findAll();
        $articles = $this->prixRepository->findAll();
        $tva = $_ENV['TVA_ARTICLE_PERCENT'];
        $totalHt = 0;
        $totalRemise = 0;
        $totalttcGlobal = 0;
        if ($request->isMethod('POST')) {
            try {
                $typepayement = $request->get('typePayement');
                $articles_selected = $request->get('article');
                $qte_articles = $request->get('qte');
                $customer_id = $request->get('customers');
                $customer = $this->clientRepository->find($customer_id);
                //chek if exist invoice
                $invoice = $this->invoiceRepository->findInvoiceByIdBl($id->getId());
                if ($invoice){
                    $this->em->remove($invoice);
                    $this->em->flush();
                }


                //update article bl and bl
                $id->setCustomer($customer);
                $id->setCreatedBy($this->getUser());
                $id->setExistDevi(false);
                $id->setStatus(0);
                $id->setTypePayement((int)$typepayement);
                $this->em->persist($id);
                $this->em->flush();
                //delete old article bl
                $old_articles = $this->bonlivraisonArticleRepository->findBy(array('bonLivraison' => $id));
                if ($old_articles) {
                    foreach ($old_articles as $key => $value) {
                        $this->em->remove($old_articles);
                        $this->em->flush();
                    }
                }

                //update articles
                foreach ($articles_selected as $key=> $value) {

                    $prixArticle = $this->prixRepository->getArticleById($value);
                    $totalHtaricle = (float)$prixArticle[0]['puVenteHT'] * $qte_articles[$key];
                    $puttcArticle = (float)$prixArticle[0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE'];
                    $totalttcArticle = (float)$puttcArticle * $qte_articles[$key];
                    $totalHt = $totalHt + (float)$totalHtaricle;
                    $totalRemise = $totalRemise + (float)$prixArticle[0]['article']['remise'];
                    $totalttcGlobal = $totalttcGlobal + (float)$totalHt;


                        try {
                            $articleBL = new BonlivraisonArticle();
                            $articleBL->setBonLivraison($id);
                            $articleBL->setArticle($this->articleRepository->find($value));

                            $articleBL->setQte($qte_articles[$key]);
                            $articleBL->setPuht($prixArticle[0]['puVenteHT']);
                            $articleBL->setPuhtnet($prixArticle[0]['puVenteHT']);
                            $articleBL->setRemise($prixArticle[0]['article']['remise']);
                            $articleBL->setTaxe($prixArticle[0]['tva']);
                            $articleBL->setTotalht((float)(number_format($totalHtaricle, 3)));
                            $articleBL->setPuttc((float)(number_format($puttcArticle, 3)));
                            $articleBL->setTotalttc((float)(number_format($totalttcArticle, 3)));
                            $this->em->persist($articleBL);
                            $this->em->flush();
                        }catch (\Exception $e) {
                            $this->addFlash('error',$e->getCode() .':' . $e->getMessage().''.$e->getFile().''.$e->getLine());
                            return $this->redirectToRoute('perso_index_bl');
                        }



                }
                $id->setTotalHT((float)(number_format($totalHt, 3)));
                $id->setTotalRemise((float)(number_format($totalRemise, 3)));
                $id->setTotalTVA($_ENV['TVA_ARTICLE_PERCENT'] / 100);
                $id->setTotalTTC((float)(number_format($totalHt + 0.19, 3)));
                $id->setStatus(0);
                $this->em->persist($id);
                $this->em->flush();

                $this->addFlash('success','Bon de livraison a été modifié avec succès');
                return $this->redirectToRoute('perso_index_bl');


            } catch (\Exception $e) {
                $this->addFlash('error',$e->getCode() .':' . $e->getMessage().''.$e->getFile().''.$e->getLine());
                return $this->redirectToRoute('perso_index_bl');


            }
        }
        return $this->render('commercial/bondLivraison/edit.html.twig',
            array(
                'customers' => $customers,
                'bl' => $id,
                'tva' => $tva,
                'articles' => $articles));

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/detail/{id}", name="perso_detail_bl")
     */
    public function detail(BondLivraison $id)
    {
        return $this->render('commercial/bondLivraison/detail.html.twig', array('bl' => $id, 'perfix_bl' => $_ENV['PREFIX_BL']));

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
        $year = date('Y');
        $lastBl = $this->bondLivraisonRepository->getLastBlWithCurrentYear($year);
        if ($lastBl) {
            $lastId = 000 + $lastBl->getId() + 1;
            $numero_bl = '000' . $lastId;
        } else {
            $numero_bl = '0001';
        }
        if ($devis && $devis[0]) {
            $customer = $this->clientRepository->find($devis[0]['client']['id']);


            //save bl
            $bl = new BondLivraison();
            $bl->setCustomer($customer);
            $bl->setNumero($numero_bl);
            $bl->setYear($year);
            $bl->setCreatedBy($this->getUser());
            $bl->setExistDevi(true);
            $bl->setStatus(1);
            $bl->setTypePayement($type_payement);
            $bl->setDevi($this->devisRepository->find($devis[0]['id']));
            $this->em->persist($bl);
            $this->em->flush();


            foreach ($devis[0]['devisArticles'] as $key => $devArticle) {
                $totalHtaricle = (float)$devArticle['article']['prixes'][0]['puVenteHT'] * $devArticle['qte'];
                $puttcArticle = (float)$devArticle['article']['prixes'][0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE'];
                $totalttcArticle = round((float)$puttcArticle * $devArticle['qte']);
                $totalHt = $totalHt + (float)$totalHtaricle;
                $totalRemise = $totalRemise + (float)$devArticle['article']['remise'];
                $totalttcGlobal = $totalttcGlobal + (float)$totalHt;

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
            $bl->setTotalTTC((float)(number_format($totalHt + 0.19, 3)));
            $this->em->persist($bl);
            $this->em->flush();

            //save invoice
            $year = date('Y');
            $lasttInvoice = $this->invoiceRepository->getLastInvoiceWithCurrentYear($year);
            if ($lasttInvoice) {
                $lasttInvoice = 000 + $lasttInvoice->getId() + 1;
                $numero_invoice = '000' . $lasttInvoice;
            } else {
                $numero_invoice = '0001';
            }
            $totalInvoice = $totalHt + $_ENV['TIMBRE'] + 0.19;
            $invoice = new Invoice();
            $invoice->setBonLivraison($bl);
            $invoice->setYear($year);
            $invoice->setStatus(1);
            $invoice->setExistBl(1);
            $invoice->setNumero($numero_invoice);
            $invoice->setTotalTTC((float)(number_format($totalInvoice, 3)));
            $invoice->setTimbre($_ENV['TIMBRE']);
            $invoice->setCreadetBy($this->getUser());
            $this->em->persist($invoice);
            $this->em->flush();

            //update status devis
            $updateDevis = $this->devisRepository->find($devis[0]['id']);
            $updateDevis->setStatus(1);
            $this->em->persist($updateDevis);
            $this->em->flush();


            $message = 'Bon de livraison et une facture a été enregistrer';
            $status = true;


        } else {
            $message = 'Aucun devi a été trouver';
            $status = false;
        }

        return $this->json(array('status' => $status, 'message' => $message));
    }


    /**
     * @param Request $request
     * @param BondLivraison $bl
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/transfert/bl/to/invoice/{bl}", name="perso_transfert_bl_to_invoince")
     */

    public function transfertBLToInvoice(Request $request, BondLivraison $bl){
        try {
            $year = date('Y');
            $lasttInvoice = $this->invoiceRepository->getLastInvoiceWithCurrentYear($year);
            if ($lasttInvoice) {
                $lasttInvoice = 000 + $lasttInvoice->getId() + 1;
                $numero_invoice = '000' . $lasttInvoice;
            } else {
                $numero_invoice = '0001';
            }
            $totalInvoice = $bl->getTotalHT() + $_ENV['TIMBRE'] + 0.19;

            $invoice = new Invoice();
            $invoice->setBonLivraison($bl);
            $invoice->setYear($year);
            $invoice->setStatus(1);
            $invoice->setNumero($numero_invoice);
            $invoice->setTotalTTC((float)(number_format($totalInvoice, 3)));
            $invoice->setTimbre($_ENV['TIMBRE']);
            $invoice->setCreadetBy($this->getUser());
            $invoice->setExistBl(1);
            $this->em->persist($invoice);
            $bl->setStatus(1);
            $this->em->persist($bl);
            $this->em->flush();
            $this->addFlash('success','Bon de livraison a été transféré avec succès');
            return $this->redirectToRoute('perso_index_invoice');


        }catch (\Exception $e) {
            $this->addFlash('error',$e->getCode() .':' . $e->getMessage().''.$e->getFile().''.$e->getLine());
            return $this->redirectToRoute('perso_index_invoice');
        }

    }


    /**
     * @param Request $request
     * @Route("personelle/api/get_articles_bl", name="perso_get_articles_bl", options={"expose" = true})
     */
    public function getArticlesBL(Request $request)
    {
        $îd_bl = $request->get('id_bl');
        $articles_bl = $this->bonlivraisonArticleRepository->findBy(array('bonLivraison' => $îd_bl));

        $articles = [];
        foreach ($articles_bl as $key => $value) {
            $articles[] = $value->getArticle()->getId();
        }
        return $this->json($articles);

    }


}