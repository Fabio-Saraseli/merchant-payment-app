<?php

namespace App\Repositories;

use App\Models\Merchant;

class PdoMerchantRepository implements MerchantRepositoryInterface
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function findByEmail($email)
    {
        $statement = $this->connection->prepare(
            'SELECT
                id,
                name,
                email,
                password_hash,
                payment_provider
             FROM merchants
             WHERE email = :email
             LIMIT 1'
        );

        $statement->execute([
            'email' => $email,
        ]);

        $merchantData = $statement->fetch();

        if (!$merchantData) {
            return null;
        }

        return new Merchant(
            $merchantData['id'],
            $merchantData['name'],
            $merchantData['email'],
            $merchantData['password_hash'],
            $merchantData['payment_provider']
        );
    }
}
