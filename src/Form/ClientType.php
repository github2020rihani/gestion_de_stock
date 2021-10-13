<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Client;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('adresse',TextType::class)
            ->add('country',EntityType::class,[
                'required' => false,
                'class'=> Country::class,
                'choice_label'=> 'name'
                ])
            ->add('city',EntityType::class,[
                'required' => false,
                'class'=> City::class,
                  'choice_label'=> 'name'])
           // ->add('location',LocationType::class)
            ->add('telephone',TextType::class)
            ->add('email',EmailType::class)
            ->add('code',TextType::class)
            ->add('codeTVA',TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
