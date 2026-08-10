<?php

use App\Controllers\PaymentController;
use App\Models\Merchant;
use App\Models\Transaction;
use PHPUnit\Framework\TestCase;

class PaymentControllerTest extends TestCase
{
    private $paymentService;
    private $bearerAuthenticator;
    private $view;
    private $cardValidator;
    private $request;
    private $controller;
    private $merchant;

    protected function setUp(): void
    {
        $this->paymentService = new PaymentControllerTestPaymentService();
        $this->bearerAuthenticator = new PaymentControllerTestAuthenticator();
        $this->view = new PaymentControllerTestView();
        $this->cardValidator = new PaymentControllerTestCardValidator();
        $this->request = new PaymentControllerTestRequest();

        $this->controller = new PaymentController(
            $this->paymentService,
            $this->bearerAuthenticator,
            $this->view,
            $this->cardValidator,
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

        $this->bearerAuthenticator->merchant = $this->merchant;

        $this->request->body = [
            'card_number' => '4242424242424242',
            'expiry' => '12/28',
            'cvv' => '123',
            'amount' => 25.50,
            'description' => 'Test payment',
        ];
    }

    public function testUnauthorizedRequestReturns401()
    {
        $this->bearerAuthenticator->merchant = null;

        $this->controller->charge();

        $this->assertSame(
            401,
            $this->view->status
        );

        $this->assertSame(
            'Unauthorized',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->paymentService->chargeCalled
        );
    }

    public function testInvalidRequestBodyReturns400()
    {
        $this->request->body = null;

        $this->controller->charge();

        $this->assertSame(
            400,
            $this->view->status
        );

        $this->assertSame(
            'Invalid request body',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->paymentService->chargeCalled
        );
    }

    public function testMissingFieldsReturn422()
    {
        $this->request->body = [
            'card_number' => '4242424242424242',
            'expiry' => '12/28',
        ];

        $this->controller->charge();

        $this->assertSame(
            422,
            $this->view->status
        );

        $this->assertSame(
            'All fields are required',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->paymentService->chargeCalled
        );
    }

    public function testInvalidCardNumberReturns422()
    {
        $this->cardValidator->validCardNumber = false;

        $this->controller->charge();

        $this->assertSame(
            422,
            $this->view->status
        );

        $this->assertSame(
            'Invalid card number',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->paymentService->chargeCalled
        );
    }

    public function testInvalidExpiryReturns422()
    {
        $this->cardValidator->validExpiry = false;

        $this->controller->charge();

        $this->assertSame(
            422,
            $this->view->status
        );

        $this->assertSame(
            'Invalid or expired card',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->paymentService->chargeCalled
        );
    }

    public function testInvalidCvvReturns422()
    {
        $this->cardValidator->validCvv = false;

        $this->controller->charge();

        $this->assertSame(
            422,
            $this->view->status
        );

        $this->assertSame(
            'CVV must contain 3 or 4 digits',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->paymentService->chargeCalled
        );
    }

    public function testInvalidAmountReturns422()
    {
        $this->request->body['amount'] = 0;

        $this->controller->charge();

        $this->assertSame(
            422,
            $this->view->status
        );

        $this->assertSame(
            'Amount must be greater than 0',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->paymentService->chargeCalled
        );
    }

    public function testSuccessfulPaymentReturns201()
    {
        $transaction = new Transaction(
            'transaction-1',
            'merchant-1',
            'fake_stripe',
            'fake_account_demo_123',
            2550,
            'EUR',
            'Test payment',
            '4242',
            'succeeded',
            '2026-08-10 12:00:00'
        );

        $this->paymentService->result = [
            'success' => true,
            'message' => 'Payment successful',
            'transaction' => $transaction,
        ];

        $this->controller->charge();

        $this->assertSame(
            201,
            $this->view->status
        );

        $this->assertSame(
            'Payment successful',
            $this->view->data['message']
        );

        $this->assertSame(
            'transaction-1',
            $this->view->data['transaction']['id']
        );

        $this->assertSame(
            2550,
            $this->view->data['transaction']['amount_cents']
        );

        $this->assertSame(
            'succeeded',
            $this->view->data['transaction']['status']
        );

        $this->assertTrue(
            $this->paymentService->chargeCalled
        );

        $this->assertSame(
            $this->merchant,
            $this->paymentService->merchant
        );

        $this->assertSame(
            '4242424242424242',
            $this->paymentService->cardNumber
        );

        $this->assertSame(
            '12/28',
            $this->paymentService->expiry
        );

        $this->assertSame(
            '123',
            $this->paymentService->cvv
        );

        $this->assertSame(
            2550,
            $this->paymentService->amountCents
        );

        $this->assertSame(
            'Test payment',
            $this->paymentService->description
        );
    }

    public function testDeclinedPaymentReturns402()
    {
        $transaction = new Transaction(
            'transaction-2',
            'merchant-1',
            'fake_stripe',
            null,
            2550,
            'EUR',
            'Test payment',
            '0002',
            'failed',
            '2026-08-10 12:00:00'
        );

        $this->paymentService->result = [
            'success' => false,
            'message' => 'Card declined',
            'transaction' => $transaction,
        ];

        $this->controller->charge();

        $this->assertSame(
            402,
            $this->view->status
        );

        $this->assertSame(
            'Card declined',
            $this->view->data['message']
        );

        $this->assertSame(
            'failed',
            $this->view->data['transaction']['status']
        );

        $this->assertTrue(
            $this->paymentService->chargeCalled
        );
    }
}

class PaymentControllerTestRequest
{
    public $body;

    public function json()
    {
        return $this->body;
    }
}

class PaymentControllerTestAuthenticator
{
    public $merchant;

    public function authenticate()
    {
        return $this->merchant;
    }
}

class PaymentControllerTestCardValidator
{
    public $validCardNumber = true;
    public $validExpiry = true;
    public $validCvv = true;

    public function isValidCardNumber($cardNumber)
    {
        return $this->validCardNumber;
    }

    public function isValidExpiry($expiry)
    {
        return $this->validExpiry;
    }

    public function isValidCvv($cvv)
    {
        return $this->validCvv;
    }
}

class PaymentControllerTestPaymentService
{
    public $result;
    public $chargeCalled = false;
    public $merchant;
    public $cardNumber;
    public $expiry;
    public $cvv;
    public $amountCents;
    public $description;

    public function charge(
        $merchant,
        $cardNumber,
        $expiry,
        $cvv,
        $amountCents,
        $description
    ) {
        $this->chargeCalled = true;
        $this->merchant = $merchant;
        $this->cardNumber = $cardNumber;
        $this->expiry = $expiry;
        $this->cvv = $cvv;
        $this->amountCents = $amountCents;
        $this->description = $description;

        return $this->result;
    }
}

class PaymentControllerTestView
{
    public $data;
    public $status = 200;

    public function render($data, $status = 200)
    {
        $this->data = $data;
        $this->status = $status;
    }
}
