<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/BookServiceTest.php';

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Book;
use App\Services\BookService;
use Tests\InMemoryBookRepository;

$passed = 0;
$failed = 0;

$run = function (string $name, callable $test) use (&$passed, &$failed): void {
    try {
        $test();
        echo "[PASS] {$name}\n";
        $passed++;
    } catch (Throwable $e) {
        echo "[FAIL] {$name}: " . $e->getMessage() . "\n";
        $failed++;
    }
};

$makeService = function (): BookService {
    $repo = new InMemoryBookRepository([
        new Book(1, 'Laskar Pelangi', 'Andrea Hirata', 2005, 5),
        new Book(2, 'Bumi Manusia', 'Pramoedya Ananta Toer', 1980, 3),
    ]);

    return new BookService($repo);
};

$run('getAllBooks mengembalikan data awal', function () use ($makeService): void {
    $books = $makeService()->getAllBooks();
    if (count($books) !== 2 || $books[0]['judul'] !== 'Laskar Pelangi') {
        throw new RuntimeException('Data awal tidak sesuai');
    }
});

$run('createBook berhasil menambah buku baru', function () use ($makeService): void {
    $created = $makeService()->createBook([
        'judul' => 'Pulang',
        'penulis' => 'Leila S. Chudori',
        'tahun' => 2012,
        'stok' => 4,
    ]);
    if ($created['id'] !== 3 || $created['judul'] !== 'Pulang') {
        throw new RuntimeException('Hasil createBook tidak sesuai');
    }
});

$run('createBook melempar ValidationException saat judul kosong', function () use ($makeService): void {
    try {
        $makeService()->createBook([
            'judul' => '',
            'penulis' => 'Andrea Hirata',
            'tahun' => 2005,
            'stok' => 5,
        ]);
        throw new RuntimeException('Seharusnya melempar ValidationException');
    } catch (ValidationException $e) {
        // expected
    }
});

$run('getBookById melempar NotFoundException saat ID tidak ada', function () use ($makeService): void {
    try {
        $makeService()->getBookById(999);
        throw new RuntimeException('Seharusnya melempar NotFoundException');
    } catch (NotFoundException $e) {
        // expected
    }
});

echo "\nHasil: {$passed} passed, {$failed} failed\n";
exit($failed > 0 ? 1 : 0);