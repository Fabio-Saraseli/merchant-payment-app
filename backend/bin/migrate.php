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

echo "Migration table is ready." . PHP_EOL;

// TODO: find and execute pending migration files