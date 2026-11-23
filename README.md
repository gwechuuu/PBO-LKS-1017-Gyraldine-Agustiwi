# LKS PBO - Merancang & Membangun REST API[cite: 42]

## Identitas Mahasiswa[cite: 42]
- **Nama Lengkap:** Gyraldine Agustiwi[cite: 2]
- **NIM:** H1101241017[cite: 2]
- **Kelas:** B[cite: 42] *(sesuaikan jika kelasmu berbeda)*[cite: 2]
- **Program Studi:** Sistem Informasi[cite: 2, 42]
- **Mata Kuliah:** Pemrograman Berbasis Objek (PBO) 3 SKS[cite: 2, 42]
- **Dosen Pengampu:** Syahru Rahmayuda, M.Kom[cite: 2, 42]
- **Repository GitHub:** https://github.com/gwechuuu/PBO-LKS-1017-Gyraldine-Agustiwi[cite: 2]

---

## Daftar Fitur yang Dibuat
- REST API berbasis PHP modern dengan *autoloading* PSR-4 (`App\` -> `src/`) dan Composer[cite: 2, 5].
- Penerapan prinsip SOLID dan *design pattern* (*Repository*, *Dependency Injection / Container*, *Front Controller*, *PDO Singleton*)[cite: 2, 8, 12, 13].
- Manajemen data Buku (`Book`) dan Kategori (`Category`) dengan operasi CRUD lengkap serta validasi dan *error handling*[cite: 15, 17, 21, 23, 25].
- Konfigurasi *environment* via `.env` dan penyimpanan data pada MySQL[cite: 6, 8, 21, 34].
- Pengujian API menggunakan cURL, Postman, skrip uji otomatis (`api_test.sh`), dan *unit test*[cite: 15, 18, 20].
- Aplikasi *frontend* terpisah dengan integrasi ke REST API (Dashboard, Halaman Buku, Halaman Kategori) serta dukungan CORS[cite: 2, 26, 27, 29, 30].

---

## Cara Menjalankan Proyek

### 1. Backend (REST API)
Pastikan layanan MySQL sudah aktif dan file `.env` sudah dikonfigurasi sesuai database[cite: 6].
```bash
composer dump-autoload -o
php -S localhost:8000 -t public
