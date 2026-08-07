<?php

namespace App\Services;

use App\Models\ApiToken;

class AuthService
{
    private $merchantRepository;
    private $apiTokenRepository;

    public function __construct($merchantRepository, $apiTokenRepository)
    {
        $this->merchantRepository = $merchantRepository;
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function login($email, $password)
    {
        $merchant = $this->merchantRepository->findByEmail($email);

        if (!$merchant) {
            return null;
        }

        if (!password_verify($password, $merchant->getPasswordHash())) {
            return null;
        }

        $plainToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $plainToken);

        $expiresAt = gmdate('Y-m-d H:i:s', time() + 3600); //After 1 hour

        $apiToken = new ApiToken(
            null,
            $merchant->getId(),
            $tokenHash,
            $expiresAt
        );

        $this->apiTokenRepository->create($apiToken);

        return [
            'token' => $plainToken,
            'merchant' => $merchant,
        ];
    }
    public function authenticateToken($plainToken)
    {
        if (!$plainToken) {
            return null;
        }

        $tokenHash = hash('sha256', $plainToken);

        $apiToken = $this->apiTokenRepository->findByTokenHash($tokenHash);

        if (!$apiToken) {
            return null;
        }

        if (strtotime($apiToken->getExpiresAt()) <= time()) {
            return null;
        }

        return $this->merchantRepository->findById(
            $apiToken->getMerchantId()
        );
    }
}
