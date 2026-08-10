<?php

namespace App\Models;

class ApiToken
{
    private $id;
    private $merchantId;
    private $tokenHash;
    private $expiresAt;

    public function __construct(
        $id,
        $merchantId,
        $tokenHash,
        $expiresAt
    ) {
        $this->id = $id;
        $this->merchantId = $merchantId;
        $this->tokenHash = $tokenHash;
        $this->expiresAt = $expiresAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMerchantId()
    {
        return $this->merchantId;
    }

    public function getTokenHash()
    {
        return $this->tokenHash;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
