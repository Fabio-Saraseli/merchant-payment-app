<?php

namespace App\Models;

class Merchant
{
    private $id;
    private $name;
    private $email;
    private $passwordHash;
    private $paymentProvider;

    public function __construct(
        $id,
        $name,
        $email,
        $passwordHash,
        $paymentProvider
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->paymentProvider = $paymentProvider;
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
}