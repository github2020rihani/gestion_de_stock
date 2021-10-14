<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Country;
use App\Entity\Fournisseur;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('designation')
            ->add('quantite')
            ->add('unite')
            ->add('prixHT')
            ->add('tva')
            ->add('description')
            ->add('qteSec')
            ->add('category', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'title',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                ]
            )
            ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
                'placeholder' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => []
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => false,
            ])
        ;

        $formModifier = function (FormInterface $form, Category $category = null) {
            $fournisseurs = [];

            if ($category !== null) {
                $fournisseurs = $category->getFournisseurs();
            }

            $form->add('fournisseur', EntityType::class, [
                'choice_label' => 'code',
                'class' => Fournisseur::class,
                'placeholder' => false,
                'choices' => $fournisseurs
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                if ($data === null || !method_exists($data, 'getCategory')) {
                    $formModifier($event->getForm(), null);
                } else {
                    $formModifier($event->getForm(), $data->getCategory());
                }

            }
        );

        $builder->get('category')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $category = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $category);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
