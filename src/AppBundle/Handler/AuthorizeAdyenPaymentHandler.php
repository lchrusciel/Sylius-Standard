<?php

declare(strict_types=1);

namespace AppBundle\Handler;

use Adyen\AdyenException;
use AppBundle\Payment\PaymentTransitions as UrbanaraPaymentTransitions;
use Sylius\Component\Payment\PaymentTransitions as SyliusPaymentTransitions;
use Adyen\Client;
use Adyen\Service\Payment;
use AppBundle\Command\AuthorizeAdyenPayment;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class AuthorizeAdyenPaymentHandler
{
    const PAYMENT_CART = 'cart';

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var Client */
    private $client;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    public function __construct(OrderRepositoryInterface $orderRepository, Client $client, FactoryInterface $stateMachineFactory)
    {
        $this->orderRepository = $orderRepository;
        $this->client = $client;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function handle(AuthorizeAdyenPayment $authorizeAdyenPayment)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $authorizeAdyenPayment->token()]);

        /** @var PaymentInterface $payment */
        $payment = $order->getLastPayment(self::PAYMENT_CART);

        $stateMachine = $this->stateMachineFactory->get($payment, SyliusPaymentTransitions::GRAPH);

        Assert::true($stateMachine->can(SyliusPaymentTransitions::TRANSITION_CREATE), 'Payment cannot be initialized.');
        $stateMachine->apply(SyliusPaymentTransitions::TRANSITION_CREATE);

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

        Assert::true($stateMachine->can(UrbanaraPaymentTransitions::TRANSITION_AUTHORIZE), 'Payment cannot be authorized.');

        $service = new Payment($this->client);

        try {
            $payment->setDetails($service->authorise($adyenData));

            $stateMachine->apply(UrbanaraPaymentTransitions::TRANSITION_AUTHORIZE);
        } catch (AdyenException $exception) {
            $payment->setDetails(['exception' => $exception->getMessage()]);

            $stateMachine->apply(SyliusPaymentTransitions::TRANSITION_FAIL);
        }
    }
}
