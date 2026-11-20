<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class NotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Data tidak ditemukan', int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}