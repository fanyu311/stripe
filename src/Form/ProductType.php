<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\ProductImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
                'sanitize_html' => true,
                'attr' => [
                    'placeholder' => 'Nom de produit',
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
                'sanitize_html' => true,
                'attr' => [
                    'placeholder' => 'Descripcion'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prix de produit'
                ]
            ])
            ->add('image', VichImageType::class, [
                'label' => 'Image de la produit',

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
