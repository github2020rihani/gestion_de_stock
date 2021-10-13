<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="client")
     */
    public function index(): Response
    {
        return $this->render('test/client.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/test", name="fournisseur")
     */
    public function index2(): Response
    {
        return $this->render('test/fournisseur.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
