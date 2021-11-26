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
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
    private $stockRepository;

    public function __construct(EntityManagerInterface $em, InvoiceRepository $invoiceRepository,
                                ClientRepository $clientRepository,
                                ArticleRepository $articleRepository,
                                PrixRepository $prixRepository,
                                StockRepository $stockRepository,
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
        $this->stockRepository = $stockRepository;

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
        } else {
            $twig = $this->render('commercial/invoice/detailInvoice.html.twig', array('invoice' => $invoice, 'perfex_invoice' => $_ENV['PREFIX_FACT']));
        }
        return $twig;

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/change/paiement", name="api_change_payement", options={"expose" = true})
     */

    public function changePayement(Request $request)
    {
        $id_invoice = $request->get('id_invoice');
        $type_payement = $request->get('type_paiement');
        $invoice = $this->invoiceRepository->find($id_invoice);

        if ($invoice) {
            $bl = $this->bondLivraisonRepository->find($invoice->getBonLivraison());
            if ($bl->getTypePayement() != (int)$type_payement) {
                $bl->setTypePayement($type_payement);
                $this->em->persist($bl);
                $this->em->flush();
                $message = 'Paiement a été modifier';
                $status = true;
            } else {
                $message = 'Aucun modification';
                $status = true;
            }

        } else {
            $message = 'Aucun fature a ete trouver';
            $status = false;
        }

        return $this->json(array('message' => $message, 'status' => $status));


    }

    /**
     * @param Request $request
     * @Route("/add", name="perso_add_invoice")
     */
    public function add(Request $request)
    {
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
                    $totalHtaricle = (float)round($articleExiste[0]['puVenteHT'] * $qte_article[$key]);
                    $puttcArticle = (float)round($articleExiste[0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE']);
                    $totalttcArticle = round((float)$puttcArticle * $qte_article[$key]);
                    $totalHt = round($totalHt + (float)$totalHtaricle);
                    $totalRemise = round($totalRemise + (float)$articleExiste[0]['article']['remise']);


                    //save article bl
                    $articleInvoice = new InvoiceArticle();
                    $articleInvoice->setInvoice($invoice);
                    $articleInvoice->setArticle($this->articleRepository->find($articleExiste[0]['article']['id']));
                    $articleInvoice->setQte($qte_article[$key]);
                    $articleInvoice->setPuht($articleExiste[0]['puVenteHT']);
                    $articleInvoice->setPuhtnet($articleExiste[0]['puVenteHT']);
                    $articleInvoice->setRemise($articleExiste[0]['article']['remise']);
                    $articleInvoice->setTaxe($articleExiste[0]['tva']);
                    $articleInvoice->setTotalht((float)(($totalHtaricle)));
                    $articleInvoice->setPuttc((float)(($puttcArticle)));
                    $articleInvoice->setTotalttc((float)(($totalttcArticle)));
                    $this->em->persist($articleInvoice);
                    $this->em->flush();
                    //change stock
                    $Lingart = $this->articleRepository->find($articleExiste[0]['article']['id']);
                    $Lingart->setQteReserved($qte_article[$key]);
                    $this->em->persist($Lingart);
                    //update stocked
                    $lingArtPr = $this->prixRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
                    $newQte = (int)$lingArtPr->getQte() - (int)$qte_article[$key];
                    $lingArtPr->setQte($newQte);
                    $this->em->persist($lingArtPr);

                    $lingeArtStock = $this->stockRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
                    $lingeArtStock->setQte($newQte);
                    $this->em->persist($lingeArtStock);
                    $this->em->flush();


                }
                $totalttcGlobal = $totalttcGlobal + (float)$totalHt;

                $invoice->setTotalHT((float)(($totalHt)));
                $invoice->setTotalRemise((float)(($totalRemise)));
                $invoice->setTotalTva($_ENV['TVA_ARTICLE_PERCENT'] / 100);
                $invoice->setTotalTTC((float)(($totalHt + 0.19 + 0.600)));
                $this->em->persist($invoice);
                $this->em->flush();

                //
                //generate invoice
                self::generateInvoice($invoice->getId(), $request, $invoice);


                $this->addFlash('success', 'Ajout effectué avec succés');
                return $this->redirectToRoute('perso_index_invoice');


            }

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getCode() . ':' . $e->getMessage() . '' . $e->getFile() . '' . $e->getLine());
            return $this->redirectToRoute('perso_index_invoice');
        }


        return $this->render('commercial/invoice/add.html.twig', array('customers' => $customers, 'tva' => $tva, 'timbre' => $timbre));

    }

    /**
     * @param Request $request
     * @Route("/edit/{invoice}", name="perso_edit_invoice")
     */
    public function edit(Request $request, Invoice $invoice)
    {
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
                    $qte_articles = $request->get('qte');
                    $customer_id = $request->get('customers');
                    $customer = $this->clientRepository->find($customer_id);

                    //delete old article invoice
                    $old_articles = $this->invoiceArticleRepository->findBy(array('invoice' => $invoice));
                    if ($old_articles) {
                        foreach ($old_articles as $key => $value) {
                            $this->em->remove($value);
                            $this->em->flush();
                        }
                    }


                    //update article invoice and invoice
                    $invoice->setCustomer($customer);
                    $invoice->setCreadetBy($this->getUser());
                    $invoice->setTypePayement((int)$typepayement);
                    $this->em->persist($invoice);
                    $this->em->flush();

                    //update articles
                    foreach ($articles_selected as $key => $value) {

                        $prixArticle = $this->prixRepository->getArticleById($value);
                        $totalHtaricle = (float)round($prixArticle[0]['puVenteHT'] * $qte_articles[$key]);
                        $puttcArticle = (float)round($prixArticle[0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE']);
                        $totalttcArticle = round((float)$puttcArticle * $qte_articles[$key]);
                        $totalHt = round($totalHt + (float)$totalHtaricle);
                        $totalRemise = round($totalRemise + (float)$prixArticle[0]['article']['remise']);

                        try {
                            $article_invoice = new InvoiceArticle();
                            $article_invoice->setInvoice($invoice);
                            $article_invoice->setArticle($this->articleRepository->find($value));
                            $article_invoice->setQte($qte_articles[$key]);
                            $article_invoice->setPuht($prixArticle[0]['puVenteHT']);
                            $article_invoice->setPuhtnet($prixArticle[0]['puVenteHT']);
                            $article_invoice->setRemise($prixArticle[0]['article']['remise']);
                            $article_invoice->setTaxe($prixArticle[0]['tva']);
                            $article_invoice->setTotalht((float)(($totalHtaricle)));
                            $article_invoice->setPuttc((float)(($puttcArticle)));
                            $article_invoice->setTotalttc((float)(($totalttcArticle)));
                            $this->em->persist($article_invoice);
                            $this->em->flush();


                            //change stock
                            $Lingart = $this->articleRepository->find($value);
                            $lingArtPr = $this->prixRepository->findOneBy(array('article' => $value));
                            $lingeArtStock = $this->stockRepository->findOneBy(array('article' => $value));

                            $sourceQte = (int)$lingArtPr->getQte() + (int)$Lingart->getQteReserved();
                            $Lingart->setQteReserved($qte_articles[$key]);
                            $this->em->persist($Lingart);
                            //update stocked
                            $newQte = (int)$sourceQte - (int)$qte_articles[$key];
                            $lingArtPr->setQte($newQte);
                            $this->em->persist($lingArtPr);
                            $lingeArtStock->setQte($newQte);
                            $this->em->persist($lingeArtStock);
                            $this->em->flush();


                        } catch (\Exception $e) {
                            $this->addFlash('error', $e->getCode() . ':' . $e->getMessage() . '' . $e->getFile() . '' . $e->getLine());
                            return $this->redirectToRoute('perso_index_invoice');
                        }


                    }
                    $totalttcGlobal = $totalttcGlobal + (float)$totalHt;

                    $invoice->setTotalHT((float)(($totalHt)));
                    $invoice->setTotalRemise((float)(($totalRemise)));
                    $invoice->setTotalTva($_ENV['TVA_ARTICLE_PERCENT'] / 100);
                    $invoice->setTotalTTC((float)(($totalHt + 0.19 + 0.600)));
                    $invoice->setStatus(0);
                    $this->em->persist($invoice);
                    $this->em->flush();

                    //generate invoice
                    self::generateInvoice($invoice->getId(), $request, $invoice);


                    $this->addFlash('success', 'Bon de livraison a été modifié avec succès');
                    return $this->redirectToRoute('perso_index_invoice');


                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getCode() . ':' . $e->getMessage() . '' . $e->getFile() . '' . $e->getLine());
                    return $this->redirectToRoute('perso_index_invoice');


                }
            }

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getCode() . ':' . $e->getMessage() . '' . $e->getFile() . '' . $e->getLine());
            return $this->redirectToRoute('perso_index_invoice');
        }
        return $this->render('commercial/invoice/edit.html.twig',
            array('customers' => $customers, 'tva' => $tva, 'invoice' => $invoice, 'timbre' => $timbre, 'articles' => $articles));


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

    public function generateInvoice($id_invoice, $request, $invoice)
    {
        $dataInvoice = $this->invoiceRepository->getDataInvoiceSeul($id_invoice);
        $uploadDir = $this->getParameter('uploads_directory');
        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Courier');
        $pdfOptions->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('commercial/invoice/printInvoice.html.twig', [
            'invoice' => $dataInvoice[0]
        ]);
        $html .= '<link type="text/css" href="/public/app/assetes/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $contxt = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($contxt);
        $dompdf->render();
        $output = $dompdf->output();
        $codeClient = $invoice->getCustomer()->getCode();
        $yearBl = $invoice->getYear();
        $numBl = $invoice->getNumero();

        $mypath = $uploadDir . 'invoice/customer_' . $codeClient;
        $newFilename = 'invoice_' . $numBl . '_' . $yearBl . '.pdf';
        $pdfFilepath = $uploadDir . 'invoice/customer_' . $codeClient . '/' . $newFilename;
        if (!is_dir($mypath)) {
            mkdir($mypath, 0777, TRUE);

        }
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/invoice/customer_' . $codeClient . '/' . $newFilename;

//        if (file_exists($pdfFilepath)) {
//            unlink($pdfFilepath);
//        }
        file_put_contents($pdfFilepath, $output);

        $invoice->setFile($baseurl);
        $this->em->persist($invoice);
        $this->em->flush();


    }


    /**
     * @param Request $request
     * @Route("api/print/invoice", name="perso_print_invoice" , options={"expose" = true})
     */
    public function printBL(Request $request)
    {
        $id_invoice = $request->get('id_invoice');
        $invoice = $this->invoiceRepository->find($id_invoice);
        $uploadDir = $this->getParameter('uploads_directory');
        if ($invoice->getFile()) {
            $success = true;
            $message = '';
            $file_with_path = $uploadDir . strstr($invoice->getFile(), 'invoice');
            $response = new BinaryFileResponse ($file_with_path);
            $response->headers->set('Content-Type', 'application/pdf; charset=utf-8', 'application/force-download');
            $response->headers->set('Content-Disposition', 'attachment; filename=devis.pdf');
        } else {
            $mssage = 'Pas de bl enregistrer ';
            $success = false;
            $response = null;
        }
        return $response;

    }

    /**
     * @param Invoice $id
     * @Route("/delete/{id}", name="perso_delete_invoice")
     */
    public function delete(Invoice $id)
    {
        $invoice = $this->invoiceRepository->find($id);

        if ($invoice) {
            foreach ($invoice->getinvoiceArticles() as $key=> $value) {

                $article = $this->articleRepository->find($value->getArticle()->getId());
                $articlePrix = $this->prixRepository->findOneBy(array('article' => $value->getArticle()->getId()));
                $articeStocked = $this->stockRepository->findOneBy(array('article' => $value->getArticle()->getId()));

                $sourceQte = (int)$articlePrix->getQte() + (int)$article->getQteReserved();
                $article->setQteReserved(0);
                $this->em->persist($article);
                //update stocked
                $newQte = $sourceQte;
                $articlePrix->setQte($newQte);
                $this->em->persist($articlePrix);

                $articeStocked->setQte($newQte);
                $this->em->persist($articeStocked);

                $this->em->flush();
            }

            $this->em->remove($invoice);
            $this->em->flush();
            $this->addFlash('success', 'Bon de livraison a été supprimé avec succès');
            return $this->redirectToRoute('perso_index_invoice');


        }


    }


}