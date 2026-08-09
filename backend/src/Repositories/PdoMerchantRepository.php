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
                payment_provider,
                payment_provider_config
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
            $merchantData['payment_provider'],
            json_decode($merchantData['payment_provider_config'], true)
        );
    }
    public function create($merchant)
    {
        $statement = $this->connection->prepare(
            'INSERT INTO merchants (
                name,
                email,
                password_hash,
                payment_provider,
                payment_provider_config
            ) VALUES (
                :name,
                :email,
                :password_hash,
                :payment_provider,
                :payment_provider_config
            )
            RETURNING id'
        );

        $statement->execute([
            'name' => $merchant->getName(),
            'email' => $merchant->getEmail(),
            'password_hash' => $merchant->getPasswordHash(),
            'payment_provider' => $merchant->getPaymentProvider(),
            'payment_provider_config' => json_encode(
                $merchant->getPaymentProviderConfig()
            ),
        ]);

        $merchantId = $statement->fetchColumn();

        return new Merchant(
            $merchantId,
            $merchant->getName(),
            $merchant->getEmail(),
            $merchant->getPasswordHash(),
            $merchant->getPaymentProvider(),
            $merchant->getPaymentProviderConfig()
        );
    }

    public function findById($id)
    {
        $statement = $this->connection->prepare(
            'SELECT
                id,
                name,
                email,
                password_hash,
                payment_provider,
                payment_provider_config
             FROM merchants
             WHERE id = :id
             LIMIT 1'
        );

        $statement->execute([
            'id' => $id,
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
            $merchantData['payment_provider'],
            json_decode($merchantData['payment_provider_config'], true)
        );
    }
}
