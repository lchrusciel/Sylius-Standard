<?php

declare(strict_types=1);

namespace AppBundle\Handler;

use Adyen\Client;
use AppBundle\Command\AuthorizeAdyenPayment;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class AuthorizeAdyenPaymentHandler
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

    public function handle(AuthorizeAdyenPayment $authorizeAdyenPayment)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $authorizeAdyenPayment->token()]);

        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->get($authorizeAdyenPayment->payment());

        $adyenData = [
            'additionalData' => [
                'card.encrypted.json' => $authorizeAdyenPayment->encryptedCreditCard(),
            ],
            'amount' => [
                'value' => $payment->getAmount(),
                'currency' => $payment->getCurrencyCode(),
            ],
            'reference' => 'Payment for order with id ' . $order->getId(),
        ];

        $service = new \Adyen\Service\Payment($this->client);

        $payment->setDetails($service->authorise($adyenData));
    }
}
