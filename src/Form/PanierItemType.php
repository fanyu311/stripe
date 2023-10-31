<?php

namespace App\Form;

use App\Entity\PanierItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PanierItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('quantity')
            ->add('panier', EntityType::class, [ // 使用 EntityType 字段来选择购物车
                'class' => 'App\Entity\Panier', // 购物车实体的类名
                'choice_label' => 'name', // 显示在下拉列表中的购物车属性
                'label' => 'Panier', // 自定义字段标签
            ])
            ->add('product', EntityType::class, [ // 使用 EntityType 字段
                'class' => 'App\Entity\Product', // 产品实体的类名
                'choice_label' => 'name', // 显示在下拉列表中的产品属性
                'label' => 'Product', // 自定义字段标签
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PanierItem::class,
        ]);
    }
}
