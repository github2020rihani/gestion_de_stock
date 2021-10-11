<?php

namespace App\Controller\Admin;
use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/client")
 */
class ClientController extends AbstractController
{


    /**
     * @Route("/new", name="add_client")
     */
    public function add( Request $request , EntityManagerInterface $em): Response
    {
        $clients = new Client();
        $form = $this->createForm(ClientType::class,$clients);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($clients);
            $em->flush();
            $this->addFlash('success',' Successfully added');

            return $this->redirectToRoute('index_client');
        }

        return $this->render('admin/client/new.html.twig',[
            'form' => $form->createView(),
            'client' => ''
        ]);
    }


    /**
     * @Route("/", name="index_client")
     */
    public function index( Request $request , EntityManagerInterface $em, ClientRepository $clientRepository): Response
    {
        $clients = $clientRepository->findAll();
        return $this->render('admin/client/index.html.twig',[
            'clients' =>$clients
        ]);
    }



    /**
     * @Route("/{id<\d+>}", name="delete_client")
     */
    public function delete(Client $clients, EntityManagerInterface $em, ClientRepository $clientRepository): Response
    {
        if ($clients) {
            $em->remove($clients);
            $em->flush();
            $this->addFlash('success',' Successfully deleted');
        }else{
            $this->addFlash('error',' error deleted');
        }
        return $this->redirectToRoute('index_client');
    }


    /**
     * @Route("/edit/{id<\d+>}", name="edit_client")
     */
    public function edit(Client $client, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(ClientType::class,$client);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($client);
            $em->flush();
            $this->addFlash('success',' Successfully edit');

            return $this->redirectToRoute('index_client');
        }

        return $this->render('admin/client/new.html.twig',[
            'form' => $form->createView(),
            'client' => $client
        ]);
    }







}



