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
        $transactionId = bin2hex(random_bytes(16));
        $createdAt = gmdate('Y-m-d H:i:s');

        $statement = $this->connection->prepare(
            'INSERT INTO transactions (
                id,
                merchant_id,
                payment_provider,
                provider_transaction_id,
                amount_cents,
                currency,
                description,
                card_last_four,
                status,
                created_at
            ) VALUES (
                :id,
                :merchant_id,
                :payment_provider,
                :provider_transaction_id,
                :amount_cents,
                :currency,
                :description,
                :card_last_four,
                :status,
                :created_at
            )'
        );

        $statement->execute([
            'id' => $transactionId,
            'merchant_id' => $transaction->getMerchantId(),
            'payment_provider' => $transaction->getPaymentProvider(),
            'provider_transaction_id' => $transaction->getProviderTransactionId(),
            'amount_cents' => $transaction->getAmountCents(),
            'currency' => $transaction->getCurrency(),
            'description' => $transaction->getDescription(),
            'card_last_four' => $transaction->getCardLastFour(),
            'status' => $transaction->getStatus(),
            'created_at' => $createdAt,
        ]);

        return new Transaction(
            $transactionId,
            $transaction->getMerchantId(),
            $transaction->getPaymentProvider(),
            $transaction->getProviderTransactionId(),
            $transaction->getAmountCents(),
            $transaction->getCurrency(),
            $transaction->getDescription(),
            $transaction->getCardLastFour(),
            $transaction->getStatus(),
            $createdAt
        );
    }

    public function findByMerchantAndDateRange(
        $merchantId,
        $fromDate = null,
        $toDate = null
    ) {
        $query = '
            SELECT
                id,
                merchant_id,
                payment_provider,
                provider_transaction_id,
                amount_cents,
                currency,
                description,
                card_last_four,
                status,
                created_at
            FROM transactions
            WHERE merchant_id = :merchant_id
        ';

        $parameters = [
            'merchant_id' => $merchantId,
        ];

        if ($fromDate) {
            $query .= ' AND created_at >= :from_date';
            $parameters['from_date'] = $fromDate . ' 00:00:00';
        }

        if ($toDate) {
            $nextDay = date(
                'Y-m-d',
                strtotime($toDate . ' +1 day')
            );

            $query .= ' AND created_at < :to_date';
            $parameters['to_date'] = $nextDay . ' 00:00:00';
        }

        $query .= ' ORDER BY created_at DESC';

        $statement = $this->connection->prepare($query);
        $statement->execute($parameters);

        $transactions = [];

        while ($transactionData = $statement->fetch()) {
            $transactions[] = new Transaction(
                $transactionData['id'],
                $transactionData['merchant_id'],
                $transactionData['payment_provider'],
                $transactionData['provider_transaction_id'],
                $transactionData['amount_cents'],
                $transactionData['currency'],
                $transactionData['description'],
                $transactionData['card_last_four'],
                $transactionData['status'],
                $transactionData['created_at']
            );
        }

        return $transactions;
    }
}
