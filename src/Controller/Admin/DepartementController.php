<?php


namespace App\Controller\Admin;


use App\Entity\Departement;
use App\Form\DepartementType;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class DepartementController extends AbstractController
{

    private $em;
    private $departementRepository;

    public function __construct(EntityManagerInterface $em, DepartementRepository $departementRepository)
    {
        $this->em = $em;
        $this->departementRepository = $departementRepository;
    }

    /**
     * @param Request $request
     * @Route("/super_admin/departement/new", name="add_departement")
     */
    public function new(Request $request)
    {
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class,$departement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($departement);
            $this->em->flush();
            $this->addFlash('success','Ajout effectué avec succés');

            return $this->redirectToRoute('index_departement');
        }

        return $this->render('admin/departement/new.html.twig',[
            'form' => $form->createView(),
            'departement' => ''
        ]);
    }

    /**
     * @param Departement $departement
     * @param Request $request
     * @Route("/super_admin/departement/{id}", name="edit_departement")
     */
    public function edit(Departement $departement, Request $request)
    {
        $form = $this->createForm(DepartementType::class,$departement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($departement);
            $this->em->flush();
            $this->addFlash('success','Modifier effectué avec succés');

            return $this->redirectToRoute('index_departement');
        }

        return $this->render('admin/departement/new.html.twig',[
            'form' => $form->createView(),
            'departement' => $departement
        ]);
    }

    /**
     * @Route("/super_admin/departement", name="index_departement")
     */
    public function index()
    {
        return $this->render('admin\departement\index.html.twig', array('departemets' => $this->departementRepository->findAll()));
    }

}