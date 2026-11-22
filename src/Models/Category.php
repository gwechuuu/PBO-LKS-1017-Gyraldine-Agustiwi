<?php

declare(strict_types=1);

namespace App\Models;

class Category
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?string $createdAt = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->createdAt,
        ];
    }
}