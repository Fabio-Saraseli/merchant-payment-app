<?php

use App\Models\ApiToken;
use App\Repositories\PdoApiTokenRepository;
use PHPUnit\Framework\TestCase;

class PdoApiTokenRepositoryTest extends TestCase
{
    private $connection;
    private $repository;

    protected function setUp(): void
    {
        $dsn = getenv('TEST_DB_DSN');

        if (!$dsn) {
            $dsn = 'sqlite::memory:';
        }

        $username = getenv('TEST_DB_USER');
        $password = getenv('TEST_DB_PASSWORD');

        if ($username === false) {
            $username = null;
        }

        if ($password === false) {
            $password = null;
        }

        $this->connection = new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        if (
            $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME)
            === 'sqlite'
        ) {
            $this->connection->exec(
                'PRAGMA foreign_keys = ON'
            );
        }

        $this->resetDatabase();
        $this->runMigrations();

        $this->createMerchant();

        $this->repository = new PdoApiTokenRepository(
            $this->connection
        );
    }

    public function testApiTokenCanBeCreated()
    {
        $tokenHash = hash(
            'sha256',
            'test-token'
        );

        $expiresAt = '2026-08-10 18:00:00';

        $apiToken = new ApiToken(
            null,
            'merchant-1',
            $tokenHash,
            $expiresAt
        );

        $storedToken = $this->repository->create(
            $apiToken
        );

        $this->assertNotNull(
            $storedToken->getId()
        );

        $this->assertSame(
            32,
            strlen($storedToken->getId())
        );

        $this->assertSame(
            'merchant-1',
            $storedToken->getMerchantId()
        );

        $this->assertSame(
            $tokenHash,
            $storedToken->getTokenHash()
        );

        $this->assertSame(
            $expiresAt,
            $storedToken->getExpiresAt()
        );

        $statement = $this->connection->prepare(
            'SELECT *
             FROM api_tokens
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $storedToken->getId(),
        ]);

        $tokenData = $statement->fetch();

        $this->assertNotFalse(
            $tokenData
        );

        $this->assertSame(
            $tokenHash,
            $tokenData['token_hash']
        );

        $this->assertSame(
            'merchant-1',
            $tokenData['merchant_id']
        );
    }

    public function testApiTokenCanBeFoundByTokenHash()
    {
        $tokenHash = hash(
            'sha256',
            'existing-token'
        );

        $expiresAt = '2026-08-10 19:00:00';

        $this->insertApiToken(
            'token-1',
            'merchant-1',
            $tokenHash,
            $expiresAt
        );

        $apiToken = $this->repository->findByTokenHash(
            $tokenHash
        );

        $this->assertNotNull(
            $apiToken
        );

        $this->assertSame(
            'token-1',
            $apiToken->getId()
        );

        $this->assertSame(
            'merchant-1',
            $apiToken->getMerchantId()
        );

        $this->assertSame(
            $tokenHash,
            $apiToken->getTokenHash()
        );

        $this->assertSame(
            $expiresAt,
            $apiToken->getExpiresAt()
        );
    }

    public function testUnknownTokenHashReturnsNull()
    {
        $apiToken = $this->repository->findByTokenHash(
            hash('sha256', 'missing-token')
        );

        $this->assertNull(
            $apiToken
        );
    }

    private function resetDatabase()
    {
        $this->connection->exec(
            'DROP TABLE IF EXISTS transactions'
        );

        $this->connection->exec(
            'DROP TABLE IF EXISTS api_tokens'
        );

        $this->connection->exec(
            'DROP TABLE IF EXISTS merchants'
        );
    }

    private function runMigrations()
    {
        $migrationsPath =
            dirname(__DIR__, 2)
            . '/database/migrations';

        $migrationFiles = glob(
            $migrationsPath . '/*.sql'
        );

        sort($migrationFiles);

        foreach ($migrationFiles as $migrationFile) {
            $sql = file_get_contents(
                $migrationFile
            );

            $this->connection->exec($sql);
        }
    }

    private function createMerchant()
    {
        $statement = $this->connection->prepare(
            'INSERT INTO merchants (
                id,
                name,
                email,
                password_hash,
                payment_provider,
                payment_provider_config
            ) VALUES (
                :id,
                :name,
                :email,
                :password_hash,
                :payment_provider,
                :payment_provider_config
            )'
        );

        $statement->execute([
            'id' => 'merchant-1',
            'name' => 'Test Merchant',
            'email' => 'test@merchant.test',
            'password_hash' => 'test-password-hash',
            'payment_provider' => 'fake_stripe',
            'payment_provider_config' => '{}',
        ]);
    }

    private function insertApiToken(
        $id,
        $merchantId,
        $tokenHash,
        $expiresAt
    ) {
        $statement = $this->connection->prepare(
            'INSERT INTO api_tokens (
                id,
                merchant_id,
                token_hash,
                expires_at
            ) VALUES (
                :id,
                :merchant_id,
                :token_hash,
                :expires_at
            )'
        );

        $statement->execute([
            'id' => $id,
            'merchant_id' => $merchantId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);
    }
}
