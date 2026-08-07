<?php

namespace App\Services;

class AuthService
{
    private $merchantRepository;

    public function __construct($merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    public function authenticate($email, $password)
    {
        $merchant = $this->merchantRepository->findByEmail($email);

        if (!$merchant) {
            return null;
        }

        if (!password_verify($password, $merchant->getPasswordHash())) {
            return null;
        }

        return $merchant;
    }
}