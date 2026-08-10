<?php

namespace App\Services;

class PaymentNotificationService
{
    private $emailSender;

    public function __construct($emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function sendPaymentResult(
        $merchant,
        $transaction,
        $paymentMessage
    ) {
        $amount = number_format(
            $transaction->getAmountCents() / 100,
            2
        );

        if ($transaction->getStatus() === 'succeeded') {
            $subject = 'Payment successful';

            $message =
                "A payment of €{$amount} was successfully processed."
                . PHP_EOL . PHP_EOL
                . "Description: {$transaction->getDescription()}"
                . PHP_EOL
                . "Transaction ID: {$transaction->getId()}";
        } else {
            $subject = 'Payment failed';

            $message =
                "A payment of €{$amount} could not be processed."
                . PHP_EOL . PHP_EOL
                . "Description: {$transaction->getDescription()}"
                . PHP_EOL
                . "Reason: {$paymentMessage}"
                . PHP_EOL
                . "Transaction ID: {$transaction->getId()}";
        }

        try {
            $this->emailSender->send(
                $merchant->getEmail(),
                $subject,
                $message
            );
        } catch (\Throwable $exception) {
            error_log(
                'Payment notification email failed: '
                    . $exception->getMessage()
            );
        }
    }
}
