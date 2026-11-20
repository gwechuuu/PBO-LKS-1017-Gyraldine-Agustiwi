<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Container
{
    /** @var array<string, callable> */
    private array $bindings = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
        unset($this->instances[$abstract]);
    }

    public function singleton(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = function (Container $container) use ($abstract, $factory) {
            if (!array_key_exists($abstract, $this->instances)) {
                $this->instances[$abstract] = $factory($container);
            }

            return $this->instances[$abstract];
        };
    }

    public function make(string $abstract): mixed
    {
        if (!isset($this->bindings[$abstract])) {
            throw new RuntimeException("Service atau binding tidak terdaftar: {$abstract}");
        }

        return ($this->bindings[$abstract])($this);
    }
}