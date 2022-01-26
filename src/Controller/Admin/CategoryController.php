<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
     * @Route("/achat/category")
 */
class CategoryController extends AbstractController
{


    /**
     * @Route("/new", name="achat_add_category")
     */
    public function add( Request $request , EntityManagerInterface $em, CategoryRepository $categoryRepository): Response
    {
        $categories = new Category();
        $form = $this->createForm(CategoryType::class,$categories);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $categoryExiste = $categoryRepository->findBy(array('title' =>  $form->get('title')->getData()));
            if ($categoryExiste) {
                $this->addFlash('error','La catégorie '.$form->get('title')->getData().' existe');

                return $this->render('admin/category/new.html.twig',[
                    'form' => $form->createView(),
                    'categorie' => ''
                ]);
            }
            $em->persist($categories);
            $em->flush();
            $this->addFlash('success','Ajout effectué avec succés');

            return $this->redirectToRoute('achat_index_category');
        }

        return $this->render('admin/category/new.html.twig',[
            'form' => $form->createView(),
            'categorie' => ''
        ]);
    }


    /**
     * @Route("/", name="achat_index_category")
     */
    public function index( Request $request , EntityManagerInterface $em, CategoryRepository  $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('admin/category/index.html.twig',[
            'categories' =>$categories
        ]);
    }



    /**
     * @Route("/{id<\d+>}", name="achat_delete_category")
     */
    public function delete(Category $categories, EntityManagerInterface $em, CategoryRepository $categoryRepository, ArticleRepository $articleRepository): Response
    {
        if ($categories) {
            $categorieLieByArticle = $articleRepository->findBy(array('categorie' => $categories));

            if ($categorieLieByArticle) {
                $this->addFlash('error','Cet catégorie lié avec un ou des (articles), tu ne peut pas supprimers');
                return $this->redirectToRoute('achat_index_category');
            }

            $em->remove($categories);
            $em->flush();
            $this->addFlash('success','Supprimer effectué avec succés');
        }else{
            $this->addFlash('error','Aucun categorie trouvé ');
        }
        return $this->redirectToRoute('achat_index_category');
    }


    /**
     * @Route("/edit/{id<\d+>}", name="achat_edit_category")
     */
    public function edit(Category $categorie, Request $request, EntityManagerInterface $em, ArticleRepository $articleRepository): Response
    {



        $form = $this->createForm(CategoryType::class,$categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->persist($categorie);
            $em->flush();
            $this->addFlash('success','Modifier effectué avec succés');

            return $this->redirectToRoute('achat_index_category');
        }

        return $this->render('admin/category/new.html.twig',[
            'form' => $form->createView(),
            'categorie' => $categorie
        ]);
    }







}



