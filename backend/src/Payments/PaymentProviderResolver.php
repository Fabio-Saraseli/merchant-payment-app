<?php

namespace App\Payments;

class PaymentProviderResolver
{
    private $providers = [];
    public function __construct($providers)
    {
        foreach ($providers as $provider) {
            $this->providers[$provider->getName()] = $provider;
        }
    }
    public function resolve($name)
    {
        return $this->providers[$name] ?? null;
    }
}
