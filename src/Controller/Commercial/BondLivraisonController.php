<?php


namespace App\Controller\Commercial;


use App\Entity\BondLivraison;
use App\Entity\BonlivraisonArticle;
use App\Entity\Devis;
use App\Entity\History;
use App\Entity\Invoice;
use App\Repository\ArticleRepository;
use App\Repository\BondLivraisonRepository;
use App\Repository\BonlivraisonArticleRepository;
use App\Repository\ClientRepository;
use App\Repository\DevisArticleRepository;
use App\Repository\DevisRepository;
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
    private $stockRepository;


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
                                StockRepository $stockRepository,
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
        $this->stockRepository = $stockRepository;
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
            $totalttcGlobal = $totalttcGlobal + (float)$totalHt + 0.19 + 0.600;

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
        $bls = $this->bondLivraisonRepository->findBy(array (),array('id' => 'desc'));
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
        $totalRemise = 0;

        if ($request->isMethod('POST')) {
            //gt type payement and id articles and qte of articles
            $id_articles = $request->get('article');
            $qte_article = $request->get('qte');
            $remise_article = $request->get('remise');
            $id_customer = $request->get('customers');
            $type_payement = $request->get('typePayement');
            $customer = $this->clientRepository->find($id_customer);
           // dd(($remise_article));
            //save new Bl
            $bl = new BondLivraison();
            $bl->setCustomer($customer);
            $bl->setNumero($numero_bl);
            $bl->setYear($year);
            $bl->setCreatedBy($this->getUser());
            $bl->setExistDevi(false);
            $bl->setStatus(0);
//            $bl->setTypePayement($type_payement);
            $this->em->persist($bl);
            $this->em->flush();


            foreach ($id_articles as $key => $id_art) {
                $articleExiste = $this->prixRepository->getArticleById($id_art);
                $totalHtaricle = (float)round($articleExiste[0]['puVenteHT'] * $qte_article[$key]);
                $puttcArticle = (float)round($articleExiste[0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE']);
                $totalttcArticle = round((float)($puttcArticle * $qte_article[$key]));
                $totalHt = round($totalHt + (float)$totalHtaricle);
                $totalRemise = round($totalRemise + (float)$articleExiste[0]['article']['remise']);


                //save article bl
                $article_bl = new BonlivraisonArticle();
                $article_bl->setBonLivraison($bl);
                $article_bl->setArticle($this->articleRepository->find($articleExiste[0]['article']['id']));
                $article_bl->setQte($qte_article[$key]);
                $article_bl->setPuht($articleExiste[0]['puVenteHT']);
                $article_bl->setPuhtnet($articleExiste[0]['puVenteHT']);
                $article_bl->setRemise($articleExiste[0]['article']['remise']);
                $article_bl->setTaxe($articleExiste[0]['tva']);
                $article_bl->setRemise($remise_article[$key]);
                $article_bl->setTotalht((float)(($totalHtaricle)));
                $article_bl->setPuttc((float)(($puttcArticle)));
                $article_bl->setTotalttc((float)(($totalttcArticle)));
                $this->em->persist($article_bl);
                $this->em->flush();

                //change stock
                $Lingart = $this->articleRepository->find($articleExiste[0]['article']['id']);
                $Lingart->setQteReserved((int)$Lingart->getQteReserved() + (int)$qte_article[$key]);
                $this->em->persist($Lingart);
                //update stocked
                $lingArtPr = $this->prixRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
                $newQte = abs((int)$lingArtPr->getQte() - (int)$qte_article[$key]);
                $lingArtPr->setQte($newQte);
                $this->em->persist($lingArtPr);

                $lingeArtStock = $this->stockRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
                $lingeArtStock->setQte($newQte);
                $this->em->persist($lingeArtStock);
                $this->em->flush();
            }
            $totalttcGlobal = $totalttcGlobal + (float)$totalHt;

            if ($remise_article) {
                $bl->setRemise((float)array_sum($remise_article));
                    $tht= (float)((float) $totalHt - (((float)$totalHt * (int) array_sum($remise_article)) / 100)) ;
                $bl->setTotalHT($tht);

            }else{
                $tht =(float)(($totalHt));
                $bl->setTotalHT($tht);

            }
            $bl->setTotalRemise((float)(($totalRemise)));
            $bl->setTotalTVA($_ENV['TVA_ARTICLE_PERCENT'] / 100);
            $bl->setTotalTTC((float)(($tht + 0.19)));
            $this->em->persist($bl);
            $this->em->flush();
            //updatestock after save new article
            $Lingart = $this->articleRepository->find($articleExiste[0]['article']['id']);
            $Lingart->setQteReserved((int)$Lingart->getQteReserved() + (int)$qte_article[$key]);
            $this->em->persist($Lingart);
            //update stocked
            $lingArtPr = $this->prixRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
            $newQte = abs((int)$lingArtPr->getQte() - (int)$qte_article[$key]);
            $lingArtPr->setQte($newQte);
            $this->em->persist($lingArtPr);

            $lingeArtStock = $this->stockRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
            $lingeArtStock->setQte($newQte);
            $this->em->persist($lingeArtStock);
            $this->em->flush();


            self::generateBl($bl->getId(), $request, $bl);

            $this->addFlash('success', 'Ajout effectué avec succés');
            return $this->redirectToRoute('perso_detail_bl', array('id' =>$bl->getId() ));


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
        $articles = $this->prixRepository->getArticleWithPrixInStocked();
        $tva = $_ENV['TVA_ARTICLE_PERCENT'];
        $totalHt = 0;
        $totalRemise = 0;
        $totalttcGlobal = 0;
        if ($request->isMethod('POST')) {
            try {
                $typepayement = $request->get('typePayement');
                $articles_selected = $request->get('article');
                $qte_articles = $request->get('qte');
                $remise_article = $request->get('remise');

                $customer_id = $request->get('customers');
                $customer = $this->clientRepository->find($customer_id);
                //chek if exist invoice
                $invoice = $this->invoiceRepository->findInvoiceByIdBl($id->getId());
                if ($invoice) {
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
                        //change stock
                        $Lingart = $this->articleRepository->find($value->getArticle()->getId());
                        $lingArtPr = $this->prixRepository->findOneBy(array('article' => $value->getArticle()->getId()));
                        $lingeArtStock = $this->stockRepository->findOneBy(array('article' => $value->getArticle()->getId()));

                        $sourceQte = (int)$lingArtPr->getQte() + (int)$value->getQte();
                        $restQteReserved = abs((int)$Lingart->getQteReserved() - (int)$value->getQte());
                        $Lingart->setQteReserved($restQteReserved);
                        $this->em->persist($Lingart);
                        //update stocked
                        $lingArtPr->setQte($sourceQte);
                        $this->em->persist($lingArtPr);
                        $lingeArtStock->setQte($sourceQte);
                        $this->em->persist($lingeArtStock);
                        $this->em->flush();


                        $this->em->remove($value);
                        $this->em->flush();
                    }
                }

                //update articles
                foreach ($articles_selected as $key => $value) {

                    $prixArticle = $this->prixRepository->getArticleById($value);
                    $totalHtaricle = (((float)round($prixArticle[0]['puVenteHT'] * $qte_articles[$key])));
                    $puttcArticle = ((float)round($prixArticle[0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE']));
                    $totalttcArticle = round((float)$puttcArticle * $qte_articles[$key]);
                    $totalHt = round($totalHt + (float)$totalHtaricle);
                    $totalRemise = round($totalRemise + (float)$prixArticle[0]['article']['remise']);


                    try {
                        $articleBL = new BonlivraisonArticle();
                        $articleBL->setBonLivraison($id);
                        $articleBL->setArticle($this->articleRepository->find($value));

                        $articleBL->setQte($qte_articles[$key]);
                        $articleBL->setRemise($remise_article[$key]);
                        $articleBL->setPuht($prixArticle[0]['puVenteHT']);
                        $articleBL->setPuhtnet($prixArticle[0]['puVenteHT']);
                        $articleBL->setRemise($prixArticle[0]['article']['remise']);
                        $articleBL->setTaxe($prixArticle[0]['tva']);
                        $articleBL->setTotalht((float)(($totalHtaricle)));
                        $articleBL->setPuttc((float)(($puttcArticle)));
                        $articleBL->setTotalttc((float)(($totalttcArticle)));
                        $this->em->persist($articleBL);
                        $this->em->flush();


                        $Lingart = $this->articleRepository->find($value);
                        $Lingart->setQteReserved((int)$Lingart->getQteReserved() + (int)$qte_articles[$key]);
                        $this->em->persist($Lingart);

                        $lingArtPr = $this->prixRepository->findOneBy(array('article' => $value));
                        $newQte = abs((int)$lingArtPr->getQte() - (int)$qte_articles[$key]);
                        $lingArtPr->setQte($newQte);
                        $this->em->persist($lingArtPr);

                        $lingeArtStock = $this->stockRepository->findOneBy(array('article' => $value));
                        $lingeArtStock->setQte($newQte);
                        $this->em->persist($lingeArtStock);
                        $this->em->flush();


                    } catch (\Exception $e) {
                        $this->addFlash('error', $e->getCode() . ':' . $e->getMessage() . '' . $e->getFile() . '' . $e->getLine());
                        return $this->redirectToRoute('perso_index_bl');
                    }


                }
                $totalttcGlobal = $totalttcGlobal + (float)$totalHt;
                if ($remise_article) {
                    $id->setRemise((float)array_sum($remise_article));
                    $tht= (float)((float) $totalHt - (((float)$totalHt * (int) array_sum($remise_article)) / 100)) ;
                    $id->setTotalHT($tht);

                }else{
                    $tht =(float)(($totalHt));
                    $id->setTotalHT($tht);

                }
                $id->setTotalRemise((float)(($totalRemise)));
                $id->setTotalTVA($_ENV['TVA_ARTICLE_PERCENT'] / 100);
                $id->setTotalTTC((float)(($tht + 0.19)));
                $id->setStatus(0);
                $this->em->persist($id);
                $this->em->flush();
//                dd($id);
                $bondL = $this->bondLivraisonRepository->find($id->getId());
//                dd($bondL);
                self::generateBl($id->getId(), $request, $id);

                $this->addFlash('success', 'Bon de livraison a été modifié avec succès');
                return $this->redirectToRoute('perso_index_bl');


            } catch (\Exception $e) {
                $this->addFlash('error', $e->getCode() . ':' . $e->getMessage() . '' . $e->getFile() . '' . $e->getLine());
                return $this->redirectToRoute('perso_index_bl');


            }
        }
        return $this->render('commercial/bondLivraison/edit.html.twig',
            array(
                'customers' => $customers,
                'bl' => $id,
                'tva' => $tva,
                'articles' => $articles
            ));

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
        $newQte = 0;
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
                $totalHtaricle = round((float)$devArticle['article']['prixes'][0]['puVenteHT'] * $devArticle['qte'], 3);
                $puttcArticle = round((float)$devArticle['article']['prixes'][0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE'], 3);
                $totalttcArticle = round((float)$puttcArticle * $devArticle['qte'], 3);
                $totalHt = round($totalHt + (float)$totalHtaricle);
                $totalRemise = round($totalRemise + (float)$devArticle['article']['remise']);


                //save article bl
                $article_bl = new BonlivraisonArticle();
                $article_bl->setBonLivraison($bl);
                $article_bl->setArticle($this->articleRepository->find($devArticle['article']['id']));
                $article_bl->setQte($devArticle['qte']);
                $article_bl->setPuht($devArticle['article']['prixes'][0]['puVenteHT']);
                $article_bl->setPuhtnet($devArticle['article']['prixes'][0]['puVenteHT']);
                $article_bl->setRemise($devArticle['article']['remise']);
                $article_bl->setTaxe($_ENV['TVA_ARTICLE_PERCENT']);
                $article_bl->setTotalht((float)(($totalHtaricle)));
                $article_bl->setPuttc((float)(($puttcArticle)));
                $article_bl->setTotalttc((float)(($totalttcArticle)));
                $this->em->persist($article_bl);
                $this->em->flush();
                //update stocked

                $Lingart = $this->articleRepository->find($devArticle['article']['id']);
                $Lingart->setQteReserved((int)$Lingart->getQteReserved() + (int)$devArticle['qte']);
                $this->em->persist($Lingart);

                $lingArtPr = $this->prixRepository->findOneBy(array('article' => $devArticle['article']['id']));
                $newQte = abs((int)$lingArtPr->getQte() - (int)$devArticle['qte']);
                $lingArtPr->setQte($newQte);
                $this->em->persist($lingArtPr);

                $lingeArtStock = $this->stockRepository->findOneBy(array('article' => $devArticle['article']['id']));
                $lingeArtStock->setQte($newQte);
                $this->em->persist($lingeArtStock);
                $this->em->flush();

                //update devis en cours
                $articleDevis = $this->devisArticleRepository->findBy(array('article' => $devArticle['article']['id']));
                if ($articleDevis) {
                    foreach ($articleDevis as $key => $art) {
                        $d = $this->devisRepository->findBy(array('id' => $art->getDevi()->getId(), 'status' => 0));
                        if ($d && $d[0]) {
                            $d[0]->setStatusMaj(true);
                            $this->em->persist($d[0]);
                            $this->em->flush();
                        }

                    }
                }

            }

            $totalttcGlobal = ((float)$totalHt + 0.19);


            $bl->setTotalHT((float)(($totalHt)));
            $bl->setTotalRemise((float)(($totalRemise)));
            $bl->setTotalTVA($_ENV['TVA_ARTICLE_PERCENT'] / 100);
            $bl->setTotalTTC((float)(($totalHt + 0.19)));
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
            $invoice->setTotalTTC((float)(($totalInvoice)));
            $invoice->setTimbre($_ENV['TIMBRE']);
            $invoice->setCreadetBy($this->getUser());
            $this->em->persist($invoice);
            $this->em->flush();

            //update status devis
            $updateDevis = $this->devisRepository->find($devis[0]['id']);
            $updateDevis->setStatus(1);
            $updateDevis->setStatusMaj(false);
            $this->em->persist($updateDevis);
            $this->em->flush();

            //generate bl
            self::generateBl($bl->getId(), $request, $bl);


            $message = 'Bon de livraison et une facture a été enregistrer';
            $status = true;
            $bl = $bl->getId();


        } else {
            $message = 'Aucun devi a été trouver';
            $status = false;
        }

        return $this->json(array('status' => $status, 'message' => $message, 'idBl' => $bl));
    }

    public function generateBl($bl, $request, $b)
    {
        $bondl = $this->bondLivraisonRepository->getDataBl($bl);
        $uploadDir = $this->getParameter('uploads_directory');
        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Courier');
        $pdfOptions->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('commercial/bondLivraison/printBlFromDevis.html.twig', [
            'bl' => $bondl[0]
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
        $codeClient = $bondl[0]['customer']['code'];
        $numBl = $bondl[0]['numero'];
        $yearBl = $bondl[0]['year'];

        $date = new \DateTime();

        $mypath = $uploadDir . 'bl/customer_' . $codeClient;
        $newFilename = 'BL_Num_' . $numBl . '_' . $yearBl . '_' . $date->getTimestamp() . '.pdf';
        $pdfFilepath = $uploadDir . 'bl/customer_' . $codeClient . '/' . $newFilename;
        if (!is_dir($mypath)) {
            mkdir($mypath, 0777, TRUE);

        }
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/bl/customer_' . $codeClient . '/' . $newFilename;

//        if (file_exists($pdfFilepath)) {
//            unlink($pdfFilepath);
//        }
        file_put_contents($pdfFilepath, $output);

        $history = new History();
        $history->setType('BL');
        $history->setFile($baseurl);
        $history->setCreatedBy($this->getUser());
        $history->setBl($b);
        $this->em->persist($history);
        $this->em->flush();

        $b->setFile($baseurl);
        $this->em->persist($b);
        $this->em->flush();


    }

    public function generateInvoice($id_invoice, $request, $invoice)
    {
        $dataInvoice = $this->invoiceRepository->getDataInvoice($id_invoice);
        $uploadDir = $this->getParameter('uploads_directory');
        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Courier');
        $pdfOptions->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('commercial/invoice/printInvoiceFromBl.html.twig', [
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
        $codeClient = $invoice->getBonLivraison()->getCustomer()->getCode();
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
     * @param BondLivraison $bl
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/transfert/bl/to/invoice/{bl}", name="perso_transfert_bl_to_invoince")
     */

    public function transfertBLToInvoice(Request $request, BondLivraison $bl)
    {
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
            $invoice->setRemise($bl->getRemise());
            $invoice->setStatus(1);
            $invoice->setNumero($numero_invoice);
            $invoice->setTotalTTC((float)(($totalInvoice)));
            $invoice->setTimbre($_ENV['TIMBRE']);
            $invoice->setCreadetBy($this->getUser());
            $invoice->setExistBl(1);
            $this->em->persist($invoice);
            $bl->setStatus(1);
            $this->em->persist($bl);
            $this->em->flush();
            //generate Invoice
            self::generateInvoice($invoice->getId(), $request, $invoice);
            $this->addFlash('success', 'Bon de livraison a été transféré avec succès');
            return $this->redirectToRoute('perso_index_invoice');


        } catch (\Exception $e) {
            $this->addFlash('error', $e->getCode() . ':' . $e->getMessage() . '' . $e->getFile() . '' . $e->getLine());
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


    /**
     * @param Request $request
     * @Route("api/print/BL", name="perso_print_bl" , options={"expose" = true})
     */
    public function printBL(Request $request)
    {
        $id_bl = $request->get('id_bl');
        $bl = $this->bondLivraisonRepository->find($id_bl);
        $uploadDir = $this->getParameter('uploads_directory');
        if ($bl->getFile()) {
            $success = true;
            $message = '';
            $file_with_path = $uploadDir . strstr($bl->getFile(), 'bl');
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
     * @param BondLivraison $id
     * @Route("/delete/{id}", name="perso_delete_bl")
     */
    public function delete(BondLivraison $id)
    {

        $bl = $this->bondLivraisonRepository->find($id);

        if ($bl) {


            foreach ($bl->getBonlivraisonArticles() as $key => $value) {

                $article = $this->articleRepository->find($value->getArticle()->getId());
                $articlePrix = $this->prixRepository->findOneBy(array('article' => $value->getArticle()->getId()));
                $articeStocked = $this->stockRepository->findOneBy(array('article' => $value->getArticle()->getId()));

                $sourceQte = (int)$articlePrix->getQte() + (int)$value->getQte();
                $restQteReserved = abs((int)$article->getQteReserved() - (int)$value->getQte());
                $article->setQteReserved($restQteReserved);
                $this->em->persist($article);
                //update stocked
                $newQte = $sourceQte;
                $articlePrix->setQte($newQte);
                $this->em->persist($articlePrix);

                $articeStocked->setQte($newQte);
                $this->em->persist($articeStocked);

                $this->em->flush();
            }

            if ($id->getDevi()) {
                $devis = $this->devisRepository->find($id->getDevi()->getId());
                if ($devis) {
                    $this->em->remove($devis);
                    $this->em->flush();
                }
            }


            $invoice = $this->invoiceRepository->findInvoiceByIdBl($id->getId());
            if ($invoice) {
                $this->em->remove($invoice);
                $this->em->flush();
            }

            $this->em->remove($bl);
            $this->em->flush();
            $this->addFlash('success', 'Bon de livraison a été supprimé avec succès');
            return $this->redirectToRoute('perso_index_bl');


        }


    }


}