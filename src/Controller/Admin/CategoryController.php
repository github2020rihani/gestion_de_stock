<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Entity\Category;
use App\Form\CategoriesType;
use App\Form\CategorYType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/category")
 */
class CategoryController extends AbstractController
{


    /**
     * @Route("/new", name="add_category")
     */
    public function add( Request $request , EntityManagerInterface $em): Response
    {
        $categories = new Category();
        $form = $this->createForm(CategorYType::class,$categories);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($categories);
            $em->flush();
            $this->addFlash('success',' Successfully added');

            return $this->redirectToRoute('index_category');
        }

        return $this->render('admin/category/new.html.twig',[
            'form' => $form->createView(),
            'categorie' => ''
        ]);
    }


    /**
     * @Route("/", name="index_category")
     */
    public function index( Request $request , EntityManagerInterface $em, CategoryRepository  $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('admin/category/index.html.twig',[
            'categories' =>$categories
        ]);
    }



    /**
     * @Route("/{id<\d+>}", name="delete_category")
     */
    public function delete(Category $categories, EntityManagerInterface $em, CategoryRepository $categoryRepository): Response
    {
        if ($categories) {
            $em->remove($categories);
            $em->flush();
            $this->addFlash('success',' Successfully deleted');
        }else{
            $this->addFlash('error',' error deleted');
        }
        return $this->redirectToRoute('index_category');
    }


    /**
     * @Route("/edit/{id<\d+>}", name="edit_category")
     */
    public function edit(Category $categorie, Request $request, EntityManagerInterface $em): Response
    {



        $form = $this->createForm(CategorYType::class,$categorie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($categorie);
            $em->flush();
            $this->addFlash('success',' Successfully edit');

            return $this->redirectToRoute('index_category');
        }

        return $this->render('admin/category/new.html.twig',[
            'form' => $form->createView(),
            'categorie' => $categorie
        ]);
    }







}



