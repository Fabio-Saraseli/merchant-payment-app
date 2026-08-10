<?php

namespace App\Controllers;

use App\Http\Request;

class PaymentController
{
    private $paymentService;
    private $bearerAuthenticator;
    private $view;
    private $cardValidator;
    private $request;

    public function __construct(
        $paymentService,
        $bearerAuthenticator,
        $view,
        $cardValidator,
        $request = null
    ) {
        $this->paymentService = $paymentService;
        $this->bearerAuthenticator = $bearerAuthenticator;
        $this->view = $view;
        $this->cardValidator = $cardValidator;
        $this->request = $request ?: new Request();
    }

    public function charge()
    {
        $merchant = $this->bearerAuthenticator->authenticate();

        if (!$merchant) {
            return $this->view->render([
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = $this->request->json();

        if (!is_array($data)) {
            return $this->view->render([
                'message' => 'Invalid request body',
            ], 400);
        }

        $cardNumber = trim($data['card_number'] ?? '');
        $expiry = trim($data['expiry'] ?? '');
        $cvv = trim($data['cvv'] ?? '');
        $description = trim($data['description'] ?? '');
        $amount = $data['amount'] ?? null;

        if (
            !$cardNumber ||
            !$expiry ||
            !$cvv ||
            !$description ||
            $amount === null
        ) {
            return $this->view->render([
                'message' => 'All fields are required',
            ], 422);
        }

        if (!$this->cardValidator->isValidCardNumber($cardNumber)) {
            return $this->view->render([
                'message' => 'Invalid card number',
            ], 422);
        }

        if (!$this->cardValidator->isValidExpiry($expiry)) {
            return $this->view->render([
                'message' => 'Invalid or expired card',
            ], 422);
        }

        if (!$this->cardValidator->isValidCvv($cvv)) {
            return $this->view->render([
                'message' => 'CVV must contain 3 or 4 digits',
            ], 422);
        }

        if (!is_numeric($amount) || $amount <= 0) {
            return $this->view->render([
                'message' => 'Amount must be greater than 0',
            ], 422);
        }

        $amountCents = (int) round($amount * 100);

        $result = $this->paymentService->charge(
            $merchant,
            $cardNumber,
            $expiry,
            $cvv,
            $amountCents,
            $description
        );

        $transaction = $result['transaction'];

        $response = [
            'message' => $result['message'],
            'transaction' => $transaction ? [
                'id' => $transaction->getId(),
                'amount_cents' => $transaction->getAmountCents(),
                'currency' => $transaction->getCurrency(),
                'description' => $transaction->getDescription(),
                'card_last_four' => $transaction->getCardLastFour(),
                'status' => $transaction->getStatus(),
                'provider_transaction_id' => $transaction->getProviderTransactionId(),
                'created_at' => $transaction->getCreatedAt(),
            ] : null,
        ];

        return $this->view->render(
            $response,
            $result['success'] ? 201 : 402
        );
    }
}
