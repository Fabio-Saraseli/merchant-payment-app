<?php

namespace App\Payments;

class FakeStripeProvider implements PaymentProviderInterface
{
    public function charge(
        $cardNumber,
        $expiry,
        $cvv,
        $amountCents,
        $description
    ) {
        $cardNumber = str_replace(' ', '', $cardNumber);

        if ($cardNumber === '4000000000000002') {
            return [
                'success' => false,
                'provider_transaction_id' => null,
                'status' => 'failed',
                'message' => 'Card declined',
            ];
        }

        return [
            'success' => true,
            'provider_transaction_id' => 'fake_stripe_' . bin2hex(random_bytes(8)),
            'status' => 'succeeded',
            'message' => null,
        ];
    }
}