<?php

namespace App\Core;

use PDO;
use RuntimeException;

class Database
{
    private $connection;

    public function connect()
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $host = $this->getEnvironmentVariable('DB_HOST');
        $port = $this->getEnvironmentVariable('DB_PORT');
        $database = $this->getEnvironmentVariable('DB_NAME');
        $username = $this->getEnvironmentVariable('DB_USER');
        $password = $this->getEnvironmentVariable('DB_PASSWORD');

        $dsn = "pgsql:host={$host};port={$port};dbname={$database}";

        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $this->connection;
    }

    private function getEnvironmentVariable($name)
    {
        $value = getenv($name);

        if ($value === false || $value === '') {
            throw new RuntimeException("Missing environment variable: {$name}");
        }

        return $value;
    }
}
