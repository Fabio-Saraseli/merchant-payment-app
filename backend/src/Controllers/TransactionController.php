<?php

namespace App\Controllers;

use App\Http\Request;

class TransactionController
{
    private $transactionRepository;
    private $bearerAuthenticator;
    private $view;
    private $request;

    public function __construct(
        $transactionRepository,
        $bearerAuthenticator,
        $view,
        $request = null
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->bearerAuthenticator = $bearerAuthenticator;
        $this->view = $view;
        $this->request = $request ?: new Request();
    }

    public function index()
    {
        $merchant = $this->bearerAuthenticator->authenticate();

        if (!$merchant) {
            return $this->view->render([
                'message' => 'Unauthorized',
            ], 401);
        }

        $fromDate = $this->request->query('from');
        $toDate = $this->request->query('to');

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
                'created_at' => $transaction->getCreatedAtIso(),
            ];
        }

        return $this->view->render([
            'transactions' => $data,
        ]);
    }
}
