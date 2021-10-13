<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class)
            ->add('country', EntityType::class, [
                    'class' => Country::class,
                    'choice_label' => 'name',
                    'attr' => [
                        'class' => 'input'
                    ],
                ]
            )->add('city', EntityType::class, [
                'choice_label' => 'name',
                'class' => City::class,
                'placeholder' => false,
                'choices' => []
            ])
            ->add('submit',SubmitType::class);

        $formModifier = function (FormInterface $form, Country $country = null) {
            $cities = [];

            if ($country !== null) {
                $cities = $country->getCities();
            }

            $form->add('city', EntityType::class, [
                'choice_label' => 'name',
                'class' => City::class,
                'placeholder' => false,
                'choices' => $cities
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                if ($data === null || !method_exists($data, 'getCountry')) {
                    $formModifier($event->getForm(), null);
                } else {
                    $formModifier($event->getForm(), $data->getCountry());
                }

            }
        );

        $builder->get('country')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $country = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $country);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
