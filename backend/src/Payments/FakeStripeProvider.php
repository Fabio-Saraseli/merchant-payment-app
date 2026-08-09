<?php

namespace App\Payments;

class FakeStripeProvider implements PaymentProviderInterface
{
    public function charge(
        $config,
        $cardNumber,
        $expiry,
        $cvv,
        $amountCents,
        $description
    ) {
        if (empty($config['account_id'])) {
            return [
                'success' => false,
                'provider_transaction_id' => null,
                'status' => 'failed',
                'message' => 'Payment provider configuration is missing',
            ];
        }

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
            'provider_transaction_id' =>
            $config['account_id'] . '_' . bin2hex(random_bytes(8)),
            'status' => 'succeeded',
            'message' => null,
        ];
    }
}
