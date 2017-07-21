<?php

namespace AppBundle\Payment;

final class PaymentTransitions
{
    const GRAPH = 'sylius_payment';

    const TRANSITION_AUTHORIZE = 'authorize';

    private function __construct()
    {
    }
}
