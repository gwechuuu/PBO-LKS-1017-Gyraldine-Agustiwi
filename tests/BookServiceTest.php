<?php

declare(strict_types=1);

namespace Tests;

use App\Contracts\BookRepositoryInterface;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Book;
use App\Services\BookService;
use PHPUnit\Framework\TestCase;

class InMemoryBookRepository implements BookRepositoryInterface
{
    /** @var array<int, Book> */
    private array $books = [];
    private int $nextId = 1;

    /**
     * @param Book[] $initialBooks
     */
    public function __construct(array $initialBooks = [])
    {
        foreach ($initialBooks as $book) {
            $id = $book->getId() ?? $this->nextId++;
            if ($id >= $this->nextId) {
                $this->nextId = $id + 1;
            }
            $this->books[$id] = new Book(
                $id,
                $book->getJudul(),
                $book->getPenulis(),
                $book->getTahun(),
                $book->getStok()
            );
        }
    }

    public function findAll(): array
    {
        return array_values($this->books);
    }

    public function findById(int $id): ?Book
    {
        return $this->books[$id] ?? null;
    }

    public function create(Book $book): Book
    {
        $id = $this->nextId++;
        $saved = new Book(
            $id,
            $book->getJudul(),
            $book->getPenulis(),
            $book->getTahun(),
            $book->getStok()
        );
        $this->books[$id] = $saved;

        return $saved;
    }

    public function update(int $id, Book $book): ?Book
    {
        if (!isset($this->books[$id])) {
            return null;
        }

        $updated = new Book(
            $id,
            $book->getJudul(),
            $book->getPenulis(),
            $book->getTahun(),
            $book->getStok()
        );
        $this->books[$id] = $updated;

        return $updated;
    }

    public function delete(int $id): bool
    {
        if (!isset($this->books[$id])) {
            return false;
        }

        unset($this->books[$id]);

        return true;
    }
}

class BookServiceTest extends TestCase
{
    private function createService(): BookService
    {
        $repo = new InMemoryBookRepository([
            new Book(1, 'Laskar Pelangi', 'Andrea Hirata', 2005, 5),
            new Book(2, 'Bumi Manusia', 'Pramoedya Ananta Toer', 1980, 3),
        ]);

        return new BookService($repo);
    }

    public function testGetAllBooksReturnsSeededBooks(): void
    {
        $service = $this->createService();
        $books = $service->getAllBooks();

        $this->assertCount(2, $books);
        $this->assertSame('Laskar Pelangi', $books[0]['judul']);
    }

    public function testCreateBookSuccess(): void
    {
        $service = $this->createService();
        $created = $service->createBook([
            'judul' => 'Pulang',
            'penulis' => 'Leila S. Chudori',
            'tahun' => 2012,
            'stok' => 4,
        ]);

        $this->assertSame(3, $created['id']);
        $this->assertSame('Pulang', $created['judul']);
        $this->assertSame(4, $created['stok']);
    }

    public function testCreateBookThrowsValidationExceptionOnEmptyTitle(): void
    {
        $service = $this->createService();

        $this->expectException(ValidationException::class);
        $service->createBook([
            'judul' => '',
            'penulis' => 'Andrea Hirata',
            'tahun' => 2005,
            'stok' => 5,
        ]);
    }

    public function testGetBookByIdThrowsNotFoundException(): void
    {
        $service = $this->createService();

        $this->expectException(NotFoundException::class);
        $service->getBookById(999);
    }
}