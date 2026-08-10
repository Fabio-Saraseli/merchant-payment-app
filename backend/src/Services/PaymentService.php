<?php

namespace App\Services;

use App\Models\Transaction;

class PaymentService
{
    private $transactionRepository;
    private $paymentProviderResolver;
    private $paymentNotificationService;

    public function __construct(
        $transactionRepository,
        $paymentProviderResolver,
        $paymentNotificationService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->paymentProviderResolver = $paymentProviderResolver;
        $this->paymentNotificationService = $paymentNotificationService;
    }

    public function charge(
        $merchant,
        $cardNumber,
        $expiry,
        $cvv,
        $amountCents,
        $description
    ) {
        $provider = $this->paymentProviderResolver->resolve(
            $merchant->getPaymentProvider()
        );

        if (!$provider) {
            return [
                'success' => false,
                'message' => 'Payment provider is not available',
                'transaction' => null,
            ];
        }

        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        $providerResult = $provider->charge(
            $merchant->getPaymentProviderConfig(),
            $cardNumber,
            $expiry,
            $cvv,
            $amountCents,
            $description
        );

        $transaction = new Transaction(
            null,
            $merchant->getId(),
            $merchant->getPaymentProvider(),
            $providerResult['provider_transaction_id'],
            $amountCents,
            'EUR',
            $description,
            substr($cardNumber, -4),
            $providerResult['status']
        );

        $transaction = $this->transactionRepository->create(
            $transaction
        );

        $this->paymentNotificationService->sendPaymentResult(
            $merchant,
            $transaction,
            $providerResult['message']
        );

        return [
            'success' => $providerResult['success'],
            'message' => $providerResult['message'],
            'transaction' => $transaction,
        ];
    }
}
