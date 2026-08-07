<?php

namespace App\Auth;

class BearerAuthenticator
{
    private $authService;

    public function __construct($authService)
    {
        $this->authService = $authService;
    }

    public function authenticate()
    {
        $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!$authorization) {
            return null;
        }

        if (!preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            return null;
        }

        $token = trim($matches[1]);

        if (!$token) {
            return null;
        }

        return $this->authService->authenticateToken($token);
    }
}
