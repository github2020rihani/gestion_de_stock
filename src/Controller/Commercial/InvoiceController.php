<?php


namespace App\Controller\Commercial;


use App\Entity\BondLivraison;
use App\Entity\Invoice;
use App\Repository\BondLivraisonRepository;
use App\Repository\BonlivraisonArticleRepository;
use App\Repository\InvoiceRepository;
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

    public function __construct(EntityManagerInterface $em, InvoiceRepository $invoiceRepository,
                                BonlivraisonArticleRepository $bonlivraisonArticleRepository,
                                BondLivraisonRepository $bondLivraisonRepository)
    {
        $this->em = $em;
        $this->invoiceRepository = $invoiceRepository;
        $this->bondLivraisonRepository = $bondLivraisonRepository;
        $this->bonlivraisonArticleRepository = $bonlivraisonArticleRepository;

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
        dump($invoice);
        return $this->render('commercial/invoice/detail.html.twig', array('invoice' => $invoice, 'perfex_invoice' => $_ENV['PREFIX_FACT']));

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



}