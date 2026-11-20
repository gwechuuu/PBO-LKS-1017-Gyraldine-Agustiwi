<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function json(array $payload, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}