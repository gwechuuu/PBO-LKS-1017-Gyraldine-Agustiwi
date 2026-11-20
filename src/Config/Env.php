<?php

declare(strict_types=1);

namespace App\Config;

use RuntimeException;

class Env
{
    private static bool $loaded = false;

    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }

        if (!is_file($path)) {
            throw new RuntimeException("File .env tidak ditemukan di: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new RuntimeException("Gagal membaca file .env di: {$path}");
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            if (!str_contains($trimmed, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $trimmed, 2);
            $name = trim($name);
            $value = trim($value);

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv("{$name}={$value}");
        }

        self::$loaded = true;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, $_ENV)) {
            return (string) $_ENV[$key];
        }

        $envVal = getenv($key);
        if ($envVal !== false) {
            return (string) $envVal;
        }

        return $default;
    }
}