<?php


namespace App\Controller\superAdmin;


use App\Repository\AchatArticleRepository;
use App\Repository\PrixRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super_admin/prix")
 */
class prixController extends AbstractController
{
    private $em;
    private $achatArticleRepository;
    private $prixRepository;


    public function __construct(EntityManagerInterface $em, AchatArticleRepository $achatArticleRepository,
                                PrixRepository $prixRepository)
    {
        $this->em = $em;
        $this->achatArticleRepository = $achatArticleRepository;
        $this->prixRepository = $prixRepository;


    }

    /**
     * @Route("/" , name="index_prix")
     */
    public function index()
    {
        $prixs = $this->prixRepository->findAll();
        return $this->render('superAdmin/prix/index.html.twig' , ['prixs' => $prixs]);
    }

    /**
     * @Route("/details" , name="detail_articles")
     */
    public function details()
    {
        $prixs = $this->prixRepository->findAll();
        return $this->render('superAdmin/prix/details.html.twig' , ['prixs' => $prixs]);
    }

}