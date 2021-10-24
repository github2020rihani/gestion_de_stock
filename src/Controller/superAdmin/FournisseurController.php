<?php

namespace App\Controller\superAdmin;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\AchatRepository;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/super_admin/fournisseur")
 */
class FournisseurController extends AbstractController
{


    /**
     * @Route("/new", name="add_fournisseur")
     */
    public function add(Request $request, EntityManagerInterface $em, FournisseurRepository $fournisseurRepository, ValidatorInterface $validator): Response
    {
        $fournisseurs = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseurs);
        $form->handleRequest($request);
        $fourniseurExiste = $fournisseurRepository->findByCodeAndEmail($form->get('email')->getData(), $form->get('code')->getData());
        if ($fourniseurExiste) {
            $this->addFlash('warning', 'il ya un fournisseur existe avec cet email ou code');
            return $this->render('superAdmin/fournisseur/new.html.twig', [
                'form' => $form->createView(),
                'fournisseur' => ''
            ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($fournisseurs);
            $em->flush();
            $this->addFlash('success', ' Successfully added');

            return $this->redirectToRoute('index_fournisseur');
        }

        return $this->render('superAdmin/fournisseur/new.html.twig', [
            'form' => $form->createView(),
            'fournisseur' => ''
        ]);
    }


    /**
     * @Route("/", name="index_fournisseur")
     */
    public function index(Request $request, EntityManagerInterface $em, FournisseurRepository $fournisseurRepository): Response
    {
        $fournisseurs = $fournisseurRepository->findAll();
        return $this->render('superAdmin/fournisseur/index.html.twig', [
            'fournisseurs' => $fournisseurs
        ]);
    }


    /**
     * @Route("/{id<\d+>}", name="delete_fournisseur")
     */
    public function delete(Fournisseur $fournisseurs, EntityManagerInterface $em, FournisseurRepository $fournisseurRepository, AchatRepository $achatRepository): Response
    {
        if ($fournisseurs) {
            $fournisseurLieAchat = $achatRepository->findBy(array('fournisseur' => $fournisseurs));
            if ($fournisseurLieAchat) {
                $this->addFlash('error','Tu ne peut pas supprimer ce fournisseur');
                return $this->redirectToRoute('index_fournisseur');
            }
            $em->remove($fournisseurs);
            $em->flush();
            $this->addFlash('success', ' Successfully deleted');
        } else {
            $this->addFlash('error', ' error deleted');
        }
        return $this->redirectToRoute('index_fournisseur');
    }

    /**
     * @Route("/edit/{id<\d+>}", name="edit_fournisseur")
     */
    public function edit(Fournisseur $fournisseur, Request $request, EntityManagerInterface $em, FournisseurRepository $fournisseurRepository): Response
    {

        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);
        $oldCode = $fournisseur->getCode();
        $oldEmail = $fournisseur->getEmail();
        $fourniseurExiste= '';





        if ($form->isSubmitted() && $form->isValid()) {
            if ($oldCode != $form->get('code')->getData()) {
                $fourniseurExiste = $fournisseurRepository->findByCode($form->get('code')->getData());

            }
            if ($oldEmail != $form->get('email')->getData()) {
                $fourniseurExiste = $fournisseurRepository->findByEmail($form->get('email')->getData());

            }
            if ($fourniseurExiste) {
                $this->addFlash('warning', 'il ya un fournisseur existe avec cet email ou code');
                return $this->render('superAdmin/fournisseur/new.html.twig', [
                    'form' => $form->createView(),
                    'fournisseur' => $fournisseur
                ]);
            }
            $em->persist($fournisseur);
            $em->flush();
            $this->addFlash('success', ' Successfully edit');

            return $this->redirectToRoute('index_fournisseur');
        }

        return $this->render('superAdmin/fournisseur/new.html.twig', [
            'form' => $form->createView(),
            'fournisseur' => $fournisseur
        ]);
    }


}



