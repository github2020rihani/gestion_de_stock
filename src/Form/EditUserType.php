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

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('function', TextType::class)



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
