<?php

namespace App\Payments;

class CardValidator
{
    public function isValidCardNumber($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (strlen($cardNumber) !== 16) {
            return false;
        }

        //To reject repeated digits of cards numbers like 0000...
        if (preg_match('/^(\d)\1{15}$/', $cardNumber)) {
            return false;
        }

        $sum = 0;
        $shouldDouble = false;

        for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
            $digit = $cardNumber[$i];

            if ($shouldDouble) {
                $digit *= 2;

                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $shouldDouble = !$shouldDouble;
        }

        return $sum % 10 === 0;
    }

    public function isValidExpiry($expiry)
    {
        if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiry, $matches)) {
            return false;
        }

        $expiryDate = '20' . $matches[2] . $matches[1];
        $currentDate = gmdate('Ym');

        return strcmp($expiryDate, $currentDate) >= 0;
    }

    public function isValidCvv($cvv)
    {
        return preg_match('/^\d{3,4}$/', $cvv) === 1;
    }
}
