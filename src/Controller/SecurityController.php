<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }z

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user->setLastLogin(new \DateTime());
        $em->persist($user);
        $em->flush();
        throw new \RuntimeException('Cela ne devrait jamais être atteint!');

    }


    /**
     * @Route("/profile", name="profile")
     */

    public function profile()
    {
        $user = $this->getUser();
        return $this->render('default/profile.html.twig', array('user' => $user));


    }


}
