<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('ref')
            ->add('description')
            ->add('remise', IntegerType::class, array(
                'required' => true
            ))
            ->add('categorie', EntityType::class, [
                    'class' => Category::class,
//                    'placeholder' => 'choisir une catégorie',
                    'choice_label' => 'title',

                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
