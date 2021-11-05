<?php


namespace App\Controller\Commercial;


use App\Entity\BondLivraison;
use App\Entity\BonlivraisonArticle;
use App\Entity\Devis;
use App\Repository\ArticleRepository;
use App\Repository\BondLivraisonRepository;
use App\Repository\BonlivraisonArticleRepository;
use App\Repository\ClientRepository;
use App\Repository\DevisArticleRepository;
use App\Repository\DevisRepository;
use App\Repository\PrixRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/transfert/{id}", name="perso_transfert_devi")
     */
    public function transfertDeviToBL(Request $request, Devis $id)
    {
        $devis = $this->devisRepository->findDetailDeviAndStock($id);
//        dd($devis);
        return $this->render('commercial/bondLivraison/transfertDeviToBl.html.twig', array('devis' => $devis[0], 'taxe' => $_ENV['TVA_ARTICLE_PERCENT']));
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="perso_index_bl")
     */
    public function index()
    {
        $bls = $this->bondLivraisonRepository->findAll();
        dump($bls);
           return $this->render('commercial/bondLivraison/index.html.twig', array('bls' => $bls));

    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/add", name="perso_add_bl")
     */
    public function add()
    {
        $customers = $this->clientRepository->findAll();
        return $this->render('commercial/bondLivraison/add.html.twig', array('customers' => $customers));

    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{id}", name="perso_edit_bl")
     */
    public function edit(BondLivraison $id)
    {
        $customers = $this->clientRepository->findAll();
        return $this->render('commercial/bondLivraison/edit.html.twig', array('customers' => $customers, 'bl' => $id));

    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/detail/{id}", name="perso_detail_bl")
     */
    public function detail(BondLivraison $id)
    {
        $customers = $this->clientRepository->findAll();
        return $this->render('commercial/bondLivraison/detail.html.twig', array('customers' => $customers, 'bl' => $id));

    }

}