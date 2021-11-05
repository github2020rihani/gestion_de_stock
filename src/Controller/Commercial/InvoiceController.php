<?php


namespace App\Controller\Commercial;


use App\Entity\BondLivraison;
use App\Entity\Invoice;
use App\Repository\BondLivraisonRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("personelle/invoices")
 */
class InvoiceController extends AbstractController
{

    private $em;
    private $invoiceRepository;
    private $bondLivraisonRepository;

    public function __construct(EntityManagerInterface $em, InvoiceRepository $invoiceRepository, BondLivraisonRepository $bondLivraisonRepository)
    {
        $this->em = $em;
        $this->invoiceRepository = $invoiceRepository;
        $this->bondLivraisonRepository = $bondLivraisonRepository;

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="perso_index_invoice")
     */
    public function index()
    {
        $invoices = $this->invoiceRepository->findAll();

        return $this->render('commercial/invoice/index.html.twig', array('invoices' => $invoices));

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/detail/{id}", name="perso_detail_invoice")
     */
    public function detail(Invoice $invoice)
    {
        $invoices = $this->invoiceRepository->find($invoice);

        return $this->render('commercial/invoice/detail.html.twig', array('invoice' => $invoice));

    }



}