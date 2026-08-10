<?php

namespace App\Payments;

interface PaymentProviderInterface
{
    public function getName();
    public function charge(
        $config,
        $cardNumber,
        $expiry,
        $cvv,
        $amountCents,
        $description
    );
}
