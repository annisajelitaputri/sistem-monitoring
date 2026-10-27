# Sistem Monitoring & Website SMKN 9 Luwu

Website sekolah dan sistem monitoring absensi guru dan siswa untuk **SMKN 9 Luwu**.
Dikerjakan sebagai Project Mandiri, Politeknik Dewantara.

## Informasi Proyek

| | |
|---|---|
| **Nama Mitra** | SMKN 9 Luwu |
| **Nama Proyek** | Website Sekolah & Sistem Monitoring Absensi Guru dan Siswa |
| **Pengembang** | Annisa Jelita Putri (NIM 24404132) |
| **Kelas** | TRM Web Design |
| **Mata Kuliah** | Project Mandiri, Politeknik Dewantara |

## Fitur

- **Situs publik:** beranda, profil sekolah, daftar guru, kegiatan, berita, dan kontak
- **Panel Admin:** kelola guru, siswa, kelas, user, berita, dan kegiatan; absensi guru dan siswa; rekap absensi (bisa dicetak)
- **Panel Guru:** absen saya, absensi siswa, dan rekap kelas
- **Login** berbasis peran (Admin dan Guru)

## Tech Stack

- **Framework:** Laravel 10
- **Bahasa:** PHP 8.1
- **Database:** MySQL (nama database: `sistem-monitoring`)
- **Server lokal:** Laragon
- **Kelola database:** phpMyAdmin
- **Kontrol versi:** Git & GitHub

## Struktur Direktori

```
sistem-monitoring/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Dashboard, Guru, Siswa, Kelas, Absensi, Rekap, Berita, Kegiatan, User
│   │   │   ├── Auth/           # LoginController
│   │   │   ├── Guru/           # Dashboard, Absen Saya, Absensi Siswa, Rekap Kelas
│   │   │   └── PublicSite/     # Beranda, Profil, Guru, Kegiatan, Berita, Kontak
│   │   ├── Middleware/
│   │   └── Kernel.php
│   ├── Models/
│   ├── Providers/
│   ├── services/
│   └── support/                # Helper (tampil.php)
├── bootstrap/
├── config/                     # Konfigurasi aplikasi (termasuk sekolah.php)
├── database/
│   ├── factories/
│   ├── migrations/             # Tabel users, gurus, kelas, siswa, berita, kegiatan,
│   │                           # profil_sekolah, absensi_guru, absensi_siswa
│   ├── seeders/                # DatabaseSeeder (akun dan data contoh), UserSeeder
│   └── sql/                    # File .sql (sistem-monitoring.sql)
├── public/
│   ├── assets/
│   │   ├── css/                # style.css
│   │   └── js/                 # main.js
│   └── storage/                # Symlink ke storage/app/public (php artisan storage:link)
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── components/         # Komponen (icon)
│       ├── layouts/            # admin, guru, public, situs
│       ├── pages/
│       │   ├── admin/          # Halaman panel Admin
│       │   ├── guru/           # Halaman panel Guru
│       │   └── public/         # Halaman situs publik dan login
│       └── partials/           # header, navbar, footer, sidebar, flash, pagination
├── routes/                     # web.php, api.php, channels.php, console.php
├── storage/
├── tests/
├── .env.example                # Contoh konfigurasi environment
├── artisan
├── composer.json
├── package.json
└── README.md
```

## Persyaratan

- [Laragon](https://laragon.org/download/) (sudah termasuk Apache, MySQL, PHP, Composer, dan phpMyAdmin)
- PHP 8.1 atau lebih baru (cek di Laragon: klik kanan, **PHP**, **Version**)
- Git

## Cara Install dan Menjalankan di Lokal (Laragon)

### 1. Ambil proyek

Buka **Terminal** Laragon, lalu jalankan:

```bash
cd C:\laragon\www
git clone <URL-REPOSITORI> sistem-monitoring
cd sistem-monitoring
```

Jika memakai file ZIP, ekstrak lalu letakkan foldernya di `C:\laragon\www\sistem-monitoring`.

### 2. Install dependensi

```bash
composer install
```

### 3. Siapkan file environment

```bash
copy .env.example .env
php artisan key:generate
```

Buka file `.env`, lalu sesuaikan koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem-monitoring
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat dan isi database

1. Klik **Start All** di Laragon (Apache dan MySQL harus menyala).
2. Buka phpMyAdmin (`http://localhost/phpmyadmin`, user `root`, password kosong).
3. Buat database baru bernama `sistem-monitoring`.

Pilih **salah satu** cara berikut.

**Cara A: impor file SQL**

Di phpMyAdmin, pilih database `sistem-monitoring`, buka tab **Import**, pilih file `database/sql/sistem-monitoring.sql`, lalu klik **Go**.

**Cara B: migrasi Laravel (otomatis membuat akun dan data contoh)**

```bash
php artisan migrate --seed
```

Perintah ini menjalankan `DatabaseSeeder` yang membuat akun Admin, satu guru contoh, satu kelas dan siswa contoh, serta profil sekolah.

### 5. Hubungkan folder storage

Agar gambar (logo, hero, foto guru, berita, dan kegiatan) tampil:

```bash
php artisan storage:link
```

### 6. Jalankan aplikasi

Pilih salah satu:

- **Laragon Auto Virtual Host:** klik kanan Laragon, **Apache**, **Reload**, lalu buka `http://sistem-monitoring.test`
- **Artisan serve:** jalankan `php artisan serve`, lalu buka `http://127.0.0.1:8000`

Untuk masuk ke panel, klik tombol **Login** di navbar.

## Akun Login Awal

Akun dibuat oleh `database/seeders/DatabaseSeeder.php`. Login memakai **username**.

| Peran | Username | Password |
|---|---|---|
| Admin / TU | `admin` | `admin123` |
| Guru | `198501012010011001` (NIP) | `guru123` |

> Akun di atas hanya untuk pengembangan dan demo lokal. Ganti password-nya sebelum aplikasi dipakai secara nyata.

## Pemecahan Masalah

| Masalah | Solusi |
|---|---|
| Halaman tampil tanpa gaya (CSS tidak termuat) | Pastikan `public/assets/css/style.css` ada dan dipanggil dengan `{{ asset('assets/css/style.css') }}` |
| `No application encryption key has been specified` | Jalankan `php artisan key:generate` |
| `SQLSTATE[HY000] [1049] Unknown database` | Buat database `sistem-monitoring` di phpMyAdmin, lalu impor file `.sql` |
| `SQLSTATE[HY000] [2002] Connection refused` | Pastikan MySQL sudah menyala di Laragon (Start All) |
| Tidak bisa login | Pastikan data akun sudah ada: jalankan `php artisan migrate:fresh --seed` (menghapus dan membuat ulang semua tabel) |
| Gambar tidak tampil | Jalankan `php artisan storage:link` dan pastikan gambar ada di `storage/app/public` |
| `sistem-monitoring.test` tidak bisa dibuka | Reload Apache di Laragon, atau pakai `php artisan serve` |
| Error versi PHP saat `composer install` | Ganti PHP Laragon ke versi 8.1 atau lebih baru |

## Catatan

- File `.env` dan folder `vendor/` tidak ikut di-commit (diabaikan oleh `.gitignore`).
- Skema database mengikuti rancangan ERD pada Tugas 4.