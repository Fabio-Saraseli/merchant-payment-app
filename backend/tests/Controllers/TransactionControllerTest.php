<?php

use App\Controllers\TransactionController;
use App\Models\Merchant;
use App\Models\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionControllerTest extends TestCase
{
    private $transactionRepository;
    private $bearerAuthenticator;
    private $view;
    private $request;
    private $controller;
    private $merchant;

    protected function setUp(): void
    {
        $this->transactionRepository =
            new TransactionControllerTestRepository();

        $this->bearerAuthenticator =
            new TransactionControllerTestAuthenticator();

        $this->view =
            new TransactionControllerTestView();

        $this->request =
            new TransactionControllerTestRequest();

        $this->controller = new TransactionController(
            $this->transactionRepository,
            $this->bearerAuthenticator,
            $this->view,
            $this->request
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

        $this->bearerAuthenticator->merchant =
            $this->merchant;
    }

    public function testUnauthorizedRequestReturns401()
    {
        $this->bearerAuthenticator->merchant = null;

        $this->controller->index();

        $this->assertSame(
            401,
            $this->view->status
        );

        $this->assertSame(
            'Unauthorized',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->transactionRepository->findCalled
        );
    }

    public function testTransactionsAreReturnedForAuthenticatedMerchant()
    {
        $transaction = new Transaction(
            'transaction-1',
            'merchant-1',
            'fake_stripe',
            'provider-123',
            2550,
            'EUR',
            'Test payment',
            '4242',
            'succeeded',
            '2026-08-10 12:00:00'
        );

        $this->transactionRepository->transactions = [
            $transaction,
        ];

        $this->controller->index();

        $this->assertSame(
            200,
            $this->view->status
        );

        $this->assertTrue(
            $this->transactionRepository->findCalled
        );

        $this->assertSame(
            'merchant-1',
            $this->transactionRepository->merchantId
        );

        $this->assertNull(
            $this->transactionRepository->fromDate
        );

        $this->assertNull(
            $this->transactionRepository->toDate
        );

        $this->assertCount(
            1,
            $this->view->data['transactions']
        );

        $this->assertSame(
            'transaction-1',
            $this->view->data['transactions'][0]['id']
        );

        $this->assertSame(
            2550,
            $this->view->data['transactions'][0]['amount_cents']
        );

        $this->assertSame(
            'Test payment',
            $this->view->data['transactions'][0]['description']
        );

        $this->assertSame(
            '4242',
            $this->view->data['transactions'][0]['card_last_four']
        );

        $this->assertSame(
            'succeeded',
            $this->view->data['transactions'][0]['status']
        );
    }

    public function testDateFiltersArePassedToRepository()
    {
        $this->request->queryParams = [
            'from' => '2026-08-01',
            'to' => '2026-08-10',
        ];

        $this->transactionRepository->transactions = [];

        $this->controller->index();

        $this->assertSame(
            'merchant-1',
            $this->transactionRepository->merchantId
        );

        $this->assertSame(
            '2026-08-01',
            $this->transactionRepository->fromDate
        );

        $this->assertSame(
            '2026-08-10',
            $this->transactionRepository->toDate
        );
    }

    public function testEmptyTransactionListReturnsEmptyArray()
    {
        $this->transactionRepository->transactions = [];

        $this->controller->index();

        $this->assertSame(
            200,
            $this->view->status
        );

        $this->assertSame(
            [],
            $this->view->data['transactions']
        );
    }
}

class TransactionControllerTestRequest
{
    public $queryParams = [];

    public function query($name)
    {
        return $this->queryParams[$name] ?? null;
    }
}

class TransactionControllerTestAuthenticator
{
    public $merchant;

    public function authenticate()
    {
        return $this->merchant;
    }
}

class TransactionControllerTestRepository
{
    public $transactions = [];
    public $findCalled = false;
    public $merchantId;
    public $fromDate;
    public $toDate;

    public function findByMerchantAndDateRange(
        $merchantId,
        $fromDate = null,
        $toDate = null
    ) {
        $this->findCalled = true;
        $this->merchantId = $merchantId;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;

        return $this->transactions;
    }
}

class TransactionControllerTestView
{
    public $data;
    public $status = 200;

    public function render($data, $status = 200)
    {
        $this->data = $data;
        $this->status = $status;
    }
}