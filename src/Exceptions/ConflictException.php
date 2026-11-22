<?php

declare(strict_types=1);

namespace App\Exceptions;

class ConflictException extends HttpException
{
    public function __construct(string $message = 'Data conflict occurred', array $errors = [])
    {
        parent::__construct($message, 409, $errors);
    }
}