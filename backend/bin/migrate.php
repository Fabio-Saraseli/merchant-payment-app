<?php

use App\Core\Database;

require dirname(__DIR__) . '/vendor/autoload.php';

$database = new Database();
$connection = $database->connect();

$connection->exec(
    'CREATE TABLE IF NOT EXISTS migrations (
        migration VARCHAR(255) PRIMARY KEY,
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
    'SELECT migration FROM migrations ORDER BY migration'
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

    try {
        $connection->exec($sql);

        $statement = $connection->prepare(
            'INSERT INTO migrations (migration)
             VALUES (:migration)'
        );

        $statement->execute([
            'migration' => $migrationName,
        ]);

        echo "Executed migration: {$migrationName}" . PHP_EOL;
    } catch (Throwable $exception) {
        throw new RuntimeException(
            "Migration failed: {$migrationName}. " . $exception->getMessage(),
            0,
            $exception
        );
    }
}

echo "Migrations are up to date." . PHP_EOL;
