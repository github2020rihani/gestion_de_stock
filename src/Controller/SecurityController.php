<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    /**
     * @Route("/edit_profile", name="edit_profile")
     */

    public function editProfile(Request $request, UserRepository $userRepository, EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder)
    {

$currentUser = $this->getUser();
        if ($request->isMethod('POST')) {
            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
            $password = $request->get('password');
            $email = $request->get('email');


            $oldEmail = $this->getUser()->getEmail();
            if ($oldEmail != $email) {
                $emailExiste = $userRepository->findBy(array('email' => $email));
                if ($emailExiste) {
                    $this->addFlash('error', 'Email existe déja  ');
                    return $this->render('superAdmin/default/profile.html.twig', [
                    ]);
                }

            }


            if ($password && $password !=$currentUser->getPassword()) {
                $this->getUser()->setPassword(
                    $passwordEncoder->hashPassword(
                        $currentUser,
                        $password
                    )
                );
            }
            $currentUser->setFirstName($firstName);
            $currentUser->setLastName($lastName);
            $em->persist($currentUser);
            $em->flush();
            $this->addFlash('success', 'Modifier effectué avec succés');
            return $this->redirectToRoute('profile');
        }


    }


}
