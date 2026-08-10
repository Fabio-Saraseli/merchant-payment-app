<?php

use App\Models\Merchant;
use App\Models\Transaction;
use App\Payments\PaymentProviderInterface;
use App\Payments\PaymentProviderResolver;
use App\Repositories\TransactionRepositoryInterface;
use App\Services\PaymentNotificationService;
use App\Services\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    private $transactionRepository;
    private $paymentProviderResolver;
    private $paymentNotificationService;
    private $provider;
    private $service;
    private $merchant;

    protected function setUp(): void
    {
        $this->transactionRepository = $this->createMock(
            TransactionRepositoryInterface::class
        );

        $this->paymentProviderResolver = $this->createMock(
            PaymentProviderResolver::class
        );

        $this->paymentNotificationService = $this->createMock(
            PaymentNotificationService::class
        );

        $this->provider = $this->createMock(
            PaymentProviderInterface::class
        );

        $this->service = new PaymentService(
            $this->transactionRepository,
            $this->paymentProviderResolver,
            $this->paymentNotificationService
        );

        $this->merchant = new Merchant(
            'merchant-1',
            'Demo Merchant',
            'demo@merchant.test',
            'password-hash',
            'fake_stripe',
            [
                'account_id' => 'fake_account_demo',
            ]
        );
    }

    public function testSuccessfulPaymentIsPersistedAndNotificationIsSent()
    {
        $this->paymentProviderResolver
            ->expects($this->once())
            ->method('resolve')
            ->with('fake_stripe')
            ->willReturn($this->provider);

        $this->provider
            ->expects($this->once())
            ->method('charge')
            ->with(
                [
                    'account_id' => 'fake_account_demo',
                ],
                '4242424242424242',
                '12/28',
                '123',
                1000,
                'Test payment'
            )
            ->willReturn([
                'success' => true,
                'message' => 'Payment successful',
                'status' => 'succeeded',
                'provider_transaction_id' => 'fake_account_demo_123',
            ]);

        $storedTransaction = new Transaction(
            'transaction-1',
            'merchant-1',
            'fake_stripe',
            'fake_account_demo_123',
            1000,
            'EUR',
            'Test payment',
            '4242',
            'succeeded',
            '2026-08-10 10:00:00'
        );

        $this->transactionRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($transaction) {
                    return
                        $transaction->getMerchantId() === 'merchant-1'
                        && $transaction->getAmountCents() === 1000
                        && $transaction->getCurrency() === 'EUR'
                        && $transaction->getDescription() === 'Test payment'
                        && $transaction->getCardLastFour() === '4242'
                        && $transaction->getStatus() === 'succeeded';
                })
            )
            ->willReturn($storedTransaction);

        $this->paymentNotificationService
            ->expects($this->once())
            ->method('sendPaymentResult')
            ->with(
                $this->merchant,
                $storedTransaction,
                'Payment successful'
            );

        $result = $this->service->charge(
            $this->merchant,
            '4242 4242 4242 4242',
            '12/28',
            '123',
            1000,
            'Test payment'
        );

        $this->assertTrue($result['success']);
        $this->assertSame(
            'Payment successful',
            $result['message']
        );
        $this->assertSame(
            $storedTransaction,
            $result['transaction']
        );
    }

    public function testDeclinedPaymentIsStillPersistedAndNotificationIsSent()
    {
        $this->paymentProviderResolver
            ->method('resolve')
            ->willReturn($this->provider);

        $this->provider
            ->method('charge')
            ->willReturn([
                'success' => false,
                'message' => 'Card declined',
                'status' => 'failed',
                'provider_transaction_id' => null,
            ]);

        $storedTransaction = new Transaction(
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

        $this->transactionRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($transaction) {
                    return
                        $transaction->getStatus() === 'failed'
                        && $transaction->getCardLastFour() === '0002'
                        && $transaction->getAmountCents() === 2000;
                })
            )
            ->willReturn($storedTransaction);

        $this->paymentNotificationService
            ->expects($this->once())
            ->method('sendPaymentResult')
            ->with(
                $this->merchant,
                $storedTransaction,
                'Card declined'
            );

        $result = $this->service->charge(
            $this->merchant,
            '4000 0000 0000 0002',
            '12/28',
            '123',
            2000,
            'Declined payment'
        );

        $this->assertFalse($result['success']);
        $this->assertSame('Card declined', $result['message']);
        $this->assertSame(
            $storedTransaction,
            $result['transaction']
        );
    }

    public function testUnavailableProviderDoesNotCreateTransaction()
    {
        $this->paymentProviderResolver
            ->expects($this->once())
            ->method('resolve')
            ->with('fake_stripe')
            ->willReturn(null);

        $this->transactionRepository
            ->expects($this->never())
            ->method('create');

        $this->paymentNotificationService
            ->expects($this->never())
            ->method('sendPaymentResult');

        $result = $this->service->charge(
            $this->merchant,
            '4242424242424242',
            '12/28',
            '123',
            1000,
            'Test payment'
        );

        $this->assertFalse($result['success']);
        $this->assertSame(
            'Payment provider is not available',
            $result['message']
        );
        $this->assertNull($result['transaction']);
    }
}
