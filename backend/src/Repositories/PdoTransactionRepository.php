<?php

namespace App\Repositories;

use App\Models\Transaction;

class PdoTransactionRepository implements TransactionRepositoryInterface
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function create($transaction)
    {
        $statement = $this->connection->prepare(
            'INSERT INTO transactions (
                merchant_id,
                payment_provider,
                provider_transaction_id,
                amount_cents,
                currency,
                description,
                card_last_four,
                status
            ) VALUES (
                :merchant_id,
                :payment_provider,
                :provider_transaction_id,
                :amount_cents,
                :currency,
                :description,
                :card_last_four,
                :status
            )
            RETURNING id, created_at'
        );

        $statement->execute([
            'merchant_id' => $transaction->getMerchantId(),
            'payment_provider' => $transaction->getPaymentProvider(),
            'provider_transaction_id' => $transaction->getProviderTransactionId(),
            'amount_cents' => $transaction->getAmountCents(),
            'currency' => $transaction->getCurrency(),
            'description' => $transaction->getDescription(),
            'card_last_four' => $transaction->getCardLastFour(),
            'status' => $transaction->getStatus(),
        ]);

        $transactionData = $statement->fetch();

        return new Transaction(
            $transactionData['id'],
            $transaction->getMerchantId(),
            $transaction->getPaymentProvider(),
            $transaction->getProviderTransactionId(),
            $transaction->getAmountCents(),
            $transaction->getCurrency(),
            $transaction->getDescription(),
            $transaction->getCardLastFour(),
            $transaction->getStatus(),
            $transactionData['created_at']
        );
    }
}
