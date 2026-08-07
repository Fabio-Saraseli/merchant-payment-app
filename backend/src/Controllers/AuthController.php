<?php

namespace App\Controllers;

class AuthController
{
    private $authService;
    private $view;

    public function __construct($authService, $view)
    {
        $this->authService = $authService;
        $this->view = $view;
    }

    public function login()
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!$data) {
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
            'merchant' => [
                'id' => $merchant->getId(),
                'name' => $merchant->getName(),
                'email' => $merchant->getEmail(),
            ],
        ]);
    }
}