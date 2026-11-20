<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $path;
    /** @var array<string, mixed> */
    private array $query;
    /** @var array<string, mixed> */
    private array $body;

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     */
    public function __construct(string $method, string $path, array $query = [], array $body = [])
    {
        $this->method = strtoupper($method);
        $this->path = '/' . trim($path, '/');
        if ($this->path === '/') {
            $this->path = '/';
        }
        $this->query = $query;
        $this->body = $body;
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) ? $path : '/';

        $rawInput = file_get_contents('php://input');
        $decoded = [];
        if (is_string($rawInput) && trim($rawInput) !== '') {
            $parsed = json_decode($rawInput, true);
            if (is_array($parsed)) {
                $decoded = $parsed;
            }
        }

        if (empty($decoded) && !empty($_POST)) {
            $decoded = $_POST;
        }

        return new self($method, $path, $_GET, $decoded);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array<string, mixed>
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @return array<string, mixed>
     */
    public function getBody(): array
    {
        return $this->body;
    }
}