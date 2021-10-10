<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class DashboardController extends AbstractController
{


    /**
     * @Route("/", name="dashboard_super_admin")
     */
    public function dashboard()
    {

        return $this->render('base.html.twig');
    }
}
