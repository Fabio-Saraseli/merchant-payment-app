<?php

use App\Models\Transaction;
use App\Repositories\PdoTransactionRepository;
use PHPUnit\Framework\TestCase;

class PdoTransactionRepositoryTest extends TestCase
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

        $this->createMerchant(
            'merchant-1',
            'Merchant One',
            'one@merchant.test'
        );

        $this->createMerchant(
            'merchant-2',
            'Merchant Two',
            'two@merchant.test'
        );

        $this->repository = new PdoTransactionRepository(
            $this->connection
        );
    }

    public function testTransactionIsPersisted()
    {
        $transaction = new Transaction(
            null,
            'merchant-1',
            'fake_stripe',
            'fake_account_test_123',
            1500,
            'EUR',
            'Repository test payment',
            '4242',
            'succeeded'
        );

        $storedTransaction = $this->repository->create(
            $transaction
        );

        $this->assertNotNull(
            $storedTransaction->getId()
        );

        $this->assertSame(
            32,
            strlen($storedTransaction->getId())
        );

        $this->assertSame(
            'merchant-1',
            $storedTransaction->getMerchantId()
        );

        $this->assertSame(
            1500,
            $storedTransaction->getAmountCents()
        );

        $statement = $this->connection->prepare(
            'SELECT *
             FROM transactions
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $storedTransaction->getId(),
        ]);

        $transactionData = $statement->fetch();

        $this->assertNotFalse(
            $transactionData
        );

        $this->assertSame(
            'Repository test payment',
            $transactionData['description']
        );
    }

    public function testMerchantsOnlyReceiveTheirOwnTransactions()
    {
        $merchantOneTransaction = new Transaction(
            null,
            'merchant-1',
            'fake_stripe',
            'provider-1',
            1000,
            'EUR',
            'Merchant one payment',
            '4242',
            'succeeded'
        );

        $merchantTwoTransaction = new Transaction(
            null,
            'merchant-2',
            'fake_stripe',
            'provider-2',
            2000,
            'EUR',
            'Merchant two payment',
            '4242',
            'succeeded'
        );

        $this->repository->create(
            $merchantOneTransaction
        );

        $this->repository->create(
            $merchantTwoTransaction
        );

        $merchantOneTransactions =
            $this->repository->findByMerchantAndDateRange(
                'merchant-1'
            );

        $merchantTwoTransactions =
            $this->repository->findByMerchantAndDateRange(
                'merchant-2'
            );

        $this->assertCount(
            1,
            $merchantOneTransactions
        );

        $this->assertSame(
            'Merchant one payment',
            $merchantOneTransactions[0]->getDescription()
        );

        $this->assertCount(
            1,
            $merchantTwoTransactions
        );

        $this->assertSame(
            'Merchant two payment',
            $merchantTwoTransactions[0]->getDescription()
        );
    }

    public function testTransactionsCanBeFilteredByDateRange()
    {
        $this->insertTransaction(
            'transaction-1',
            'merchant-1',
            'Payment August 1',
            '2026-08-01 10:00:00'
        );

        $this->insertTransaction(
            'transaction-2',
            'merchant-1',
            'Payment August 5',
            '2026-08-05 12:00:00'
        );

        $this->insertTransaction(
            'transaction-3',
            'merchant-1',
            'Payment August 10',
            '2026-08-10 15:00:00'
        );

        $transactions =
            $this->repository->findByMerchantAndDateRange(
                'merchant-1',
                '2026-08-04',
                '2026-08-06'
            );

        $this->assertCount(
            1,
            $transactions
        );

        $this->assertSame(
            'Payment August 5',
            $transactions[0]->getDescription()
        );
    }

    public function testToDateIncludesTheWholeSelectedDay()
    {
        $this->insertTransaction(
            'transaction-1',
            'merchant-1',
            'Late payment',
            '2026-08-05 23:59:59'
        );

        $transactions =
            $this->repository->findByMerchantAndDateRange(
                'merchant-1',
                null,
                '2026-08-05'
            );

        $this->assertCount(
            1,
            $transactions
        );

        $this->assertSame(
            'Late payment',
            $transactions[0]->getDescription()
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

    private function createMerchant(
        $id,
        $name,
        $email
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
            'password_hash' => 'test-hash',
            'payment_provider' => 'fake_stripe',
            'payment_provider_config' => '{}',
        ]);
    }

    private function insertTransaction(
        $id,
        $merchantId,
        $description,
        $createdAt
    ) {
        $statement = $this->connection->prepare(
            'INSERT INTO transactions (
                id,
                merchant_id,
                payment_provider,
                provider_transaction_id,
                amount_cents,
                currency,
                description,
                card_last_four,
                status,
                created_at
            ) VALUES (
                :id,
                :merchant_id,
                :payment_provider,
                :provider_transaction_id,
                :amount_cents,
                :currency,
                :description,
                :card_last_four,
                :status,
                :created_at
            )'
        );

        $statement->execute([
            'id' => $id,
            'merchant_id' => $merchantId,
            'payment_provider' => 'fake_stripe',
            'provider_transaction_id' => 'provider-' . $id,
            'amount_cents' => 1000,
            'currency' => 'EUR',
            'description' => $description,
            'card_last_four' => '4242',
            'status' => 'succeeded',
            'created_at' => $createdAt,
        ]);
    }
}