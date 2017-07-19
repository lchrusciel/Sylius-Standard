<?php

declare(strict_types=1);

namespace AppBundle\Handler;

use AppBundle\Command\AuthorizeAdyenPayment;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class CaptureAdyenPaymentHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var string */
    private $applicationName;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /**
     * AuthorizeAdyenPaymentHandler constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param string $applicationName
     * @param string $username
     * @param string $password
     */
    public function __construct(OrderRepositoryInterface $orderRepository, string $applicationName, string $username, string $password)
    {
        $this->orderRepository = $orderRepository;
        $this->applicationName = $applicationName;
        $this->username = $username;
        $this->password = $password;
    }

    public function handle(AuthorizeAdyenPayment $authorizeAdyenPayment)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $authorizeAdyenPayment->token()]);

        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->get($authorizeAdyenPayment->payment());

        $adyenData = [
            'modificationAmount' => [
                'value' => $payment->getAmount(),
                'currency' => $payment->getCurrencyCode(),
            ],
            'originalReference' => $payment->getDetails()['pspReference'],
            'merchantAccount' => 'SyliusORG',
        ];

        $client = new \Adyen\Client();
        $client->setApplicationName($this->applicationName);
        $client->setUsername($this->username);
        $client->setPassword($this->password);
        $client->setEnvironment(\Adyen\Environment::TEST);

        $service = new \Adyen\Service\Payment($client);

        $payment->setDetails($service->authorise($adyenData));
    }
}
