<?php

namespace App\Controller\Admin;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/new", name="add_produit")
     */
    public function add( Request $request , EntityManagerInterface $em): Response
    {
        $produits = new Produit();
        $form = $this->createForm(ProduitType::class,$produits);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($produits);
            $em->flush();
            $this->addFlash('success',' Successfully added');

            return $this->redirectToRoute('index_produit');
        }

        return $this->render('admin/produit/new.html.twig',[
            'form' => $form->createView(),
            'produit' => ''
        ]);
    }


    /**
     * @Route("/", name="index_produit")
     */
    public function index( Request $request , EntityManagerInterface $em, ProduitRepository  $produitRepository): Response
    {
        $produits = $produitRepository->findBy([], ['createdAt'=>'DESC']);
        return $this->render('admin/produit/index.html.twig',[
            'produits' =>$produits
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="delete_produit")
     */
    public function delete(Produit $produits, EntityManagerInterface $em, ProduitRepository $produitRepository): Response
    {
        if ($produits) {
            $em->remove($produits);
            $em->flush();
            $this->addFlash('success',' Successfully deleted');
        }else{
            $this->addFlash('error',' error deleted');
        }
        return $this->redirectToRoute('index_produit');
    }

    /**
     * @Route("/edit/{id<\d+>}", name="edit_produit")
     */
    public function edit(Produit $produit, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(ProduitType::class,$produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($produit);
            $em->flush();
            $this->addFlash('success',' Successfully edit');

            return $this->redirectToRoute('index_produit');
        }

        return $this->render('admin/produit/new.html.twig',[
            'form' => $form->createView(),
            'produit' => $produit
        ]);
    }






}



