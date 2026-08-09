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

        $dsn = $this->getEnvironmentVariable('DB_DSN');

        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

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

        return $this->connection;
    }

    private function getEnvironmentVariable($name)
    {
        $value = getenv($name);

        if ($value === false || $value === '') {
            throw new RuntimeException(
                "Missing environment variable: {$name}"
            );
        }

        return $value;
    }
}
