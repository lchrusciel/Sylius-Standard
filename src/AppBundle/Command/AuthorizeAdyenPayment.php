<?php

declare(strict_types=1);

namespace AppBundle\Command;

final class AuthorizeAdyenPayment
{
    /** @var string */
    private $token;

    /** @var string */
    private $encryptedCreditCard;

    public function __construct(string $token, string $encryptedCreditCard)
    {
        $this->token = $token;
        $this->encryptedCreditCard = $encryptedCreditCard;
    }

    /** @return string */
    public function token(): string
    {
        return $this->token;
    }

    /** @return string */
    public function encryptedCreditCard(): string
    {
        return $this->encryptedCreditCard;
    }
}
