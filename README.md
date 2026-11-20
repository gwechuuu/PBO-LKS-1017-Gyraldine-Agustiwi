# LKS PBO — REST API Perpustakaan

REST API manajemen data buku berbasis PHP 8.1 dan MySQL dengan arsitektur berlapis (`Core`, `Models`, `Contracts`, `Repositories`, `Services`, `Controllers`, `Exceptions`) serta autoloading PSR-4 (`App\`).

## Struktur Folder Utama
- `public/index.php` — Entry point dan global error handler
- `src/Core/` — `Request`, `Response`, `Router`, `Container`
- `src/Config/` & `src/Database/` — `Env`, `Connection` (Singleton PDO)
- `src/Models/` — `Book`
- `src/Contracts/` — `BookRepositoryInterface`
- `src/Repositories/` — `MySQLBookRepository`
- `src/Services/` — `BookService`
- `src/Controllers/` — `BookController`
- `src/Exceptions/` — `ValidationException`, `NotFoundException`, `DatabaseException`
- `tests/` — `BookServiceTest.php` & `run_tests.php`

## Cara Menjalankan
1. Pastikan MySQL di Laragon aktif dan database `perpustakaan` beserta tabel `books` sudah tersedia.
2. Sesuaikan konfigurasi `.env` dari `.env.example`.
3. Jalankan server pengembangan PHP:
   ```bash
   php -S 127.0.0.1:8000 -t public