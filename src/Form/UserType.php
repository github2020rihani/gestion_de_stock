<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)

            ->add('roles', CollectionType::class, [
                'entry_type'   => ChoiceType::class,
                'entry_options'  => [
                    'choices'  => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Responsable' => 'ROLE_RESPONSABLE',
                        'Gerant' =>  'ROLE_GERANT',
                    ],

                    'attr' => ['class' => 'form-control'],
                    'label' => false,
                ],
            ])
            ->add('plainPassword',RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'mot de passe',

                    'error_bubbling' => true,
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'attr'=>[
                        'placeholder'=> 'Mot de passe',
                        'required'=>true,

                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrer le mot de passe',
                        ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'Le mot de passe doit contenir au mois {{ limit }} charactéres',
                            // max length allowed by Symfony for security reasons
                            'max' => 20,
                        ]),
                    ],
                ],
                'second_options' => ['label' => 'Répèter votre mot de passe',

                    'error_bubbling' => true,
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'attr'=>[
                        'placeholder'=> 'Re-entrer le mot de passe',
                        'required'=>true,

                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Re-entrer le mot de passe',
                        ]),
                        /*
                        new Length([
                            'min' => 4,
                            'minMessage' => 'Le mot de passe doit contenir au mois{{ limit }} charactéres',
                            // max length allowed by Symfony for security reasons
                            'max' => 20,
                        ]),*/
                    ],
                ],

            ])
            ->add('firstName',TextType::class,[
                'attr'=>[
                    'placeholder'=> 'Nom',
                    'required'=>true
                ]
            ])
            ->add('lastName',TextType::class,[
                'attr'=>[
                    'placeholder'=> 'Prénom',
                    'required'=>true
                ]
            ])
            ->add('email',EmailType::class,[
                'attr'=>[
                    'placeholder'=> 'Email',
                    'required'=>true
                ]
            ])
            ->add('phone', TextType::class)
            ->add('matricule', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
