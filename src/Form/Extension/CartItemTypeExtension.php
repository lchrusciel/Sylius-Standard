<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantMatchType;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CartItemTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('quantity', IntegerType::class, [
            'attr' => ['min' => 1],
            'label' => 'sylius.ui.quantity',
        ]);

        if (isset($options['product']) && $options['product']->hasVariants() && !$options['product']->isSimple()) {
            if (Product::VARIANT_SELECTION_CHOICE === $options['product']->getVariantSelectionMethod()) {
                $builder->add('variantChoice', ProductVariantChoiceType::class, [
                    'product' => $options['product'],
                    'property_path' => 'variant',
                ]);
            } else {
                $builder->add('variantMatch', ProductVariantMatchType::class, [
                    'product' => $options['product'],
                    'property_path' => 'variant',
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'product',
            ])
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    public function getExtendedType(): string
    {
        return CartItemType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [CartItemType::class];
    }
}
