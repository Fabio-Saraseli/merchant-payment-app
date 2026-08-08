<?php

namespace App\Repositories;

interface TransactionRepositoryInterface
{
    public function create($transaction);
    public function findByMerchantAndDateRange(
        $merchantId,
        $fromDate = null,
        $toDate = null
    );
}
