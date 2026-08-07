<?php

namespace App\Payments;

interface PaymentProviderInterface
{
    public function charge(
        $cardNumber,
        $expiry,
        $cvv,
        $amountCents,
        $description
    );
}