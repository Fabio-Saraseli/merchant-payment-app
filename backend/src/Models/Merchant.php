<?php

namespace App\Models;

class Merchant
{
    private $id;
    private $name;
    private $email;
    private $passwordHash;
    private $paymentProvider;
    private $paymentProviderConfig;

    public function __construct(
        $id,
        $name,
        $email,
        $passwordHash,
        $paymentProvider,
        $paymentProviderConfig = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->paymentProvider = $paymentProvider;
        $this->paymentProviderConfig = $paymentProviderConfig;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function getPaymentProvider()
    {
        return $this->paymentProvider;
    }
    public function getPaymentProviderConfig()
    {
        return $this->paymentProviderConfig;
    }
}
