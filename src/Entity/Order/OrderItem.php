<?php

declare(strict_types=1);

namespace App\Entity\Order;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_order_item")
 */
class OrderItem extends BaseOrderItem
{
    public function __construct()
    {
        parent::__construct();

        $this->units = new ArrayCollection([new OrderItemUnit($this)]);
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function addUnit(OrderItemUnitInterface $itemUnit): void
    {
        throw new \InvalidArgumentException('Cannot add unit to order item');
    }

    public function removeUnit(OrderItemUnitInterface $itemUnit): void
    {
        throw new \InvalidArgumentException('Cannot remove unit to order item');
    }
}
