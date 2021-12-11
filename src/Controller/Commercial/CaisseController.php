<?php


namespace App\Controller\Commercial;


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

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

    }

    /**
     * @Route("/", name="perso_index_caisse")
     */
    public function index() {
        die;

    }



}