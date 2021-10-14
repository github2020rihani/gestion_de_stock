<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\UserType;
use App\Repository\DepartementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/super_admin/user")
 */
class UserController extends AbstractController
{

    protected $em;
    protected $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/new", name="add_user")
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, DepartementRepository $departementRepository)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $emailExiste = $this->userRepository->findBy(array('email' => $email));
            if ($emailExiste) {
                $this->addFlash('error', ' email existe ');
                exit;
            }
            $role = $form->get('roles')->getData();
            $user->setRoles($role);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', ' Successfully added');
            return $this->redirectToRoute('index_user');
        }
        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
            'user' => ''
        ]);

    }

    /**
     * @Route("/", name="index_user")
     */
    public function index() {
        $users = $this->userRepository->findAll();
        return $this->render('admin/user/index.html.twig',[
            'users' =>$users
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_user")
     */
    public function edit(Request $request, User $user ,  UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $oldEmail = $user->getEmail();
            $emailExiste = $this->userRepository->findBy(array('email' => $email));
            if ($oldEmail != $email) {
              $user->setEmail($email);
            }
            $role = $request->request->get('roles');
            $password = $passwordEncoder->encodePassword(
                $user,
                $request->request->get('password'));

            if ($password && $password != $user->getPassword()) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $password
                    )
                );
            }
            $oldRole = $user->getRoles()[0];
            if ($role != $oldRole){
                $user->setRoles(array($role));
            }
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', ' Successfully modified');
            return $this->redirectToRoute('index_user');
        }
        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);

    }

    /**
     * @Route("/change_status", name="change_status")
     */
    public function changeStatus(Request  $request){

        $id = $request->get('id');
        $user = $this->userRepository->find($id);
        //if user existe
        if ($user){
            if ($user->getStatus() == true) {
                $user->setStatus(false);
                $classcss = 'badge badge-danger';
                $mot = 'Desactiver';
            }else{
                $user->setStatus(true);
                $classcss = 'badge badge-success';
                $mot = 'Activer';
            }
        }
        $this->em->persist($user);
        $this->em->flush();
        return $this->json(array('classcss' => $classcss , 'mot'=> $mot));

    }
}