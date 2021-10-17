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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function new(Request $request, UserPasswordHasherInterface  $passwordEncoder, DepartementRepository $departementRepository)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $emailExiste = $this->userRepository->findBy(array('email' => $email));
            if ($emailExiste) {
                $this->addFlash('error', 'Email existe déja  ');
                exit;
            }
            $role = $form->get('roles')->getData();
            $user->setRoles($role);
            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Ajout effectué avec succés');
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
        $users = $this->userRepository->findUserByDepartementSaufSuperAdmin($this->getUser()->getDepartemnt()->getId(), $this->getUser());
        return $this->render('admin/user/index.html.twig',[
            'users' =>$users
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_user")
     */
    public function edit(Request $request, User $user ,  UserPasswordHasherInterface $passwordEncoder, DepartementRepository $departementRepository)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $departements = $departementRepository->findAll();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $oldepart = $user->getDepartemnt();
            $departement = $request->get('departement');
            if ($oldepart != $departement) {
                $user->setDepartemnt($departementRepository->find($departement));
            }

            $oldEmail = $user->getEmail();
            if ($oldEmail != $email) {
                $emailExiste = $this->userRepository->findBy(array('email' => $email));
                if ($emailExiste) {
                    $this->addFlash('error', 'Email existe déja  ');
                    exit;
                }

                $user->setEmail($email);
            }
            $role = $request->request->get('roles');
            //new password

//            dump($request->request->get('password'));
            dump($user->getPassword());
            dump(     $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    '123456'
                )
            ));
            $password = $request->request->get('password');



            if ($password && $password != $user->getPassword()) {
                $user->setPassword(
                    $passwordEncoder->hashPassword(
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
            $this->addFlash('success', 'Modifier effectué avec succés');
            return $this->redirectToRoute('index_user');
        }
        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'departements' => $departements
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