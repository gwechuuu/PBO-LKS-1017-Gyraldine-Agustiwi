# Sistem Perpustakaan — REST API (PHP OOP) & Frontend

Aplikasi manajemen data buku dan kategori berbasis arsitektur berlapis (`Core`, `Models`, `Contracts`, `Repositories`, `Services`, `Controllers`, `Exceptions`) menggunakan PHP 8.1+, PDO Singleton, kustom `Router` dan `Container`, serta antarmuka web `frontend-perpustakaan`.

## Struktur Folder Utama
- `public/index.php` — *Entry point*, registrasi *container*, pendaftaran *route*, CORS, dan *global exception handler*
- `src/Core/` — `Container`, `Request`, `Response`, `Router`
- `src/Database/` — `Connection` (PDO Singleton)
- `src/Models/` — `Book`, `Category`
- `src/Contracts/` — `BookRepositoryInterface`, `CategoryRepositoryInterface`
- `src/Repositories/` — `MySQLBookRepository`, `MySQLCategoryRepository`
- `src/Services/` — `BookService`, `CategoryService`
- `src/Controllers/` — `BookController`, `CategoryController`
- `src/Exceptions/` — `HttpException`, `ConflictException`, `NotFoundException`, `ValidationException`, `DatabaseException`
- `tests/` — `BookServiceTest.php`, `run_tests.php`
- `frontend-perpustakaan/` — `index.html`, `style.css`, `app.js`

## Menjalankan Aplikasi
1. Pastikan layanan MySQL aktif dan database/tabel sudah sesuai (`database/schema.sql`).
2. Jalankan server REST API:
   ```bash
   php -S 127.0.0.1:8000 -t public