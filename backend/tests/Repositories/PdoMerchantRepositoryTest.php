<?php

use App\Models\Merchant;
use App\Repositories\PdoMerchantRepository;
use PHPUnit\Framework\TestCase;

class PdoMerchantRepositoryTest extends TestCase
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

        $this->repository = new PdoMerchantRepository(
            $this->connection
        );
    }

    public function testMerchantCanBeCreated()
    {
        $merchant = new Merchant(
            null,
            'Test Merchant',
            'test@merchant.test',
            'test-password-hash',
            'fake_stripe',
            [
                'account_id' => 'fake_account_test',
            ]
        );

        $storedMerchant = $this->repository->create(
            $merchant
        );

        $this->assertNotNull(
            $storedMerchant->getId()
        );

        $this->assertSame(
            32,
            strlen($storedMerchant->getId())
        );

        $this->assertSame(
            'Test Merchant',
            $storedMerchant->getName()
        );

        $this->assertSame(
            'test@merchant.test',
            $storedMerchant->getEmail()
        );

        $this->assertSame(
            'test-password-hash',
            $storedMerchant->getPasswordHash()
        );

        $this->assertSame(
            'fake_stripe',
            $storedMerchant->getPaymentProvider()
        );

        $this->assertSame(
            [
                'account_id' => 'fake_account_test',
            ],
            $storedMerchant->getPaymentProviderConfig()
        );

        $statement = $this->connection->prepare(
            'SELECT *
             FROM merchants
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $storedMerchant->getId(),
        ]);

        $merchantData = $statement->fetch();

        $this->assertNotFalse(
            $merchantData
        );

        $this->assertSame(
            'test@merchant.test',
            $merchantData['email']
        );
    }

    public function testMerchantCanBeFoundByEmail()
    {
        $this->insertMerchant(
            'merchant-1',
            'Merchant One',
            'one@merchant.test',
            'password-hash',
            'fake_stripe',
            [
                'account_id' => 'fake_account_one',
            ]
        );

        $merchant = $this->repository->findByEmail(
            'one@merchant.test'
        );

        $this->assertNotNull(
            $merchant
        );

        $this->assertSame(
            'merchant-1',
            $merchant->getId()
        );

        $this->assertSame(
            'Merchant One',
            $merchant->getName()
        );

        $this->assertSame(
            'one@merchant.test',
            $merchant->getEmail()
        );

        $this->assertSame(
            'password-hash',
            $merchant->getPasswordHash()
        );

        $this->assertSame(
            'fake_stripe',
            $merchant->getPaymentProvider()
        );

        $this->assertSame(
            [
                'account_id' => 'fake_account_one',
            ],
            $merchant->getPaymentProviderConfig()
        );
    }

    public function testMerchantCanBeFoundById()
    {
        $this->insertMerchant(
            'merchant-2',
            'Merchant Two',
            'two@merchant.test',
            'password-hash',
            'fake_stripe',
            [
                'account_id' => 'fake_account_two',
            ]
        );

        $merchant = $this->repository->findById(
            'merchant-2'
        );

        $this->assertNotNull(
            $merchant
        );

        $this->assertSame(
            'merchant-2',
            $merchant->getId()
        );

        $this->assertSame(
            'Merchant Two',
            $merchant->getName()
        );

        $this->assertSame(
            'two@merchant.test',
            $merchant->getEmail()
        );

        $this->assertSame(
            [
                'account_id' => 'fake_account_two',
            ],
            $merchant->getPaymentProviderConfig()
        );
    }

    public function testUnknownEmailReturnsNull()
    {
        $merchant = $this->repository->findByEmail(
            'missing@merchant.test'
        );

        $this->assertNull(
            $merchant
        );
    }

    public function testUnknownIdReturnsNull()
    {
        $merchant = $this->repository->findById(
            'missing-merchant'
        );

        $this->assertNull(
            $merchant
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

    private function insertMerchant(
        $id,
        $name,
        $email,
        $passwordHash,
        $paymentProvider,
        $paymentProviderConfig
    ) {
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
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'payment_provider' => $paymentProvider,
            'payment_provider_config' => json_encode(
                $paymentProviderConfig
            ),
        ]);
    }
}
