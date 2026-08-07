<?php

namespace App\Models;

class Transaction
{
    private $id;
    private $merchantId;
    private $paymentProvider;
    private $providerTransactionId;
    private $amountCents;
    private $currency;
    private $description;
    private $cardLastFour;
    private $status;
    private $createdAt;

    public function __construct(
        $id,
        $merchantId,
        $paymentProvider,
        $providerTransactionId,
        $amountCents,
        $currency,
        $description,
        $cardLastFour,
        $status,
        $createdAt = null
    ) {
        $this->id = $id;
        $this->merchantId = $merchantId;
        $this->paymentProvider = $paymentProvider;
        $this->providerTransactionId = $providerTransactionId;
        $this->amountCents = $amountCents;
        $this->currency = $currency;
        $this->description = $description;
        $this->cardLastFour = $cardLastFour;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMerchantId()
    {
        return $this->merchantId;
    }

    public function getPaymentProvider()
    {
        return $this->paymentProvider;
    }

    public function getProviderTransactionId()
    {
        return $this->providerTransactionId;
    }

    public function getAmountCents()
    {
        return $this->amountCents;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCardLastFour()
    {
        return $this->cardLastFour;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}