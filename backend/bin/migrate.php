<?php

use App\Core\Database;

require dirname(__DIR__) . '/vendor/autoload.php';

$database = new Database();
$connection = $database->connect();

$connection->exec(
    'CREATE TABLE IF NOT EXISTS migrations (
        id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
        migration VARCHAR(255) UNIQUE NOT NULL,
        executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )'
);

$migrationsPath = dirname(__DIR__) . '/database/migrations';
$migrationFiles = glob($migrationsPath . '/*.sql');

if (!$migrationFiles) {
    echo "No migration files found." . PHP_EOL;
    exit;
}

sort($migrationFiles);

$statement = $connection->query(
    'SELECT migration FROM migrations ORDER BY id'
);

$executedMigrations = $statement->fetchAll(PDO::FETCH_COLUMN);

foreach ($migrationFiles as $migrationFile) {
    $migrationName = basename($migrationFile);

    if (in_array($migrationName, $executedMigrations)) {
        continue;
    }

    $sql = file_get_contents($migrationFile);

    if ($sql === false) {
        throw new RuntimeException(
            "Could not read migration: {$migrationName}"
        );
    }

    $connection->beginTransaction();

    try {
        $connection->exec($sql);

        $statement = $connection->prepare(
            'INSERT INTO migrations (migration)
             VALUES (:migration)'
        );

        $statement->execute([
            'migration' => $migrationName,
        ]);

        $connection->commit();

        echo "Executed migration: {$migrationName}" . PHP_EOL;
    } catch (Throwable $exception) {
        $connection->rollBack();

        throw $exception;
    }
}

echo "Migrations are up to date." . PHP_EOL;
