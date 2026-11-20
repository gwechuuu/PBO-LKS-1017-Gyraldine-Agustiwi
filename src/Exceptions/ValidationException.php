<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class ValidationException extends RuntimeException
{
    /** @var array<string, mixed> */
    private array $errors;

    /**
     * @param array<string, mixed> $errors
     */
    public function __construct(string $message = 'Validasi gagal', array $errors = [], int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}