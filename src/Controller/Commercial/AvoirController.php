<?php


namespace App\Controller\Commercial;


use App\Entity\ArticleAvoir;
use App\Entity\Avoir;
use App\Entity\History;
use App\Entity\Payemet;
use App\Repository\ArticleAvoirRepository;
use App\Repository\ArticleRepository;
use App\Repository\AvoirRepository;
use App\Repository\InvoiceRepository;
use App\Repository\PayemetRepository;
use App\Repository\PrixRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AvoirController
 * @package App\Controller\Commercial
 * @Route("/personelle/avoirs")
 */
class AvoirController extends AbstractController
{
    private $em;
    private $avoirRepository;
    private $invoiceRepository;


    public function __construct(EntityManagerInterface $em, AvoirRepository $avoirRepository, InvoiceRepository $invoiceRepository)
    {
        $this->em = $em;
        $this->avoirRepository = $avoirRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/" , name="index_avoir")
     */
    public function index()
    {
        $avoirs = $this->avoirRepository->findAll();
        //dd($avoirs);
        return $this->render('commercial/avoir/index.html.twig', array('avoirs' => $avoirs,
            'PREFIX_AVOIR' => $_ENV['PREFIX_AVOIR'],
            'PREFIX_FACT' => $_ENV['PREFIX_FACT']
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/add" , name="add_avoir")
     */
    public function add(Request $request, ArticleRepository $articleRepository, PrixRepository $prixRepository, StockRepository $stockRepository)
    {
        $invoices = $this->invoiceRepository->findBy(array('status' => [2, 3]));
        $tva = $_ENV['TVA_ARTICLE_PERCENT'];
        $tva_percent = $_ENV['TVA_ARTICLE'];
        $timbre = $_ENV['TIMBRE'];
        $year = date('Y');
        $totalHt = 0;
        $totalRemise = 0;
        $totalttcGlobal = 0;
        $total = 0;
        $lastAvoir = $this->avoirRepository->getLastAvoirWithCurrentYear($year);

        if ($lastAvoir) {
            $lastId = 000 + $lastAvoir->getId() + 1;
            $numero_avoir = '000' . $lastId;
        } else {
            $numero_avoir = '0001';
        }

        if ($request->isMethod('post')) {
            $id_articles = $request->get('article');
            $qte_article = $request->get('qte');
            $type_payement = $request->get('typePayement');
            $invoice = $this->invoiceRepository->find($request->request->get('customers'));

            //save avoir
            $avoirObj = new Avoir();
            $avoirObj->setAddedBy($this->getUser());
            $avoirObj->setYear($year);
            $avoirObj->setNumero($numero_avoir);
            $avoirObj->setInvoice($invoice);
            $avoirObj->setTotalttc($total);
            $avoirObj->setTimbre($timbre);
            $avoirObj->setTypePayement($type_payement);
            if ($invoice) {
                $blExiste = $invoice->getBonLivraison();
                if ($blExiste) {
                    $customer = $invoice->getBonLivraison()->getCustomer();
                    $avoirObj->setCustomer($customer);
                } else {
                    $customer = $invoice->getCustomer();
                    $avoirObj->setCustomer($customer);
                }
            }
            $this->em->persist($avoirObj);
            $this->em->flush();

            //save articles avoir
            foreach ($id_articles as $key => $id_art) {
                $articleExiste = $prixRepository->getArticleById($id_art);
                $totalHtaricle = (float)round($articleExiste[0]['puVenteHT'] * $qte_article[$key]);
                $puttcArticle = (float)round($articleExiste[0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE']);
                $totalttcArticle = round((float)$puttcArticle * $qte_article[$key]);
                $totalHt = round($totalHt + (float)$totalHtaricle);
                $totalRemise = round($totalRemise + (float)$articleExiste[0]['article']['remise']);


                //save article avoir
                $articleAvoir = new ArticleAvoir();
                $articleAvoir->setAvoir($avoirObj);
                $articleAvoir->setArticle($articleRepository->find($articleExiste[0]['article']['id']));
                $articleAvoir->setQte($qte_article[$key]);
                $articleAvoir->setPuht($articleExiste[0]['puVenteHT']);
                $articleAvoir->setPuhtnet($articleExiste[0]['puVenteHT']);
                $articleAvoir->setRemise($articleExiste[0]['article']['remise']);
                $articleAvoir->setTaxe($articleExiste[0]['tva']);
                $articleAvoir->setTotalht((float)(($totalHtaricle)));
                $articleAvoir->setPuttc((float)(($puttcArticle)));
                $articleAvoir->setTotalttc((float)(($totalttcArticle)));
                $this->em->persist($articleAvoir);
                $this->em->flush();



                //update stocked
//                $lingArtPr = $prixRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
//                $newQte = abs((int)$lingArtPr->getQte() + (int)$qte_article[$key]);
//                $lingArtPr->setQte($newQte);
//                $this->em->persist($lingArtPr);
//
//                $lingeArtStock = $stockRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
//                $lingeArtStock->setQte($newQte);
//                $this->em->persist($lingeArtStock);
//                $this->em->flush();


            }
            $avoirObj->setTotalHT((float)(($totalHt)));
            $avoirObj->setTotalRemise((float)(($totalRemise)));
            $avoirObj->setTotalTva($_ENV['TVA_ARTICLE_PERCENT'] / 100);
            $avoirObj->setTotalTTC((float)(($totalHt + 0.19 + 0.600)));
            $this->em->persist($avoirObj);
            $this->em->flush();






            $this->addFlash('success', 'Ajoute a été effectuer avec succès');
            return $this->redirectToRoute('index_avoir');


        }
        return $this->render('commercial/avoir/add.html.twig', array('invoices' => $invoices, 'PREFIX_FACT' => $_ENV['PREFIX_FACT'] ));
    }

    /**
     * @param Request $request
     * @Route("/edit/{id}" , name="edit_avoir")
     */
    public function edit(Request $request, Avoir $id,
                         ArticleRepository $articleRepository,
                         PrixRepository $prixRepository,
                         StockRepository $stockRepository,
                         ArticleAvoirRepository $articleAvoirRepository)
    {
        $invoices = $this->invoiceRepository->findBy(array('status' => [2, 3]));
        $avoirObj = $this->avoirRepository->find($id);
        $articless = $articleRepository->findAll();
        $total = 0;
        $tva = $_ENV['TVA_ARTICLE_PERCENT'];
        $tva_percent = $_ENV['TVA_ARTICLE'];
        $timbre = $_ENV['TIMBRE'];
        $year = date('Y');
        $totalHt = 0;
        $totalRemise = 0;
        $totalttcGlobal = 0;
        $total = 0;
        if ($request->isMethod('post')) {

            //delete all articles avoir precedent
            $articleAvoirs = $articleAvoirRepository->findBy(array('avoir' => $avoirObj->getId()));
            foreach ($articleAvoirs as $key => $value) {
                $this->em->remove($articleAvoirRepository->find($value));
                $this->em->flush();
            }
            $invoice = $this->invoiceRepository->find($request->request->get('customers'));
            $id_articles = $request->get('article');
            $qte_article = $request->get('qte');
            $type_payement = $request->get('typePayement');
            $year = date('Y');
            $lastAvoir = $this->avoirRepository->getLastAvoirWithCurrentYear($year);

            if ($lastAvoir) {
                $lastId = 000 + $lastAvoir->getId() + 1;
                $numero_avoir = '000' . $lastId;
            } else {
                $numero_avoir = '0001';
            }
            //update avoir
            $avoirObj->setAddedBy($this->getUser());
            $avoirObj->setYear($year);
            $avoirObj->setNumero($numero_avoir);
            $avoirObj->setInvoice($invoice);
            $avoirObj->setTotalttc($total);
            $avoirObj->setTimbre($timbre);
            $avoirObj->setTypePayement($type_payement);
            if ($invoice) {
                $blExiste = $invoice->getBonLivraison();
                if ($blExiste) {
                    $customer = $invoice->getBonLivraison()->getCustomer();
                    $avoirObj->setCustomer($customer);
                } else {
                    $customer = $invoice->getCustomer();
                    $avoirObj->setCustomer($customer);
                }
            }
            $this->em->persist($avoirObj);
            $this->em->flush();
            //save articles avoir
            foreach ($id_articles as $key => $id_art) {
                $articleExiste = $prixRepository->getArticleById($id_art);
                $totalHtaricle = (float)round($articleExiste[0]['puVenteHT'] * $qte_article[$key]);
                $puttcArticle = (float)round($articleExiste[0]['puVenteHT'] * (float)$_ENV['TVA_ARTICLE']);
                $totalttcArticle = round((float)$puttcArticle * $qte_article[$key]);
                $totalHt = round($totalHt + (float)$totalHtaricle);
                $totalRemise = round($totalRemise + (float)$articleExiste[0]['article']['remise']);


                //save article avoir
                $articleAvoir = new ArticleAvoir();
                $articleAvoir->setAvoir($avoirObj);
                $articleAvoir->setArticle($articleRepository->find($articleExiste[0]['article']['id']));
                $articleAvoir->setQte($qte_article[$key]);
                $articleAvoir->setPuht($articleExiste[0]['puVenteHT']);
                $articleAvoir->setPuhtnet($articleExiste[0]['puVenteHT']);
                $articleAvoir->setRemise($articleExiste[0]['article']['remise']);
                $articleAvoir->setTaxe($articleExiste[0]['tva']);
                $articleAvoir->setTotalht((float)(($totalHtaricle)));
                $articleAvoir->setPuttc((float)(($puttcArticle)));
                $articleAvoir->setTotalttc((float)(($totalttcArticle)));
                $this->em->persist($articleAvoir);
                $this->em->flush();



                //update stocked
//                $lingArtPr = $prixRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
//                $newQte = abs((int)$lingArtPr->getQte() + (int)$qte_article[$key]);
//                $lingArtPr->setQte($newQte);
//                $this->em->persist($lingArtPr);
//
//                $lingeArtStock = $stockRepository->findOneBy(array('article' => $articleExiste[0]['article']['id']));
//                $lingeArtStock->setQte($newQte);
//                $this->em->persist($lingeArtStock);
//                $this->em->flush();


            }
            $avoirObj->setTotalHT((float)(($totalHt)));
            $avoirObj->setTotalRemise((float)(($totalRemise)));
            $avoirObj->setTotalTva($_ENV['TVA_ARTICLE_PERCENT'] / 100);
            $avoirObj->setTotalTTC((float)(($totalHt + 0.19 + 0.600)));
            $this->em->persist($avoirObj);
            $this->em->flush();



            $this->addFlash('success', 'Avoir a été modifier');
            return $this->redirectToRoute('index_avoir');


        }
        return $this->render('commercial/avoir/edit.html.twig', array(
            'invoices' => $invoices,
            'a' => $id,
            'articles' => $articless,
            'PREFIX_FACT' => $_ENV['PREFIX_FACT']));

    }


    /**
     * @param PayemetRepository $payemetRepository
     * @param AvoirRepository $avoirRepository
     * @param ArticleAvoirRepository $articleAvoirRepository
     * @param Avoir $avoir
     * @Route("/rembourser/{id}" , name="rembourser_avoir")
     */
    public function rembourserAvoir(PayemetRepository $payemetRepository, StockRepository $stockRepository, Request $request,
                                    AvoirRepository $avoirRepository ,PrixRepository $prixRepository ,ArticleAvoirRepository $articleAvoirRepository, Avoir $avoir) {

        $av = $avoirRepository->find($avoir);

        $av->setStatus(1);
        $this->em->persist($av);
        $this->em->flush();
        //change stock
        foreach ($av->getArticleAvoirs() as $key => $value) {
            $lingArtPr[$key] = $prixRepository->findOneBy(array('article' =>$value->getArticle()->getId()));
            $newQte[$key] = abs((int)$lingArtPr[$key]->getQte() + (int)$value->getQte());
            $lingArtPr[$key]->setQte($newQte[$key]);
            $this->em->persist($lingArtPr[$key]);

            $lingeArtStock[$key] = $stockRepository->findOneBy(array('article' => $value->getArticle()->getId()));
            $lingeArtStock[$key]->setQte($newQte[$key]);
            $this->em->persist($lingeArtStock[$key]);
            $this->em->flush();

        }

        //save in payement
        $payment = new Payemet();
        $payment->setAddedBy($this->getUser());
        $payment->setMontant($av->getTotalttc());
        $payment->setTotalttc($av->getTotalttc());
        $payment->setReste(0.000);
        $payment->setRetenu(0.000);
        $payment->setType('Avoir');
        $payment->setTypePayement($av->getTypePayement());
        $payment->setCreatedAt(new \DateTime());
        $payment->setAvoir($av);
        $payment->setCustomer($av->getCustomer()->getNom());
        $this->em->persist($payment);
        $this->em->flush();

        $invoice = $this->invoiceRepository->find($av->getInvoice()->getId());
        $invoice->setAvoir(true);
        $this->em->persist($invoice);
        $this->em->flush();

        //generate avoir to pdf
        self::generateAvoir($av->getId(), $request, $av);

        $this->addFlash('success', 'Avoir a été rembourser');
        return $this->redirectToRoute('index_avoir');




    }




    public function generateAvoir($id_av, $request, $av)
    {

//        $dataInvoice = $this->invoiceRepository->getDataInvoiceSeul($id_invoice);
        $uploadDir = $this->getParameter('uploads_directory');
        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Courier');
        $pdfOptions->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($pdfOptions);
//        $html = $this->renderView('commercial/invoice/printInvoice.html.twig', [

        $html = $this->renderView('commercial/avoir/printAvoir.html.twig', [
            'av' => $av,
            'PREFIX_AVOIR' => $_ENV['PREFIX_AVOIR'],
            'PREFIX_FACT' => $_ENV['PREFIX_FACT']
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
        $codeClient = $av->getCustomer()->getCode();
        $yearBl = $av->getYear();
        $numBl = $av->getNumero();
        $date = new \DateTime();

        $mypath = $uploadDir . 'avoirs/customer_' . $codeClient;
        $newFilename = 'invoice_' . $numBl . '_' . $yearBl.'_'.$date->getTimestamp() . '.pdf';
        $pdfFilepath = $uploadDir . 'avoirs/customer_' . $codeClient . '/' . $newFilename;
        if (!is_dir($mypath)) {
            mkdir($mypath, 0777, TRUE);

        }
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/avoirs/customer_' . $codeClient . '/' . $newFilename;


        file_put_contents($pdfFilepath, $output);
//        $history = new History();
//        $history->setType('Avoir');
//        $history->setFile($baseurl);
//        $history->setCreatedBy($this->getUser());
//        $history->setInvoice($invoice);
//        $this->em->persist($history);
//        $this->em->flush();


        $av->setFile($baseurl);
        $this->em->persist($av);
        //setfile avoir invoice

        $invoice = $this->invoiceRepository->find($av->getInvoice()->getId());
        $invoice->setFileAvoir($baseurl);
        $this->em->persist($invoice);
        $this->em->flush();


    }







}