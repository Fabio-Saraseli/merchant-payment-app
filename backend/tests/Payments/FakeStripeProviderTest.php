<?php

use App\Payments\FakeStripeProvider;
use PHPUnit\Framework\TestCase;

class FakeStripeProviderTest extends TestCase
{
    private $provider;

    protected function setUp(): void
    {
        $this->provider = new FakeStripeProvider();
    }

    public function testSuccessfulPayment()
    {
        $result = $this->provider->charge(
            [
                'account_id' => 'fake_account_test',
            ],
            '4242424242424242',
            '12/28',
            '123',
            1000,
            'Test payment'
        );

        $this->assertTrue($result['success']);
        $this->assertSame('succeeded', $result['status']);
        $this->assertNotNull(
            $result['provider_transaction_id']
        );
    }

    public function testTransactionIdContainsMerchantAccount()
    {
        $result = $this->provider->charge(
            [
                'account_id' => 'fake_account_test',
            ],
            '4242424242424242',
            '12/28',
            '123',
            1000,
            'Test payment'
        );

        $this->assertStringStartsWith(
            'fake_account_test_',
            $result['provider_transaction_id']
        );
    }

    public function testDeclinedCardFails()
    {
        $result = $this->provider->charge(
            [
                'account_id' => 'fake_account_test',
            ],
            '4000000000000002',
            '12/28',
            '123',
            1000,
            'Declined payment'
        );

        $this->assertFalse($result['success']);
        $this->assertSame('failed', $result['status']);
        $this->assertNull(
            $result['provider_transaction_id']
        );
    }

    public function testMissingProviderConfigurationFails()
    {
        $result = $this->provider->charge(
            [],
            '4242424242424242',
            '12/28',
            '123',
            1000,
            'Test payment'
        );

        $this->assertFalse($result['success']);
        $this->assertSame('failed', $result['status']);
        $this->assertNull(
            $result['provider_transaction_id']
        );
    }
}
