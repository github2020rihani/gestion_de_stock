<?php

namespace App\Controller\Admin;
use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super_admin/fournisseur")
 */
class FournisseurController extends AbstractController
{


    /**
     * @Route("/new", name="add_fournisseur")
     */
    public function add( Request $request , EntityManagerInterface $em): Response
    {
        $fournisseurs = new Fournisseur();
        $form = $this->createForm(FournisseurType::class,$fournisseurs);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($fournisseurs);
            $em->flush();
            $this->addFlash('success',' Successfully added');

            return $this->redirectToRoute('index_fournisseur');
        }

        return $this->render('admin/fournisseur/new.html.twig',[
            'form' => $form->createView(),
            'fournisseur' => ''
        ]);
    }


    /**
     * @Route("/", name="index_fournisseur")
     */
    public function index( Request $request , EntityManagerInterface $em, FournisseurRepository $fournisseurRepository): Response
    {
        $fournisseurs = $fournisseurRepository->findAll();
        return $this->render('admin/fournisseur/index.html.twig',[
            'fournisseurs' =>$fournisseurs
        ]);
    }



    /**
     * @Route("/{id<\d+>}", name="delete_fournisseur")
     */
    public function delete(Fournisseur $fournisseurs, EntityManagerInterface $em, FournisseurRepository $fournisseurRepository): Response
    {
        if ($fournisseurs) {
            $em->remove($fournisseurs);
            $em->flush();
            $this->addFlash('success',' Successfully deleted');
        }else{
            $this->addFlash('error',' error deleted');
        }
        return $this->redirectToRoute('index_fournisseur');
    }

    /**
     * @Route("/edit/{id<\d+>}", name="edit_fournisseur")
     */
    public function edit(Fournisseur $fournisseur, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(FournisseurType::class,$fournisseur);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($fournisseur);
            $em->flush();
            $this->addFlash('success',' Successfully edit');

            return $this->redirectToRoute('index_fournisseur');
        }

        return $this->render('admin/fournisseur/new.html.twig',[
            'form' => $form->createView(),
            'fournisseur' => $fournisseur
        ]);
    }






}



