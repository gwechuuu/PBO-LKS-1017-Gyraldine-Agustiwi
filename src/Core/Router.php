<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    /** @var array<int, array{method: string, pattern: string, regex: string, params: array<int, string>, handler: callable}> */
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $normalized = '/' . trim($pattern, '/');
        if ($normalized === '//') {
            $normalized = '/';
        }

        $params = [];
        $regexPattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function (array $matches) use (&$params): string {
                $params[] = $matches[1];
                return '([^/]+)';
            },
            $normalized
        );

        $regex = '#^' . $regexPattern . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $normalized,
            'regex' => $regex,
            'params' => $params,
            'handler' => $handler,
        ];
    }

    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->add('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler): void
    {
        $this->add('DELETE', $pattern, $handler);
    }

    public function dispatch(Request $request): mixed
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $path, $matches)) {
                array_shift($matches);
                $routeParams = [];
                foreach ($route['params'] as $index => $paramName) {
                    $routeParams[$paramName] = $matches[$index] ?? null;
                }

                return ($route['handler'])($request, $routeParams);
            }
        }

        Response::json([
            'status' => 'error',
            'message' => 'Endpoint tidak ditemukan',
        ], 404);

        return null;
    }
}