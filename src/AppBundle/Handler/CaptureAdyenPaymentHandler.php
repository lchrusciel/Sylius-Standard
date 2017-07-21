<?php

declare(strict_types=1);

namespace AppBundle\Handler;

use Adyen\AdyenException;
use Adyen\Client;
use AppBundle\Command\CaptureAdyenPayment;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Webmozart\Assert\Assert;

final class CaptureAdyenPaymentHandler
{
    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    /** @var Client */
    private $client;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    public function __construct(PaymentRepositoryInterface $paymentRepository, Client $client, FactoryInterface $stateMachineFactory)
    {
        $this->paymentRepository = $paymentRepository;
        $this->client = $client;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function handle(CaptureAdyenPayment $captureAdyenPayment)
    {
        /** @var PaymentInterface $payment */
        $payment = $this->paymentRepository->find($captureAdyenPayment->paymentId());

        $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);

        $adyenData = [
            'modificationAmount' => [
                'value' => $payment->getAmount(),
                'currency' => $payment->getCurrencyCode(),
            ],
            'originalReference' => $payment->getDetails()['pspReference'],
            'merchantAccount' => 'SyliusORG',
        ];

        Assert::true($stateMachine->can(PaymentTransitions::TRANSITION_COMPLETE), 'Payment cannot be captured.');

        $service = new \Adyen\Service\Modification($this->client);

        try {
            $payment->setDetails($service->capture($adyenData));

            $stateMachine->apply(PaymentTransitions::TRANSITION_COMPLETE);
        } catch (AdyenException $exception) {
            $payment->setDetails(['exception' => $exception->getMessage()]);

            $stateMachine->apply(PaymentTransitions::TRANSITION_FAIL);
        }
    }
}
