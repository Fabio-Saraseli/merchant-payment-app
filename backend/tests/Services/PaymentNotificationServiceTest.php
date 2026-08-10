<?php

use App\Models\Merchant;
use App\Models\Transaction;
use App\Notifications\EmailSenderInterface;
use App\Services\PaymentNotificationService;
use PHPUnit\Framework\TestCase;

class PaymentNotificationServiceTest extends TestCase
{
    public function testSuccessfulPaymentSendsSuccessEmail()
    {
        $emailSender = $this->createMock(
            EmailSenderInterface::class
        );

        $emailSender
            ->expects($this->once())
            ->method('send')
            ->with(
                'demo@merchant.test',
                'Payment successful',
                $this->stringContains(
                    'A payment of €10.00 was successfully processed.'
                )
            );

        $service = new PaymentNotificationService(
            $emailSender
        );

        $merchant = new Merchant(
            'merchant-1',
            'Demo Merchant',
            'demo@merchant.test',
            'password-hash',
            'fake_stripe',
            []
        );

        $transaction = new Transaction(
            'transaction-1',
            'merchant-1',
            'fake_stripe',
            'fake-provider-id',
            1000,
            'EUR',
            'Test payment',
            '4242',
            'succeeded',
            '2026-08-10 10:00:00'
        );

        $service->sendPaymentResult(
            $merchant,
            $transaction,
            'Payment successful'
        );
    }

    public function testFailedPaymentSendsFailureEmail()
    {
        $emailSender = $this->createMock(
            EmailSenderInterface::class
        );

        $emailSender
            ->expects($this->once())
            ->method('send')
            ->with(
                'demo@merchant.test',
                'Payment failed',
                $this->callback(function ($message) {
                    return str_contains(
                        $message,
                        'A payment of €20.00 could not be processed.'
                    ) && str_contains(
                        $message,
                        'Reason: Card declined'
                    );
                })
            );

        $service = new PaymentNotificationService(
            $emailSender
        );

        $merchant = new Merchant(
            'merchant-1',
            'Demo Merchant',
            'demo@merchant.test',
            'password-hash',
            'fake_stripe',
            []
        );

        $transaction = new Transaction(
            'transaction-2',
            'merchant-1',
            'fake_stripe',
            null,
            2000,
            'EUR',
            'Declined payment',
            '0002',
            'failed',
            '2026-08-10 10:00:00'
        );

        $service->sendPaymentResult(
            $merchant,
            $transaction,
            'Card declined'
        );
    }

    public function testEmailFailureDoesNotThrow()
    {
        $emailSender = $this->createMock(
            EmailSenderInterface::class
        );

        $emailSender
            ->method('send')
            ->willThrowException(
                new RuntimeException('SMTP unavailable')
            );

        $service = new PaymentNotificationService(
            $emailSender
        );

        $merchant = new Merchant(
            'merchant-1',
            'Demo Merchant',
            'demo@merchant.test',
            'password-hash',
            'fake_stripe',
            []
        );

        $transaction = new Transaction(
            'transaction-1',
            'merchant-1',
            'fake_stripe',
            'fake-provider-id',
            1000,
            'EUR',
            'Test payment',
            '4242',
            'succeeded',
            '2026-08-10 10:00:00'
        );

        $service->sendPaymentResult(
            $merchant,
            $transaction,
            'Payment successful'
        );

        $this->assertTrue(true);
    }
}
