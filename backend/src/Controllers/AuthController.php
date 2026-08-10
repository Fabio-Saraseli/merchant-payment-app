<?php

namespace App\Controllers;
use App\Http\Request;

class AuthController
{
    private $authService;
    private $view;
    private $request;

    public function __construct(
        $authService,
        $view,
        $request = null
    ) {
        $this->authService = $authService;
        $this->view = $view;
        $this->request = $request ?: new Request();
    }

    public function login()
    {
        $data = $this->request->json();

        if (!is_array($data)) {
            $this->view->render([
                'message' => 'Invalid request body',
            ], 400);

            return;
        }

        if (empty($data['email']) || empty($data['password'])) {
            $this->view->render([
                'message' => 'Email and password are required',
            ], 422);

            return;
        }

        $result = $this->authService->login(
            $data['email'],
            $data['password']
        );

        if (!$result) {
            $this->view->render([
                'message' => 'Invalid credentials',
            ], 401);

            return;
        }

        $merchant = $result['merchant'];

        $this->view->render([
            'token' => $result['token'],
            'expires_at' => $result['expires_at'],
            'merchant' => [
                'id' => $merchant->getId(),
                'name' => $merchant->getName(),
                'email' => $merchant->getEmail(),
            ],
        ]);
    }
}
