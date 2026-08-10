<?php

use App\Controllers\AuthController;
use App\Models\Merchant;
use PHPUnit\Framework\TestCase;

class AuthControllerTest extends TestCase
{
    private $authService;
    private $view;
    private $request;
    private $controller;

    protected function setUp(): void
    {
        $this->authService = new AuthControllerTestAuthService();
        $this->view = new AuthControllerTestView();
        $this->request = new AuthControllerTestRequest();

        $this->controller = new AuthController(
            $this->authService,
            $this->view,
            $this->request
        );
    }

    public function testInvalidRequestBodyReturns400()
    {
        $this->request->body = null;

        $this->controller->login();

        $this->assertSame(
            400,
            $this->view->status
        );

        $this->assertSame(
            'Invalid request body',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->authService->loginCalled
        );
    }

    public function testMissingCredentialsReturns422()
    {
        $this->request->body = [
            'email' => 'demo@merchant.test',
        ];

        $this->controller->login();

        $this->assertSame(
            422,
            $this->view->status
        );

        $this->assertSame(
            'Email and password are required',
            $this->view->data['message']
        );

        $this->assertFalse(
            $this->authService->loginCalled
        );
    }

    public function testInvalidCredentialsReturns401()
    {
        $this->request->body = [
            'email' => 'demo@merchant.test',
            'password' => 'wrong-password',
        ];

        $this->authService->result = null;

        $this->controller->login();

        $this->assertSame(
            401,
            $this->view->status
        );

        $this->assertSame(
            'Invalid credentials',
            $this->view->data['message']
        );

        $this->assertTrue(
            $this->authService->loginCalled
        );

        $this->assertSame(
            'demo@merchant.test',
            $this->authService->email
        );

        $this->assertSame(
            'wrong-password',
            $this->authService->password
        );
    }

    public function testValidCredentialsReturnAuthenticationData()
    {
        $merchant = new Merchant(
            'merchant-1',
            'Demo Merchant',
            'demo@merchant.test',
            'password-hash',
            'fake_stripe',
            [
                'account_id' => 'fake_account_demo',
            ]
        );

        $this->request->body = [
            'email' => 'demo@merchant.test',
            'password' => 'password123',
        ];

        $this->authService->result = [
            'token' => 'test-token',
            'expires_at' => '2026-08-10T18:00:00Z',
            'merchant' => $merchant,
        ];

        $this->controller->login();

        $this->assertSame(
            200,
            $this->view->status
        );

        $this->assertSame(
            'test-token',
            $this->view->data['token']
        );

        $this->assertSame(
            '2026-08-10T18:00:00Z',
            $this->view->data['expires_at']
        );

        $this->assertSame(
            [
                'id' => 'merchant-1',
                'name' => 'Demo Merchant',
                'email' => 'demo@merchant.test',
            ],
            $this->view->data['merchant']
        );
    }
}

class AuthControllerTestRequest
{
    public $body;

    public function json()
    {
        return $this->body;
    }
}

class AuthControllerTestAuthService
{
    public $result;
    public $loginCalled = false;
    public $email;
    public $password;

    public function login($email, $password)
    {
        $this->loginCalled = true;
        $this->email = $email;
        $this->password = $password;

        return $this->result;
    }
}

class AuthControllerTestView
{
    public $data;
    public $status = 200;

    public function render($data, $status = 200)
    {
        $this->data = $data;
        $this->status = $status;
    }
}