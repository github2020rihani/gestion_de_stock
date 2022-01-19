<?php

namespace App\Controller\Commercial;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/personelle/client")
 */
class ClientController extends AbstractController
{


    /**
     * @Route("/new", name="perso_add_client")
     */
    public function add(Request $request, EntityManagerInterface $em, ClientRepository $clientRepository): Response
    {
        $clients = new Client();
        $form = $this->createForm(ClientType::class, $clients);
        $form->handleRequest($request);
        $lastCustomer = $clientRepository->getLastCustomer();
        if ($lastCustomer) {
            $numCustomer = $_ENV['PREFIX_CUSTOMER'] . ((int)$lastCustomer->getId()) + 1;

        } else {
            $numCustomer = $_ENV['PREFIX_CUSTOMER'] . '1';
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $clients->getEmail();

            $clients->setCode($numCustomer);
            $em->persist($clients);
            $em->flush();

            $em->flush();
            $this->addFlash('success', ' Successfully added');

            return $this->redirectToRoute('perso_index_client');
        }


        return $this->render('commercial/client/new.html.twig', [
            'form' => $form->createView(),
            'client' => ''
        ]);
    }


    /**
     * @Route("/", name="perso_index_client")
     */
    public function index(Request $request, EntityManagerInterface $em, ClientRepository $clientRepository): Response
    {
        $clients = $clientRepository->findAll();
        return $this->render('commercial/client/index.html.twig', [
            'clients' => $clients
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="perso_delete_client")
     */
    public function delete(Client $clients, EntityManagerInterface $em, ClientRepository $clientRepository): Response
    {
        if ($clients) {
            $em->remove($clients);
            $em->flush();
            $this->addFlash('success', ' Successfully deleted');
        } else {
            $this->addFlash('error', ' error deleted');
        }
        return $this->redirectToRoute('perso_index_client');
    }


    /**
     * @Route("/edit/{id<\d+>}", name="perso_edit_client")
     */
    public function edit(Client $client, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();
            $this->addFlash('success', ' Successfully edit');

            return $this->redirectToRoute('perso_index_client');
        }

        return $this->render('commercial/client/new.html.twig', [
            'form' => $form->createView(),
            'client' => $client
        ]);
    }


}



