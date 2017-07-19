<?php

declare(strict_types=1);

namespace AppBundle\Command;

final class AuthorizeAdyenPayment
{
    /** @var string */
    private $token;

    /** @var string */
    private $paymentId;

    /** @var string */
    private $encryptedCreditCard;

    public function __construct(string $token, string $paymentId, string $encryptedCreditCard)
    {
        $this->token = $token;
        $this->paymentId = $paymentId;
        $this->encryptedCreditCard = $encryptedCreditCard;
    }

    /** @return string */
    public function token(): string
    {
        return $this->token;
    }

    /** @return string */
    public function payment(): string
    {
        return $this->paymentId;
    }

    /** @return string */
    public function encryptedCreditCard(): string
    {
        return $this->encryptedCreditCard;
    }
}
