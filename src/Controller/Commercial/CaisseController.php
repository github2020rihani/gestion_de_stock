<?php


namespace App\Controller\Commercial;


use App\Entity\History;
use App\Repository\DepenseRepository;
use App\Repository\PayemetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;

/**
 * Class CaisseController
 * @package App\Controller\Commercial
 * @Route("/personelle/caisse")
 */
class CaisseController extends AbstractController
{

    private $em;
    private $payemetRepository;


    public function __construct(EntityManagerInterface $em, PayemetRepository $payemetRepository)
    {
        $this->em = $em;
        $this->payemetRepository = $payemetRepository;

    }

    /**
     * @Route("/", name="perso_index_caisse")
     */
    public function index(DepenseRepository $depenseRepository)
    {

        $totalCheque = 0;
        $totalEspese = 0;
        $total_depense = 0;
        $total_caisse = 0;
        $total_avoir = 0;
        $totalFacture = 0;
        $totalReste = 0;

        $data = [];
        $date = new \DateTime();

        $caisse = $this->payemetRepository->getAllByDate($date->format('y-m-d'));
//        dd($caisse); die;
        $perfix_invoice = $_ENV['PREFIX_FACT'];
        $perfix_depense = $_ENV['PREFIX_DEPENSE'];
        $perfix_avoir = $_ENV['PREFIX_AVOIR'];

        foreach ($caisse as $key => $c) {
            $totalReste = $totalReste + ($c->getReste());

            $total_caisse = $total_caisse + $c->getMontant();
            $data[$key]['date'] = $c->getCreatedAt();
            $data[$key]['montant'] = $c->getTotalttc();
            $data[$key]['ncheque'] = $c->getNumeroCheque();
            $data[$key]['espece'] = $c->getMontant();
            $data[$key]['reste'] = $c->getReste();
            $data[$key]['retenu'] = $c->getRetenu();
            $data[$key] ['nomCustomer'] = $c->getCustomer();
            if ($c->getTypePayement() == '1') {
                $data[$key]['tp'] = 'Espèce';
                $totalEspese = $totalEspese + $c->getMontant();

            } else if ($c->getTypePayement() == '2') {
                $totalCheque = $totalCheque + $c->getMontant();
                $data[$key]['tp'] = 'Chéque';
            } else {
                $data[$key]['tp'] = 'Carte';
            }
            if ($c->getType() == 'Facture') {
                if ($c->getInvoice()->getExistBl() ) {
                    $data[$key]['typeD'] = 'BL / Facture';

                }else{
                    $data[$key]['typeD'] = 'Facture';

                }                $data[$key]['num'] = $perfix_invoice . '' . $c->getInvoice()->getId();
                $data[$key]['invoiceId'] =$c->getInvoice()->getId();
                $totalFacture = $totalFacture + $c->getMontant();

            } else if ($c->getType() == 'Dépence') {
                $data[$key]['typeD'] = 'Dépence';
                $data[$key]['num'] = $perfix_depense . '' . $c->getDepense()->getId();
                $total_depense = $total_depense + $c->getMontant();
            } else {
                $data[$key]['typeD'] = 'Avoir';
                $data[$key]['num'] = $perfix_avoir . '' . $c->getAvoir()->getId();
                $total_avoir = $total_avoir + $c->getMontant();
            }
        }
//
        return $this->render('commercial/caisse/index.html.twig'
//        return $this->render('commercial/caisse/_print_caisse.html.twig'
            , array(
                'data' => $data,
                'total_depense' => $total_depense,
                'totalEspese' => $totalEspese,
                'totalCheque' => $totalCheque,
                'totalAvoir' => $total_avoir,
                'totalReste' => $totalReste,
                'total_caisse' => $totalFacture - ($total_depense + $total_avoir)
            ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/change/caisse", name="change_caisse", options={"expose" = true})
     */
    public function changeCaise(Request $request)
    {

        $date = $request->get('date');
        $dateNew = new \DateTime($date);

        $totalCheque = 0;
        $totalEspese = 0;
        $total_depense = 0;
        $total_caisse = 0;
        $total_avoir = 0;
        $totalFacture = 0;
        $totalReste = 0;

        $data = [];

        $caisse = $this->payemetRepository->getAllByDate($dateNew->format('y-m-d'));
        $perfix_invoice = $_ENV['PREFIX_FACT'];
        $perfix_depense = $_ENV['PREFIX_DEPENSE'];
        $perfix_avoir = $_ENV['PREFIX_AVOIR'];
        $twig = '';
        $status = false;
        if ($caisse) {
            foreach ($caisse as $key => $c) {
                $totalReste = $totalReste + ($c->getReste());

                $total_caisse = $total_caisse + $c->getMontant();
                $data[$key]['date'] = $c->getCreatedAt();
                $data[$key]['montant'] = $c->getTotalttc();
                $data[$key]['ncheque'] = $c->getNumeroCheque();
                $data[$key]['espece'] = $c->getMontant();
                $data[$key]['reste'] = $c->getReste();
                $data[$key]['retenu'] = $c->getRetenu();
                $data[$key] ['nomCustomer'] = $c->getCustomer();
                if ($c->getTypePayement() == '1') {
                    $data[$key]['tp'] = 'Espèce';
                    $totalEspese = $totalEspese + $c->getMontant();

                } else if ($c->getTypePayement() == '2') {
                    $totalCheque = $totalCheque + $c->getMontant();
                    $data[$key]['tp'] = 'Chéque';
                } else {
                    $data[$key]['tp'] = 'Carte';
                }
                if ($c->getType() == 'Facture') {
                    if ($c->getInvoice()->getExistBl() ) {
                        $data[$key]['typeD'] = 'BL / Facture';

                    }else{
                        $data[$key]['typeD'] = 'Facture';

                    }
                    $data[$key]['num'] = $perfix_invoice . '' . $c->getInvoice()->getId();
                    $totalFacture = $totalFacture + $c->getMontant();
                    $data[$key]['invoiceId'] =$c->getInvoice()->getId();


                } else if ($c->getType() == 'Dépence') {
                    $data[$key]['typeD'] = 'Dépence';
                    $data[$key]['num'] = $perfix_depense . '' . $c->getDepense()->getId();
                    $total_depense = $total_depense + $c->getMontant();
                } else {
                    $data[$key]['typeD'] = 'Avoir';
                    $data[$key]['num'] = $perfix_avoir . '' . $c->getAvoir()->getId();
                    $total_avoir = $total_avoir + $c->getMontant();
                }
            }
            $status = true;

            $twig = $this->render('commercial/caisse/_content_caisse.html.twig'
                , array(
                    'data' => $data,
                    'total_depense' => $total_depense,
                    'totalEspese' => $totalEspese,
                    'totalCheque' => $totalCheque,
                    'totalAvoir' => $total_avoir,
                    'totalReste' => $totalReste,
                    'total_caisse' => $totalFacture - ($total_depense + $total_avoir)
                ))->getContent();
        }


        return $this->json(['status' => $status, 'twig' => $twig]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/print/caisse", name="print_caisse", options={"expose" = true})
     */
    public function printCaisse(Request $request)
    {

        $date = $request->get('date');
        $dateNew = new \DateTime($date);

        $totalCheque = 0;
        $totalEspese = 0;
        $total_depense = 0;
        $total_caisse = 0;
        $total_avoir = 0;
        $totalFacture = 0;

        $data = [];

        $caisse = $this->payemetRepository->getAllByDate($dateNew->format('y-m-d'));
        $perfix_invoice = $_ENV['PREFIX_FACT'];
        $perfix_depense = $_ENV['PREFIX_DEPENSE'];
        $perfix_avoir = $_ENV['PREFIX_AVOIR'];
        $twig = '';
        $status = false;
        if ($caisse) {
            foreach ($caisse as $key => $c) {

                $total_caisse = $total_caisse + $c->getMontant();
                $data[$key]['date'] = $c->getCreatedAt();
                $data[$key]['montant'] = $c->getTotalttc();
                $data[$key]['ncheque'] = $c->getNumeroCheque();
                $data[$key]['espece'] = $c->getMontant();
                $data[$key]['reste'] = $c->getReste();
                $data[$key]['retenu'] = $c->getRetenu();
                $data[$key] ['nomCustomer'] = $c->getCustomer();
                if ($c->getTypePayement() == '1') {
                    $data[$key]['tp'] = 'Espèce';
                    $totalEspese = $totalEspese + $c->getMontant();

                } else if ($c->getTypePayement() == '2') {
                    $totalCheque = $totalCheque + $c->getMontant();
                    $data[$key]['tp'] = 'Chéque';
                } else {
                    $data[$key]['tp'] = 'Carte';
                }
                if ($c->getType() == 'Facture') {
                    $data[$key]['typeD'] = 'Facture';
                    $data[$key]['num'] = $perfix_invoice . '' . $c->getInvoice()->getId();
                    $totalFacture = $totalFacture + $c->getMontant();

                } else if ($c->getType() == 'Dépence') {
                    $data[$key]['typeD'] = 'Dépence';
                    $data[$key]['num'] = $perfix_depense . '' . $c->getDepense()->getId();
                    $total_depense = $total_depense + $c->getMontant();
                } else {
                    $data[$key]['typeD'] = 'Avoir';
                    $data[$key]['num'] = $perfix_avoir . '' . $c->getAvoir()->getId();
                    $total_avoir = $total_avoir + $c->getMontant();
                }
            }
            $status = true;
            $uploadDir = $this->getParameter('uploads_directory');
            $pdfOptions = new Options();
            $pdfOptions->setDefaultFont('Courier');
            $pdfOptions->setIsRemoteEnabled(true);
            $dompdf = new Dompdf($pdfOptions);
            $html = $this->renderView('commercial/caisse/_print_caisse.html.twig', [
                'data' => $data,
                'total_depense' => $total_depense,
                'totalEspese' => $totalEspese,
                'totalCheque' => $totalCheque,
                'totalAvoir' => $total_avoir,
                'total_caisse' => $totalFacture - ($total_depense + $total_avoir)
            ]);
//            $html = $this->renderView('commercial/caisse/_print_caisse.html.twig', [
//                'data' => $data,
//                'total_depense' => $total_depense,
//                'totalEspese' => $totalEspese,
//                'totalCheque' => $totalCheque,
//                'totalAvoir' => $total_avoir,
//                'total_caisse' => $totalFacture - ($total_depense + $total_avoir)
//            ]);
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

            $mypath = $uploadDir . 'caisse';
            $newFilename = 'caisse_' . $date . '.pdf';
            $pdfFilepath = $uploadDir . 'caisse/' . $newFilename;
            if (!is_dir($mypath)) {
                mkdir($mypath, 0777, TRUE);

            }
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/caisse/' . $newFilename;
            file_put_contents($pdfFilepath, $output);
            $file_with_path = $uploadDir . strstr($baseurl, 'caisse');
            $response = new BinaryFileResponse ($file_with_path);
            $response->headers->set('Content-Type', 'application/pdf; charset=utf-8', 'application/force-download');
            $response->headers->set('Content-Disposition', 'attachment; filename=devis.pdf');

            $res = $response;
        } else {
            $res = 'error';

        }


        return $res;



    }

//    /**
//     * @return Response
//     * @Route("/test", name="test_snappy")
//     */
//
//    public function test()
//    {
//        $snappy = $this->get('knp_snappy.pdf');
//        $filename = 'myFirstSnappyPDF';
//        $url = 'http://ourcodeworld.com';
//
//
//        return new Response(
//            $snappy->getOutput($url),
//            200,
//            array(
//                'Content-Type'          => 'application/pdf',
//                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
//            )
//        );
//    }
//
//
    /**
     * @param Pdf $knpSnappyPdf
     * @return PdfResponse
     * @Route("/test2", name="test2_snappy")
     */
    public function pdfAction(Pdf $knpSnappyPdf)
    {

        $html = $this->renderView('commercial/caisse/_print_caisse.html.twig', array(
            'some'  => '$vars'
        ));


        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'file.pdf'
        );
    }
//
//    /**
//     * @param Pdf $knpSnappyPdf
//     * @return PdfResponse
//     * @Route("/test3", name="test3_snappy")
//     */
//
//    public function pdf3Action(Pdf $knpSnappyPdf)
//    {
//        $pageUrl = $this->generateUrl('test2_snappy', array(), true); // use absolute path!
//
//        return new PdfResponse(
//            $knpSnappyPdf->getOutput($pageUrl),
//            'file.pdf'
//        );
//    }


}