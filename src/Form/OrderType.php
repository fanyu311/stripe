<?php

namespace App\Form;

use App\Entity\Order;
use App\Form\UserType;
use App\Form\ProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => 'reference',
                'sanitize_html' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Reference'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'price',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Price'
                ]
            ])
            ->add('user', UserType::class, [
                'label' => 'UserName',
                'sanitize_html' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'UserName'
                ]

            ])
            ->add('product', ProductType::class, [
                'label' => 'nom de product',
                'sanitize_html' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom de product'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
