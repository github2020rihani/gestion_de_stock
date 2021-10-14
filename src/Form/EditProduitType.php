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

class EditProduitType extends AbstractType
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
            ->add('category',EntityType::class,[
                'required' => false,
                'class'=> category::class])
            ->add('fournisseur', EntityType::class,[
                'required' => false,
                'class'=> fournisseur::class,
                'choice_label' => 'nom' ,
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
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
