<?php

declare(strict_types=1);

namespace AppBundle\Command;

final class CaptureAdyenPayment
{
    /** @var string */
    private $paymentId;

    public function __construct($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function paymentId()
    {
        return $this->paymentId;
    }
}
