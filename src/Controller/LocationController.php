<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    /**
     * @Route("/location", name="location_new")
     */
    public function new(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $location = new Location();

        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();

            $this->addFlash('success', 'Added.' .
                ' <a href="' . $this->generateUrl('location_edit', ['id' => $location->getId()]) . '">Click for edit</a>');
        }

        return $this->render('location/new.html.twig', [
            'locationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="location_edit")
     */
    public function edit($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $location = $em->getRepository(Location::class)->find($id);

        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            var_dump($form->getData());
        }

        return $this->render('location/edit.html.twig', [
            'locationForm' => $form->createView()
        ]);
    }
}
