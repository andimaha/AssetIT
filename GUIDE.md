# Matapel IT Asset Management
# Installation Guide

Dokumen ini berisi langkah-langkah untuk menjalankan project **Matapel IT Asset Management** pada komputer baru.

Panduan ini ditujukan untuk user/developer yang belum memiliki project sebelumnya.

---

# 1. Persiapan Awal

Sebelum menjalankan project, pastikan komputer sudah memiliki beberapa aplikasi berikut.

## Required Software

### 1. PHP

Versi minimum:

```
PHP 8.2+
```

Cek instalasi:

```bash
php -v
```

---

### 2. Composer

Composer digunakan untuk menginstall dependency Laravel.

Cek:

```bash
composer -V
```

Jika belum ada, download:

https://getcomposer.org/

---

### 3. Node.js

Digunakan untuk menjalankan asset frontend.

Cek:

```bash
node -v
npm -v
```

Disarankan:

```
Node.js 18+
```

---

### 4. Database Server

Project menggunakan:

```
MySQL
```

Disarankan menggunakan:

- Laragon
- XAMPP
- MySQL Server

Pastikan MySQL dalam keadaan running.

---

# 2. Download Project

Clone project dari GitHub:

```bash
git clone https://github.com/jamesalejandros/MatapelProject2.git
```

Masuk ke folder project:

```bash
cd MatapelProject2
```

---

# 3. Install Dependency Laravel

Jalankan:

```bash
composer install
```

Tunggu hingga proses selesai.

Folder:

```
vendor/
```

akan otomatis dibuat.

---

# 4. Konfigurasi Environment

Laravel membutuhkan file konfigurasi `.env`.

Copy file:

Windows:

```bash
copy .env.example .env
```

Linux/Mac:

```bash
cp .env.example .env
```

---

Generate Laravel Key:

```bash
php artisan key:generate
```

Jika berhasil akan muncul:

```
Application key set successfully.
```

---

# 5. Konfigurasi Database

Buka file:

```
.env
```

Cari bagian database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Sesuaikan menjadi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=matapel_asset
DB_USERNAME=root
DB_PASSWORD=
```

Jika MySQL menggunakan password, isi bagian:

```
DB_PASSWORD
```

---

# 6. Membuat Database

Buka:

- phpMyAdmin
- MySQL Workbench
- Command Line


Buat database baru:

```sql
CREATE DATABASE matapel_asset;
```

---

# 7. Import Database Project

Import file database yang diberikan:

```
matapel_asset.sql
```

Cara melalui phpMyAdmin:

1. Buka phpMyAdmin
2. Pilih database:

```
matapel_asset
```

3. Pilih menu:

```
Import
```

4. Pilih file:

```
matapel_asset.sql
```

5. Klik:

```
Go
```

Tunggu sampai selesai.

---

# 8. Install Frontend Dependency

Install package:

```bash
npm install
```

Setelah selesai lakukan build:

```bash
npm run build
```

---

# 9. Clear Laravel Cache

Jalankan:

```bash
php artisan optimize:clear
```

Untuk Filament:

```bash
php artisan filament:cache-components
```

---

# 10. Membuat User Admin

Project menggunakan Filament Admin Panel.

Buat akun administrator:

```bash
php artisan make:filament-user
```

Isi:

```
Name:
Email:
Password:
```

Contoh:

```
Name:
Administrator

Email:
admin@matapel.com

Password:
********
```

---

# 11. Menjalankan Project

Jalankan Laravel:

```bash
php artisan serve
```

Jika berhasil:

```
Server running on http://127.0.0.1:8000
```

---

# 12. Membuka Aplikasi

Halaman utama:

```
http://127.0.0.1:8000
```

Admin Panel:

```
http://127.0.0.1:8000/admin
```

Login menggunakan akun admin yang telah dibuat.

---

# 13. Mode Development

Jika ingin melakukan perubahan frontend:

Buka terminal baru:

```bash
npm run dev
```

Laravel tetap berjalan:

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

---

# 14. Troubleshooting


## Error:
```
SQLSTATE[HY000] Connection refused
```

Solusi:

Pastikan:

- MySQL berjalan
- Database sudah dibuat
- Konfigurasi `.env` benar


---

## Error:
```
Class not found
```

Jalankan:

```bash
composer dump-autoload
```

Kemudian:

```bash
php artisan optimize:clear
```


---

## Error:
```
Vite manifest not found
```

Jalankan:

```bash
npm run build
```


---

## Error Filament

Jalankan:

```bash
php artisan filament:cache-components
php artisan optimize:clear
```


---

# 15. Struktur Menu Aplikasi


## Asset Management

Berisi:

- Asset
- Mutasi Asset
- Service Asset
- Retire Asset


## Master Data

Berisi:

- Perusahaan
- Vendor
- Lokasi
- Karyawan


## Software Management

Berisi:

- Software
- License
- Assignment Software


## Dashboard

Menampilkan:

- Total Asset
- Status Asset
- Asset berdasarkan perusahaan
- Warranty monitoring


---

# 16. Catatan Penting

Jangan menjalankan:

```bash
php artisan migrate:fresh
```

karena project menggunakan database yang sudah berisi data.

Command tersebut akan menghapus seluruh tabel dan data.

---

# Quick Installation

Jika semua kebutuhan sudah tersedia:

```bash
git clone https://github.com/jamesalejandros/MatapelProject2.git

cd MatapelProject2

composer install

copy .env.example .env

php artisan key:generate

npm install

npm run build

php artisan optimize:clear

php artisan serve
```

Kemudian buka:

```
http://127.0.0.1:8000/admin
```

---

# End of Guide
