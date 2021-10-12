<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractController
{


    /**
     * @Route("/super_admin/", name="dashboard_super_admin")
     */
    public function dashboard()
    {

        return $this->render('dashboard/dashboard_super_admin.html.twig');
    }
    /**
     * @Route("/admin/", name="dashboard_admin")
     */
    public function dashboardAdmin()
    {

        return $this->render('dashboard/dashboard_admin.html.twig');
    }
    /**
     * @Route("/responsable/", name="dashboard_responsable")
     */
    public function dashboardResponsable()
    {

        return $this->render('dashboard/dashboard_responsable.html.twig');
    }

    /**
     * @Route("/gerant/", name="dashboard_gerant")
     */
    public function dashboardGerant()
    {

        return $this->render('dashboard/dashboard_gerant.html.twig');
    }
}
