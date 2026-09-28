Matapel IT Asset Management
Installation Guide

Dokumen ini berisi langkah-langkah untuk menjalankan project Matapel IT Asset Management pada komputer baru.

Project ini dibangun menggunakan Laravel, Filament, Livewire, Tailwind CSS, Vite, MySQL, dan Spatie Permission.

Panduan ini ditujukan untuk user/developer yang belum memiliki project sebelumnya.

1. Persiapan Awal

Pastikan komputer sudah memiliki software berikut.

Required Software
PHP

Minimum:

PHP 8.2+


Cek:

php -v

Composer

Composer digunakan untuk dependency Laravel.

Cek:

composer -V


Jika belum tersedia, install Composer terlebih dahulu.

Node.js & NPM

Digunakan untuk asset frontend dan Vite.

Cek:

node -v
npm -v


Disarankan menggunakan:

Node.js 18+

MySQL

Project menggunakan:

MySQL


MySQL dapat dijalankan menggunakan:

Laragon

XAMPP

MySQL Server

Pastikan MySQL sudah running.

2. Download Project

Clone repository:

git clone https://github.com/jamesalejandros/MatapelProject2.git


Masuk ke folder project:

cd MatapelProject2


Jika project diberikan dalam bentuk ZIP, extract project kemudian buka terminal pada folder project.

3. Install Dependency

Install dependency PHP:

composer install


Install dependency frontend:

npm install


Folder berikut akan dibuat/tersedia setelah proses instalasi:

vendor/
node_modules/

4. Konfigurasi Environment

Copy file environment.

Windows:

copy .env.example .env


Linux/Mac:

cp .env.example .env


Kemudian generate application key:

php artisan key:generate

5. Konfigurasi Database

Buka:

.env


Sesuaikan konfigurasi database:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=matapel_asset
DB_USERNAME=root
DB_PASSWORD=


Jika MySQL menggunakan password, isi:

DB_PASSWORD=your_password

6. Membuat Database

Buat database:

CREATE DATABASE matapel_asset;


Database dapat dibuat melalui:

phpMyAdmin

MySQL Workbench

MySQL Command Line

7. Database Migration

Project menyediakan migration lengkap pada:

database/migrations/


Migration mencakup antara lain:

User

Asset

Departemen

Karyawan

Perusahaan

Lokasi

Ruangan

Vendor

Software

Software License

Software Assignment

Mutasi Asset

Service Asset

Retire Asset

CCTV Assignment

PABX Assignment

ISP

ISP Bandwidth

ISP Downtime

IT Request

IT Request Approval

Activity Log

Permission & Role

Media Library

Notification

Untuk instalasi database baru, migration dapat dijalankan dengan:

php artisan migrate


Jika menggunakan database yang sudah berisi data production/development, jangan menjalankan migrate:fresh.

8. Database Seeder

Project memiliki beberapa seeder penting pada:

database/seeders/


Seeder utama:

DatabaseSeeder.php
MasterSeeder.php
RolePermissionSeeder.php
UserSeeder.php
AdminUserSeeder.php
ItRequestPermissionSeeder.php


Seeder permission dan user sangat penting karena sistem menggunakan Spatie Permission.

Untuk menjalankan seluruh seeder:

php artisan db:seed


Atau:

php artisan migrate --seed

9. Role & Permission

Project menggunakan role berikut:

super_admin
user
staff_it
kepala_bagian

super_admin

Super Admin mendapatkan akses penuh melalui mekanisme Gate::before().

Super Admin tidak membutuhkan permission satu per satu.

Role:

super_admin

user

User biasa menggunakan role:

user


Secara default user tidak memiliki permission resource.

Permission dapat diberikan secara individual oleh Super Admin melalui User Management.

staff_it

Staff IT menggunakan:

staff_it


Staff IT mendapatkan permission modul Permintaan IT melalui role.

Permission:

itrequest.view
itrequest.create
itrequest.update
itrequest.delete

kepala_bagian

Kepala Bagian menggunakan:

kepala_bagian


Permission:

itrequest.approval.view
itrequest.approval.approve
itrequest.approval.reject


Permission tersebut digunakan untuk melihat, menyetujui, dan menolak Permintaan IT bawahannya.

10. Konfigurasi Akun Seeder

Credential akun hasil seeder dapat diatur melalui .env.

Super Admin
SEED_SUPER_ADMIN_NAME=Super Admin
SEED_SUPER_ADMIN_EMAIL=superadmin@example.com
SEED_SUPER_ADMIN_PASSWORD=12345678

User
SEED_USER_NAME=User
SEED_USER_EMAIL=user@example.com
SEED_USER_PASSWORD=12345678


Setelah konfigurasi, jalankan:

php artisan db:seed


Seeder akan melakukan updateOrCreate, sehingga akun dapat dibuat atau diperbarui.

Untuk environment production, gunakan password yang kuat dan jangan menggunakan password contoh.

11. Konfigurasi Storage

Project menggunakan filesystem Laravel dan media library.

Setelah instalasi, jalankan:

php artisan storage:link


Jika berhasil, Laravel akan membuat symbolic link:

public/storage

12. Build Frontend

Untuk production/build:

npm run build


Untuk development:

npm run dev


Project menggunakan:

Vite
Tailwind CSS
Livewire
Filament

13. Clear Cache

Setelah konfigurasi atau perubahan environment, jalankan:

php artisan optimize:clear


Untuk Filament:

php artisan filament:cache-components


Jika terjadi masalah permission/cache, jalankan kembali:

php artisan optimize:clear

14. Menjalankan Project

Jalankan Laravel:

php artisan serve


Default URL:

http://127.0.0.1:8000


Untuk development frontend, gunakan terminal kedua:

npm run dev


Sehingga:

Terminal 1
php artisan serve

Terminal 2
npm run dev

15. Sistem Login

Project menggunakan satu sistem login utama dengan guard:

web


Login tersedia melalui route Laravel:

/login


Setelah login, halaman tujuan ditentukan berdasarkan role.

Super Admin / User

User dapat diarahkan ke:

/admin


atau:

/permintaan-it


tergantung permission yang dimiliki.

Kepala Bagian

Kepala Bagian diarahkan ke:

/kepala-bagian/permintaan-it

16. Filament Admin Panel

Panel utama aplikasi menggunakan Filament.

URL:

http://127.0.0.1:8000/admin


Middleware:

RedirectUnauthorizedFilamentUser


memiliki aturan utama:

super_admin dapat masuk Filament.

staff_it dapat masuk Filament.

User yang memiliki permission dapat masuk Filament.

User tanpa permission diarahkan ke halaman Permintaan IT.

User tanpa permission tidak diberikan akses ke panel Filament.

17. Modul Aplikasi
Dashboard

Dashboard menyediakan informasi seperti:

Statistik Asset

Asset berdasarkan perusahaan

Asset berdasarkan departemen

Asset berdasarkan lokasi

Status Asset

Jenis Asset

Service Asset

CCTV Assignment

PABX Location

Software Assignment

Software License

Warranty Asset

Asset Management

Modul asset:

Asset

Mutasi Asset

Service Asset

Retire Asset

CCTV Assignment

PABX Assignment

Master Data

Master data meliputi:

Departemen

Karyawan

Kepala Bagian

Perusahaan

Lokasi

Ruangan

Vendor

Sambungan

ISP

Software Management

Meliputi:

Software

Software License

Software Assignment

Terdapat monitoring expiration/license dan end of support.

ISP Management

Meliputi:

ISP

ISP Bandwidth

ISP Downtime

IT Request

Modul Permintaan IT digunakan untuk:

Membuat permintaan IT

Melihat permintaan

Mengubah permintaan

Menghapus permintaan

Menambahkan related user

Menambahkan catatan

Proses approval

Serah terima

Kepala Bagian

Kepala Bagian dapat melihat request bawahannya melalui:

/kepala-bagian/permintaan-it


Fitur utama:

Melihat request

Melihat detail request

Approve request

Reject request

User Management

Super Admin dapat mengelola user dan permission melalui:

User Management


Permission user dapat diberikan secara individual.

Activity Log

Project menggunakan activity log untuk mencatat aktivitas/perubahan data.

Konfigurasi tersedia pada:

config/activitylog.php

18. Struktur Project Penting

Struktur utama project:

app/
├── Filament/
│   ├── Resources/
│   ├── Pages/
│   ├── Forms/
│   ├── Tables/
│   └── Widgets/
│
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
│
├── Livewire/
├── Models/
├── Policies/
└── Providers/

database/
├── migrations/
├── seeders/
└── factories/

resources/
├── css/
├── js/
└── views/

routes/
├── web.php
├── auth.php
├── api.php
└── console.php

19. File Penting

Beberapa file utama yang perlu diketahui developer:

.env
composer.json
package.json
vite.config.js
tailwind.config.js
artisan


Konfigurasi Filament:

app/Providers/Filament/AdminPanelProvider.php


Role & Permission:

database/seeders/RolePermissionSeeder.php


User Seeder:

database/seeders/UserSeeder.php


Routing utama:

routes/web.php


Middleware Filament:

app/Http/Middleware/RedirectUnauthorizedFilamentUser.php


Middleware role:

app/Http/Middleware/CheckRole.php

20. Troubleshooting
SQLSTATE Connection Refused

Contoh:

SQLSTATE[HY000] [2002] Connection refused


Periksa:

MySQL sudah running.

Database sudah dibuat.

DB_HOST benar.

DB_PORT benar.

Username/password database benar.

Class Not Found

Jalankan:

composer dump-autoload


Kemudian:

php artisan optimize:clear

Vite Manifest Not Found

Jalankan:

npm install
npm run build

Filament Bermasalah

Jalankan:

php artisan filament:cache-components
php artisan optimize:clear

Permission Tidak Berubah

Clear permission/cache:

php artisan optimize:clear


Kemudian login kembali.

Pastikan role dan permission diperiksa melalui User Management.

Storage/File Upload Bermasalah

Jalankan:

php artisan storage:link

21. Perhatian Database

Jangan sembarangan menjalankan:

php artisan migrate:fresh


Command tersebut akan menghapus seluruh tabel database dan membuatnya kembali dari awal.

Untuk database yang sudah memiliki data, gunakan:

php artisan migrate


Jika perlu melakukan perubahan database, buat migration baru:

php artisan make:migration nama_migration

22. Quick Installation

Jika semua software sudah tersedia:

git clone https://github.com/jamesalejandros/MatapelProject2.git

cd MatapelProject2

composer install

npm install

copy .env.example .env

php artisan key:generate


Konfigurasi database pada .env, kemudian:

php artisan migrate --seed

php artisan storage:link

php artisan optimize:clear

npm run build

php artisan serve


Buka:

http://127.0.0.1:8000/login


Untuk Filament:

http://127.0.0.1:8000/admin


Untuk Permintaan IT:

http://127.0.0.1:8000/permintaan-it


Untuk Kepala Bagian:

http://127.0.0.1:8000/kepala-bagian/permintaan-it

23. Development

Saat melakukan development, gunakan:

php artisan serve


dan terminal kedua:

npm run dev


Setelah perubahan pada database:

php artisan migrate


Setelah perubahan konfigurasi/cache:

php artisan optimize:clear

End of Guide