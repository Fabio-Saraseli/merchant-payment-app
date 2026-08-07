<?php

namespace App\Payments;

class PaymentProviderResolver
{
    private $providers;
    public function __construct($providers)
    {
        $this->providers = $providers;
    }
    public function resolve($providerName)
    {
        return $this->providers[$providerName] ?? null;
    }
}
