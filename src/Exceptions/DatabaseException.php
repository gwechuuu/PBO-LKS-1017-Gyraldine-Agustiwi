<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class DatabaseException extends RuntimeException
{
    public function __construct(string $message = 'Terjadi kesalahan pada database', int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}