<?php

declare(strict_types=1);

namespace AppBundle\Handler;

use Adyen\Client;
use AppBundle\Command\CaptureAdyenPayment;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class CaptureAdyenPaymentHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var Client */
    private $client;

    public function __construct(OrderRepositoryInterface $orderRepository, Client $client)
    {
        $this->orderRepository = $orderRepository;
        $this->client = $client;
    }

    public function handle(CaptureAdyenPayment $captureAdyenPayment)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $captureAdyenPayment->token()]);

        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->get($captureAdyenPayment->payment());

        $adyenData = [
            'modificationAmount' => [
                'value' => $payment->getAmount(),
                'currency' => $payment->getCurrencyCode(),
            ],
            'originalReference' => $payment->getDetails()['pspReference'],
            'merchantAccount' => 'SyliusORG',
        ];

        $service = new \Adyen\Service\Modification($this->client);

        $service->capture($adyenData);
    }
}
