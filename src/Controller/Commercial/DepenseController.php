<?php


namespace App\Controller\Commercial;

use App\Entity\Depense;
use App\Entity\Payemet;
use App\Repository\DepenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/personelle/depense")
 */
class DepenseController extends AbstractController
{

    private $em;
    private $depenseRepository;

    public function __construct(EntityManagerInterface $em, DepenseRepository $depenseRepository)
    {
        $this->em = $em;
        $this->depenseRepository = $depenseRepository;
    }

    /**
     * @Route("/", name="perso_index_depense")
     */
    public function index()
    {

        return $this->render('commercial/depense/index.html.twig', array('depenses' => $this->depenseRepository->findAll()));


    }

    /**
     * @Route("/add", name="perso_add_depense")
     */
    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            $date_depense = $request->get('date_dep');
            $total_ttc_dep = $request->get('total_ttc_dep');
            $type_dep = $request->get('type_dep');
            $desc_dep = $request->get('desc_dep');
            $dataRes = [];
            foreach ($date_depense as $key => $item) {
                $d = explode('T', $item);
                $dataRes[$key][] = $d[0];
                $dataRes[$key][] = $d[1];

            }
//            dd($dataRes);
            foreach ($dataRes as $key => $date) {
                $depense = new Depense();
                $depense->setType($type_dep[$key]);
                $depense->setDescription($desc_dep[$key]);
                $depense->setTotalTTC($total_ttc_dep[$key]);
                $depense->setDate(new \DateTime($date[0]));
                $depense->setHour($date[1]);
                $depense->setAddedBy($this->getUser());
                $this->em->persist($depense);
                $this->em->flush();
                //save caisse depence
                $payment = new Payemet();
                $payment->setAddedBy($this->getUser());
                $payment->setCreatedAt(new \DateTime('now'));
                $payment->setTotalttc($total_ttc_dep[$key]);
                $payment->setMontant($total_ttc_dep[$key]);
                $payment->setTypePayement(1);
                $payment->setType('Dépence');
                $this->em->persist($payment);
                $this->em->flush();

            }
            $this->addFlash('success', 'Ajout effectué avec succés');

            return $this->redirectToRoute('perso_index_depense');
        }


        return $this->render('commercial/depense/add.html.twig');


    }


    /**
     * @Route("/edit/{id}", name="perso_edit_depense")
     */
    public function edit(Request $request, Depense $id)
    {
        $date = new \DateTime('2000-01-01');
        $result = $date->format('Y-m-d ');
       // dd($result);
        if ($request->isMethod('post')) {
            $date_depense = $request->get('date_dep');
            $hour = $request->get('hour');
            $total_ttc_dep = $request->get('total_ttc_dep');
            $type_dep = $request->get('type_dep');
            $desc_dep = $request->get('desc_dep');

            $id->setType($type_dep);
            $id->setDescription($desc_dep);
            $id->setTotalTTC($total_ttc_dep);
            $id->setDate(new \DateTime($date_depense));
            $id->setHour($hour);
            $id->setAddedBy($this->getUser());
            $this->em->persist($id);
            $this->em->flush();


            $this->addFlash('success', 'modifier effectué avec succés');

            return $this->redirectToRoute('perso_index_depense');
        }


        return $this->render('commercial/depense/edit.html.twig', array('depense' => $id, 'datedep' => $result));


    }


}