<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\BookRepositoryInterface;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Book;

class BookService
{
    private BookRepositoryInterface $repository;

    public function __construct(BookRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAllBooks(): array
    {
        $books = $this->repository->findAll();

        return array_map(fn(Book $book): array => $book->toArray(), $books);
    }

    /**
     * @return array<string, mixed>
     */
    public function getBookById(int $id): array
    {
        if ($id <= 0) {
            throw new ValidationException('ID buku tidak valid', [
                'id' => ['ID buku harus berupa angka positif'],
            ]);
        }

        $book = $this->repository->findById($id);
        if ($book === null) {
            throw new NotFoundException("Buku dengan ID {$id} tidak ditemukan");
        }

        return $book->toArray();
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function createBook(array $input): array
    {
        $this->validateBookInput($input);

        $judul = trim((string) $input['judul']);
        $penulis = trim((string) $input['penulis']);
        $tahun = (int) $input['tahun'];
        $stok = isset($input['stok']) ? (int) $input['stok'] : 0;

        $book = new Book(null, $judul, $penulis, $tahun, $stok);
        $created = $this->repository->create($book);

        return $created->toArray();
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function updateBook(int $id, array $input): array
    {
        if ($id <= 0) {
            throw new ValidationException('ID buku tidak valid', [
                'id' => ['ID buku harus berupa angka positif'],
            ]);
        }

        $existing = $this->repository->findById($id);
        if ($existing === null) {
            throw new NotFoundException("Buku dengan ID {$id} tidak ditemukan");
        }

        $merged = [
            'judul' => $input['judul'] ?? $existing->getJudul(),
            'penulis' => $input['penulis'] ?? $existing->getPenulis(),
            'tahun' => $input['tahun'] ?? $existing->getTahun(),
            'stok' => $input['stok'] ?? $existing->getStok(),
        ];

        $this->validateBookInput($merged);

        $book = new Book(
            $id,
            trim((string) $merged['judul']),
            trim((string) $merged['penulis']),
            (int) $merged['tahun'],
            (int) $merged['stok']
        );

        $updated = $this->repository->update($id, $book);
        if ($updated === null) {
            throw new NotFoundException("Buku dengan ID {$id} tidak ditemukan");
        }

        return $updated->toArray();
    }

    public function deleteBook(int $id): bool
    {
        if ($id <= 0) {
            throw new ValidationException('ID buku tidak valid', [
                'id' => ['ID buku harus berupa angka positif'],
            ]);
        }

        $existing = $this->repository->findById($id);
        if ($existing === null) {
            throw new NotFoundException("Buku dengan ID {$id} tidak ditemukan");
        }

        return $this->repository->delete($id);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function validateBookInput(array $input): void
    {
        $errors = [];

        $judul = trim((string) ($input['judul'] ?? ''));
        if ($judul === '') {
            $errors['judul'][] = 'Judul wajib diisi';
        }

        $penulis = trim((string) ($input['penulis'] ?? ''));
        if ($penulis === '') {
            $errors['penulis'][] = 'Penulis wajib diisi';
        }

        $tahun = filter_var($input['tahun'] ?? null, FILTER_VALIDATE_INT);
        $currentYear = (int) date('Y') + 1;
        if ($tahun === false || $tahun < 1000 || $tahun > $currentYear) {
            $errors['tahun'][] = 'Tahun terbit tidak valid';
        }

        $stok = filter_var($input['stok'] ?? 0, FILTER_VALIDATE_INT);
        if ($stok === false || $stok < 0) {
            $errors['stok'][] = 'Stok tidak boleh negatif';
        }

        if (!empty($errors)) {
            throw new ValidationException('Validasi data buku gagal', $errors);
        }
    }
}