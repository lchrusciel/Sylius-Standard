<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App;

use Sylius\Component\Order\Factory\OrderItemUnitFactoryInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

class OrderItemQuantityModifier implements OrderItemQuantityModifierInterface
{
    public function __construct()
    {
    }

    public function modify(OrderItemInterface $orderItem, int $targetQuantity): void
    {
        $orderItem->setQuantity($targetQuantity);
    }
}
