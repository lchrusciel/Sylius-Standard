<?php

declare(strict_types=1);

namespace AppBundle\Command;

final class CaptureAdyenPayment
{
    /** @var string */
    private $token;

    /** @var string */
    private $paymentId;

    public function __construct(string $token, string $paymentId)
    {
        $this->token = $token;
        $this->paymentId = $paymentId;
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
}
