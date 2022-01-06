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
        $total_depense = 0 ;
        $total_caisse = 0 ;
        $total_avoir = 0 ;

        $data = [];

        $caisse = $this->payemetRepository->findAll();
//        dd($caisse); die;
        $perfix_invoice = $_ENV['PREFIX_FACT'];
        $perfix_depense = $_ENV['PREFIX_DEPENSE'];
        $perfix_avoir = $_ENV['PREFIX_AVOIR'];

        foreach ($caisse as$key=> $c) {

            $total_caisse = $total_caisse + $c->getMontant();
            $data[$key]['date'] = $c->getCreatedAt();
            $data[$key]['montant'] = $c->getTotalttc();
            $data[$key]['ncheque'] = $c->getNumeroCheque();
            $data[$key]['espece'] = $c->getMontant();
            $data[$key]['reste'] = $c->getReste();
            $data[$key]['retenu'] = $c->getRetenu();
            $data[$key] ['nomCustomer'] = $c->getCustomer();
            if ($c->getTypePayement() == '1') {
                $data[$key]['tp'] ='Espese';
                $totalEspese = $totalEspese + $c->getMontant();

            }else if ($c->getTypePayement() == '2') {
                $totalCheque = $totalCheque + $c->getMontant();
                $data[$key]['tp'] ='Cheque';
            }else{
                $data[$key]['tp'] ='Carte';
            }
            if ($c->getType() == 'Facture') {
                $data[$key]['typeD'] = 'Facture';
                $data[$key]['num'] = $perfix_invoice.''.$c->getInvoice()->getId();

            }else if ($c->getType() == 'Dépence') {
                $data[$key]['typeD'] = 'Dépence';
                $data[$key]['num'] = $perfix_depense.''.$c->getDepense()->getId();
                $total_depense = $total_depense + $c->getMontant();
            }else{
                $data[$key]['typeD'] = 'Avoir';
                $data[$key]['num'] = $perfix_avoir.''.$c->getAvoir()->getId();
                $total_avoir = $total_avoir + $c->getMontant();
            }
        }

        return $this->render('commercial/caisse/index.html.twig'
            , array(
            'data' => $data,
            'total_depense' => $total_depense ,
            'totalEspese' => $totalEspese ,
            'totalCheque' => $totalCheque ,
            'totalAvoir' => $total_avoir ,
            'total_caisse' => $total_caisse));
    }



}