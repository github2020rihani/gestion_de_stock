<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Fournisseur;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('addedBy')
            ->add('Category',EntityType::class,[
                'required' => false,
                'class'=> category::class])
            ->add('Fournisseur', EntityType::class,[
                'required' => false,
                'class'=> fournisseur::class,
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => true,
                'asset_helper' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
