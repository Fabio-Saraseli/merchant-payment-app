<?php

namespace App\Controllers;

class TransactionController
{
    private $transactionRepository;
    private $bearerAuthenticator;
    private $view;

    public function __construct(
        $transactionRepository,
        $bearerAuthenticator,
        $view
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->bearerAuthenticator = $bearerAuthenticator;
        $this->view = $view;
    }

    public function index()
    {
        $merchant = $this->bearerAuthenticator->authenticate();

        if (!$merchant) {
            return $this->view->render([
                'message' => 'Unauthorized',
            ], 401);
        }

        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;

        $transactions = $this->transactionRepository
            ->findByMerchantAndDateRange(
                $merchant->getId(),
                $fromDate,
                $toDate
            );

        $data = [];

        foreach ($transactions as $transaction) {
            $data[] = [
                'id' => $transaction->getId(),
                'amount_cents' => $transaction->getAmountCents(),
                'currency' => $transaction->getCurrency(),
                'description' => $transaction->getDescription(),
                'card_last_four' => $transaction->getCardLastFour(),
                'status' => $transaction->getStatus(),
                'provider_transaction_id' => $transaction->getProviderTransactionId(),
                'created_at' => $transaction->getCreatedAt(),
            ];
        }

        return $this->view->render([
            'transactions' => $data,
        ]);
    }
}