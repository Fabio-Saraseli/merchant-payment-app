<?php

namespace App\Repositories;

use App\Models\ApiToken;

class PdoApiTokenRepository implements ApiTokenRepositoryInterface
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function create($apiToken)
    {
        $statement = $this->connection->prepare(
            'INSERT INTO api_tokens (
                merchant_id,
                token_hash,
                expires_at
            ) VALUES (
                :merchant_id,
                :token_hash,
                :expires_at
            )
            RETURNING id'
        );

        $statement->execute([
            'merchant_id' => $apiToken->getMerchantId(),
            'token_hash' => $apiToken->getTokenHash(),
            'expires_at' => $apiToken->getExpiresAt(),
        ]);

        $tokenId = $statement->fetchColumn();

        return new ApiToken(
            $tokenId,
            $apiToken->getMerchantId(),
            $apiToken->getTokenHash(),
            $apiToken->getExpiresAt()
        );
    }

    public function findByTokenHash($tokenHash)
    {
        $statement = $this->connection->prepare(
            'SELECT
                id,
                merchant_id,
                token_hash,
                expires_at
             FROM api_tokens
             WHERE token_hash = :token_hash
             LIMIT 1'
        );

        $statement->execute([
            'token_hash' => $tokenHash,
        ]);

        $tokenData = $statement->fetch();

        if (!$tokenData) {
            return null;
        }

        return new ApiToken(
            $tokenData['id'],
            $tokenData['merchant_id'],
            $tokenData['token_hash'],
            $tokenData['expires_at']
        );
    }
}