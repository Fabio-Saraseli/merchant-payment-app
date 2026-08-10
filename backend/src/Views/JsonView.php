<?php

declare(strict_types=1);

namespace App\Views;

final class JsonView
{
    public function render(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
}
