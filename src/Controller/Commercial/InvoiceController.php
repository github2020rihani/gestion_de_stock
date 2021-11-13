<?php


namespace App\Controller\Commercial;


use App\Entity\BondLivraison;
use App\Entity\Invoice;
use App\Entity\InvoiceArticle;
use App\Repository\ArticleRepository;
use App\Repository\BondLivraisonRepository;
use App\Repository\BonlivraisonArticleRepository;
use App\Repository\ClientRepository;
use App\Repository\InvoiceArticleRepository;
use App\Repository\InvoiceRepository;
use App\Repository\PrixRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("personelle/invoices")
 */
class InvoiceController extends AbstractController
{

    private $em;
    private $invoiceRepository;
    private $bondLivraisonRepository;
    private $bonlivraisonArticleRepository;
    private $clientRepository;
    private $prixRepository;
    private $articleRepository;
    private $invoiceArticleRepository;

    public function __construct(EntityManagerInterface $em, InvoiceRepository $invoiceRepository,
                                ClientRepository $clientRepository,
                                ArticleRepository $articleRepository,
                                PrixRepository $prixRepository,
                                InvoiceArticleRepository $invoiceArticleRepository,
                                BonlivraisonArticleRepository $bonlivraisonArticleRepository,
                                BondLivraisonRepository $bondLivraisonRepository)
    {
        $this->em = $em;
        $this->invoiceRepository = $invoiceRepository;
        $this->bondLivraisonRepository = $bondLivraisonRepository;
        $this->bonlivraisonArticleRepository = $bonlivraisonArticleRepository;
        $this->clientRepository = $clientRepository;
        $this->prixRepository = $prixRepository;
        $this->articleRepository = $articleRepository;
        $this->invoiceArticleRepository = $invoiceArticleRepository;

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="perso_index_invoice")
     */
    public function index()
    {
        $invoices = $this->invoiceRepository->findAll();

        return $this->render('commercial/invoice/index.html.twig', array('invoices' => $invoices, 'perfex_invoice' => $_ENV['PREFIX_FACT']));

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/detail/{id}", name="perso_detail_invoice")
     */
    public function detail(Invoice $invoice)
    {
        if ($invoice->getBonLivraison()) {
            $twig = $this->render('commercial/invoice/detail.html.twig', array('invoice' => $invoice, 'perfex_invoice' => $_ENV['PREFIX_FACT']));
        }else{
            $twig = $this->render('commercial/invoice/detailInvoice.html.twig', array('invoice' => $invoice, 'perfex_invoice' => $_ENV['PREFIX_FACT']));
        }
        return $twig ;

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/change/paiement", name="api_change_payement", options={"expose" = true})
     */

    public function changePayement(Request $request) {
        $id_invoice = $request->get('id_invoice');
        $type_payement = $request->get('type_paiement');
        $invoice = $this->invoiceRepository->find($id_invoice);

        if ($invoice) {
            $bl = $this->bondLivraisonRepository->find($invoice->getBonLivraison());
            if ($bl->getTypePayement() != (int)$type_payement){
                $bl->setTypePayement($type_payement);
                $this->em->persist($bl);
                $this->em->flush();
                $message = 'Paiement a été modifier';
                $status = true ;
            }else{
                $message = 'Aucun modification';
                $status = true ;
            }

        }else{
            $message = 'Aucun fature a ete trouver';
            $status = false ;
        }

        return $this->json(array('message' => $message, 'status' => $status));


    }

    /**
     * @param Request $request
     * @Route("/add", name="perso_add_invoice")
     */
    public function add(Request $request) {
        $customers = $this->clientRepository->findAll();
        $tva = $_ENV['TVA_ARTICLE_PERCENT'];
        $tva_percent = $_ENV['TVA_ARTICLE'];
        $timbre = $_ENV['TIMBRE'];
        //get last bl
        $year = date('Y');
        $totalHt = 0;
        $totalRemise = 0;
        $totalttcGlobal = 0;
        $lasttInvoice = $this->invoiceRepository->getLastInvoiceWithCurrentYear($year);
        if ($lasttInvoice) {
            $lasttInvoice = 000 + $lasttInvoice->getId() + 1;
            $numero_invoice = '000' . $lasttInvoice;
        } else {
            $numero_invoice = '0001';
        }
        try {
            if ($request->isMethod('POST')) {
                //gt type payement and id articles and qte of articles
                $id_articles = $request->get('article');
                $qte_article = $request->get('qte');
                $id_customer = $request->get('customers');
                $type_payement = $request->get('typePayement');
                $customer = $this->clientRepository->find($id_customer);
                //save new Bl
                $invoice = new Invoice();
                $invoice->setCustomer($customer);
                $invoice->setNumero($numero_invoice);
                $invoice->setYear($year);
                $invoice->setCreadetBy($this->getUser());
                $invoice->setExistBl(false);
                $invoice->setStatus(0);
                $invoice->setTypePayement($type_payement);
                $this->em->persist($invoice);
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
                    $articleInvoice = new InvoiceArticle();
                    $articleInvoice->setInvoice($invoice);
                    $articleInvoice->setArticle($this->articleRepository->find($articleExiste[0]['article']['id']));
                    $articleInvoice->setQte($qte_article[$key]);
                    $articleInvoice->setPuht($articleExiste[0]['puVenteHT']);
                    $articleInvoice->setPuhtnet($articleExiste[0]['puVenteHT']);
                    $articleInvoice->setRemise($articleExiste[0]['article']['remise']);
                    $articleInvoice->setTaxe($articleExiste[0]['tva']);
                    $articleInvoice->setTotalht((float)(number_format($totalHtaricle, 3)));
                    $articleInvoice->setPuttc((float)(number_format($puttcArticle, 3)));
                    $articleInvoice->setTotalttc((float)(number_format($totalttcArticle, 3)));
                    $this->em->persist($articleInvoice);
                    $this->em->flush();
                }

                $invoice->setTotalHT((float)(number_format($totalHt, 3)));
                $invoice->setTotalRemise((float)(number_format($totalRemise, 3)));
                $invoice->setTotalTva($_ENV['TVA_ARTICLE_PERCENT'] / 100);
                $invoice->setTotalTTC((float)(number_format($totalHt + 0.19+0.600, 3)));
                $this->em->persist($invoice);
                $this->em->flush();

                $this->addFlash('success', 'Ajout effectué avec succés');
                return $this->redirectToRoute('perso_index_invoice');


            }

        }catch (\Exception $e) {
            $this->addFlash('error',$e->getCode() .':' . $e->getMessage().''.$e->getFile().''.$e->getLine());
            return $this->redirectToRoute('perso_index_invoice');
        }


        return $this->render('commercial/invoice/add.html.twig', array('customers' => $customers, 'tva' => $tva , 'timbre' => $timbre));

    }

    /**
     * @param Request $request
     * @Route("/edit/{invoice}", name="perso_edit_invoice")
     */
    public function edit(Request $request, Invoice $invoice) {
        $customers = $this->clientRepository->findAll();
        $articles = $this->prixRepository->findAll();
        $tva = $_ENV['TVA_ARTICLE_PERCENT'];
        $totalHt = 0;
        $totalRemise = 0;
        $totalttcGlobal = 0;
        $timbre = $_ENV['TIMBRE'];

        try {
            if ($request->isMethod('POST')) {
                try {
                    $typepayement = $request->get('typePayement');
                    $articles_selected = $request->get('article');
                    $articlesTodelete = explode(",", $request->get('articleToDelete')[0]);
                    $qte_articles = $request->get('qte');
                    $customer_id = $request->get('customers');
                    $customer = $this->clientRepository->find($customer_id);

                    //delete old articles invoice
                    if (!empty($articlesTodelete[0])) {
                        foreach ($articlesTodelete as $key=> $value) {
                            $articleDelete = $this->invoiceArticleRepository->getArticleInvoiceByIdArticle($value);
                            if ($articleDelete) {
                                $this->em->remove($articleDelete);
                                $this->em->flush();
                            }
                        }

                    }

                    //update article invoice and invoice
                    $invoice->setCustomer($customer);
                    $invoice->setCreadetBy($this->getUser());
                    $invoice->setTypePayement((int)$typepayement);
                    $this->em->persist($invoice);
                    $this->em->flush();

                    //update articles
                    foreach ($articles_selected as $key=> $value) {
                        $articleInv_exist = $this->invoiceArticleRepository->getArticleInvoiceByIdArticle($value);

                        $prixArticle = $this->prixRepository->getArticleById($value);
                        $totalHtaricle = (float)$prixArticle[0]['puVenteHT'] * $qte_articles[$key];
                        $puttcArticle = (float)$prixArticle[0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE'];
                        $totalttcArticle = (float)$puttcArticle * $qte_articles[$key];
                        $totalHt = $totalHt + (float)$totalHtaricle;
                        $totalRemise = $totalRemise + (float)$prixArticle[0]['article']['remise'];
                        $totalttcGlobal = $totalttcGlobal + (float)$totalHt;

                        if ($articleInv_exist) {
                            $articleInv_exist->setInvoice($invoice);
                            $articleInv_exist->setArticle($this->articleRepository->find($value));
                            $articleInv_exist->setQte($qte_articles[$key]);
                            $articleInv_exist->setPuht($prixArticle[0]['puVenteHT']);
                            $articleInv_exist->setPuhtnet($prixArticle[0]['puVenteHT']);
                            $articleInv_exist->setRemise($prixArticle[0]['article']['remise']);
                            $articleInv_exist->setTaxe($prixArticle[0]['tva']);
                            $articleInv_exist->setTotalht((float)(number_format($totalHtaricle, 3)));
                            $articleInv_exist->setPuttc((float)(number_format($puttcArticle, 3)));
                            $articleInv_exist->setTotalttc((float)(number_format($totalttcArticle, 3)));
                            $this->em->persist($articleInv_exist);
                            $this->em->flush();

                        }else{
                            try {
                                $article_invoice = new InvoiceArticle();
                                $article_invoice->setInvoice($invoice);
                                $article_invoice->setArticle($this->articleRepository->find($value));
                                $article_invoice->setQte($qte_articles[$key]);
                                $article_invoice->setPuht($prixArticle[0]['puVenteHT']);
                                $article_invoice->setPuhtnet($prixArticle[0]['puVenteHT']);
                                $article_invoice->setRemise($prixArticle[0]['article']['remise']);
                                $article_invoice->setTaxe($prixArticle[0]['tva']);
                                $article_invoice->setTotalht((float)(number_format($totalHtaricle, 3)));
                                $article_invoice->setPuttc((float)(number_format($puttcArticle, 3)));
                                $article_invoice->setTotalttc((float)(number_format($totalttcArticle, 3)));
                                $this->em->persist($article_invoice);
                                $this->em->flush();

                            }catch (\Exception $e) {
                                $this->addFlash('error',$e->getCode() .':' . $e->getMessage().''.$e->getFile().''.$e->getLine());
                                return $this->redirectToRoute('perso_index_invoice');
                            }


                        }
                    }
                    $invoice->setTotalHT((float)(number_format($totalHt, 3)));
                    $invoice->setTotalRemise((float)(number_format($totalRemise, 3)));
                    $invoice->setTotalTva($_ENV['TVA_ARTICLE_PERCENT'] / 100);
                    $invoice->setTotalTTC((float)(number_format($totalHt + 0.19, 3)));
                    $invoice->setStatus(0);
                    $this->em->persist($invoice);
                    $this->em->flush();

                    $this->addFlash('success','Bon de livraison a été modifié avec succès');
                    return $this->redirectToRoute('perso_index_invoice');


                } catch (\Exception $e) {
                    $this->addFlash('error',$e->getCode() .':' . $e->getMessage().''.$e->getFile().''.$e->getLine());
                    return $this->redirectToRoute('perso_index_invoice');


                }
            }

        }catch (\Exception $e) {
            $this->addFlash('error',$e->getCode() .':' . $e->getMessage().''.$e->getFile().''.$e->getLine());
            return $this->redirectToRoute('perso_index_invoice');
        }
        return $this->render('commercial/invoice/edit.html.twig',
            array('customers' => $customers, 'tva' => $tva , 'invoice' => $invoice, 'timbre' => $timbre, 'articles' =>$articles));


    }


    /**
     * @param Request $request
     * @Route("personelle/api/get_articles_invoice", name="perso_get_articles_invoice", options={"expose" = true})
     */
    public function getArticlesInvoice(Request $request)
    {
        $id_invoice = $request->get('id_bl');
        $articles_bl = $this->invoiceArticleRepository->findBy(array('invoice' => $id_invoice));

        $articles = [];
        foreach ($articles_bl as $key => $value) {
            $articles[] = $value->getArticle()->getId();
        }
        return $this->json($articles);

    }





}