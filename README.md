# StokClean

StokClean adalah aplikasi pengelolaan persediaan alat kebersihan berbasis Laravel. Aplikasi ini menyediakan dashboard inventaris dan autentikasi pengguna dengan dua role: `pemilik` dan `pegawai`.

## Fitur

- Login menggunakan username/email, password, dan role.
- Proteksi dashboard menggunakan autentikasi session Laravel.
- Logout dengan invalidasi session dan token CSRF.
- Dashboard persediaan dengan data barang, kategori, stok masuk, stok keluar, dan riwayat transaksi.
- Database MySQL melalui Eloquent ORM.
- Tampilan responsif untuk desktop dan perangkat mobile.

## Teknologi

- PHP 8.3+
- Laravel
- MySQL / MariaDB
- Blade
- Tailwind CSS
- Vite
- Chart.js
- Lucide Icons

## Persyaratan

- Laragon dengan PHP dan MySQL aktif.
- Composer.
- Node.js dan npm untuk membangun asset frontend.

## Instalasi

1. Letakkan proyek di folder `C:\laragon\www\stok-kebersihan`.
2. Install dependency PHP:

   ```bash
   composer install
   ```

3. Install dependency frontend:

   ```bash
   npm install
   ```

4. Buat database MySQL dengan nama `stok_kebersihan`.
5. Salin `.env.example` menjadi `.env`, lalu sesuaikan konfigurasi:

   ```env
   APP_URL=http://127.0.0.1:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=stok_kebersihan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Buat application key dan jalankan migration:

   ```bash
   php artisan key:generate
   php artisan migrate
   ```

7. Opsional, isi akun contoh:

   ```bash
   php artisan db:seed
   ```

## Menjalankan Aplikasi

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Untuk development frontend, buka terminal lain:

```bash
npm run dev
```

Buka http://127.0.0.1:8000/login untuk masuk ke aplikasi.

## Akun Contoh

Seeder bawaan membuat akun berikut:

| Email | Password | Role |
| --- | --- | --- |
| `test@example.com` | `password` | `pemilik` |

Role `pegawai` dapat digunakan setelah membuat atau mengubah akun pengguna di database dengan nilai kolom `role` menjadi `pegawai`.

## Perintah Berguna

```bash
php artisan migrate:status
php artisan db:show
php artisan test
npm run build
```

## Struktur Utama

```text
app/Http/Controllers/AuthController.php  # Login dan logout
app/Models/User.php                       # Model pengguna
database/migrations/                      # Struktur database
resources/views/auth/login.blade.php      # Halaman login
resources/views/dashboard.blade.php       # Dashboard aplikasi
routes/web.php                            # Route dan middleware auth
```

## Catatan Keamanan

- Jangan gunakan password contoh pada lingkungan production.
- Jangan commit file `.env` ke repository.
- Gunakan `APP_DEBUG=false` pada production.
- Simpan kredensial deployment sebagai secret.

## Lisensi

Proyek ini menggunakan framework Laravel. Detail lisensi aplikasi dapat ditambahkan sesuai kebutuhan proyek.
