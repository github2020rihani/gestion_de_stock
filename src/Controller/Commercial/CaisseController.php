<?php


namespace App\Controller\Commercial;


use App\Repository\DepenseRepository;
use App\Repository\PayemetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
    public function index(DepenseRepository $depenseRepository) {

        $totalCheque = 0;
        $totalEspese = 0;
        $data = [];

        $caisse = $this->payemetRepository->findAll();
        $perfix_invoice = $_ENV['PREFIX_FACT'];
        $perfix_depense = $_ENV['PREFIX_DEPENSE'];

        foreach ($caisse as$key=> $c) {

            $data[$key]['date'] = $c->getCreatedAt();
            $data[$key]['montant'] = $c->getTotalttc();
            $data[$key]['ncheque'] = $c->getNumeroCheque();
            $data[$key]['espece'] = $c->getMontant();
            $data[$key]['reste'] = $c->getReste();
            $data[$key]['retenu'] = $c->getRetenu();
            $data[$key] ['nomCustomer'] = $c->getCustomer();
            if ($c->getTypePayement() == '1') {
                $data[$key]['tp'] ='Espese';
            }else if ($c->getTypePayement() == '2') {
                $data[$key]['tp'] ='Cheque';
            }else{
                $data[$key]['tp'] ='Carte';
            }
            if ($c->getType() != 'Dépence') {
                $data[$key]['typeD'] = 'Facture';
                $data[$key]['num'] = $perfix_invoice.''.$c->getInvoice()->getId();



            }else{
                $data[$key]['typeD'] = 'Dépence';
                $data[$key]['num'] = $perfix_depense.''.$c->getDepense()->getId();


            }
        }

        return $this->render('commercial/caisse/index.html.twig', array('data' => $data));
    }



}