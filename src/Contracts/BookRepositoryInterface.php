<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Book;

interface BookRepositoryInterface
{
    /**
     * @return Book[]
     */
    public function findAll(): array;

    public function findById(int $id): ?Book;

    public function create(Book $book): Book;

    public function update(int $id, Book $book): ?Book;

    public function delete(int $id): bool;
}