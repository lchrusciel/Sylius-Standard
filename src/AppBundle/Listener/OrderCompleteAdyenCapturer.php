<?php

declare(strict_types=1);

namespace AppBundle\Listener;

use AppBundle\Command\CaptureAdyenPayment;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @internal
 */
final class OrderCompleteAdyenCapturer
{
    const PAYMENT_AUTHORIZED = 'authorized';

    /** @var CommandBus */
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function capture(OrderInterface $order)
    {
        $payment = $order->getLastPayment(self::PAYMENT_AUTHORIZED);

        $this->bus->handle(new CaptureAdyenPayment($payment->getId()));
    }
}
