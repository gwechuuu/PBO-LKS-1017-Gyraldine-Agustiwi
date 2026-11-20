<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BookRepositoryInterface;
use App\Models\Book;
use PDO;

class MySQLBookRepository implements BookRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT id, judul, penulis, tahun, stok FROM books ORDER BY id DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row): Book => $this->mapRowToBook($row), $rows);
    }

    public function findById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare('SELECT id, judul, penulis, tahun, stok FROM books WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->mapRowToBook($row);
    }

    public function create(Book $book): Book
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO books (judul, penulis, tahun, stok) VALUES (:judul, :penulis, :tahun, :stok)'
        );
        $stmt->execute([
            'judul' => $book->getJudul(),
            'penulis' => $book->getPenulis(),
            'tahun' => $book->getTahun(),
            'stok' => $book->getStok(),
        ]);

        $newId = (int) $this->pdo->lastInsertId();

        return new Book(
            $newId,
            $book->getJudul(),
            $book->getPenulis(),
            $book->getTahun(),
            $book->getStok()
        );
    }

    public function update(int $id, Book $book): ?Book
    {
        $existing = $this->findById($id);
        if ($existing === null) {
            return null;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE books SET judul = :judul, penulis = :penulis, tahun = :tahun, stok = :stok WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'judul' => $book->getJudul(),
            'penulis' => $book->getPenulis(),
            'tahun' => $book->getTahun(),
            'stok' => $book->getStok(),
        ]);

        return new Book(
            $id,
            $book->getJudul(),
            $book->getPenulis(),
            $book->getTahun(),
            $book->getStok()
        );
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    private function mapRowToBook(array $row): Book
    {
        return new Book(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['judul'],
            (string) $row['penulis'],
            (int) $row['tahun'],
            (int) $row['stok']
        );
    }
}