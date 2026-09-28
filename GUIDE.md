# Matapel IT Asset Management
# Installation Guide

Dokumen ini berisi langkah-langkah untuk menjalankan project **Matapel IT Asset Management** pada komputer baru.

Project ini dibangun menggunakan **Laravel, Filament, Livewire, Tailwind CSS, Vite, MySQL, dan Spatie Permission**.

Panduan ini ditujukan untuk user/developer yang belum memiliki project sebelumnya.

---

# 1. Persiapan Awal

Sebelum menjalankan project, pastikan komputer sudah memiliki beberapa aplikasi berikut.

## Required Software

### 1. PHP

Versi minimum:

```text
PHP 8.2+
````

 Cek instalasi:

```
php -v
```

---

 ### 2\. Composer

 Composer digunakan untuk menginstall dependency Laravel.

 Cek:

```
composer -V
```

 Jika belum ada, install Composer terlebih dahulu.

---

 ### 3\. Node.js & NPM

 Digunakan untuk menjalankan asset frontend dan Vite.

 Cek:

```
node -v
npm -v
```

 Disarankan:

```
Node.js 18+
```

---

 ### 4\. Database Server

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

 # 2\. Download Project

 Clone project dari GitHub:

```
git clone https://github.com/jamesalejandros/MatapelProject2.git
```

 Masuk ke folder project:

```
cd MatapelProject2
```

 Jika project diberikan dalam bentuk ZIP, extract project kemudian buka terminal pada folder project.

---

 # 3\. Install Dependency Laravel

 Install dependency PHP:

```
composer install
```

 Tunggu hingga proses selesai.

 Folder:

```
vendor/
```

 akan otomatis dibuat.

---

 # 4\. Install Frontend Dependency

 Install package frontend:

```
npm install
```

 Folder:

```
node_modules/
```

 akan otomatis dibuat.

---

 # 5\. Konfigurasi Environment

 Laravel membutuhkan file konfigurasi `.env`.

 Copy file:

 Windows:

```
copy .env.example .env
```

 Linux/Mac:

```
cp .env.example .env
```

---

 Generate Laravel Key:

```
php artisan key:generate
```

 Jika berhasil akan muncul:

```
Application key set successfully.
```

---

 # 6\. Konfigurasi Database

 Buka file:

```
.env
```

 Sesuaikan bagian database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=matapel_asset
DB_USERNAME=root
DB_PASSWORD=
```

 Jika MySQL menggunakan password, isi:

```
DB_PASSWORD=your_password
```

---

 # 7\. Membuat Database

 Buka salah satu:

 - phpMyAdmin
- MySQL Workbench
- MySQL Command Line

 Buat database baru:

```
CREATE DATABASE matapel_asset;
```

 Pastikan nama database sama dengan konfigurasi:

```
DB_DATABASE=matapel_asset
```

---

 # 8\. Database Migration

 Project menyediakan migration lengkap pada:

```
database/migrations/
```

 Migration mencakup beberapa modul utama:

 - Users
- Assets
- Departemen
- Karyawan
- Perusahaan
- Lokasi
- Ruangan
- Vendor
- Sambungan
- Software
- Software License
- Software Assignment
- Mutasi Asset
- Service Asset
- Retire Asset
- CCTV Assignment
- PABX Assignment
- ISP
- ISP Bandwidth
- ISP Downtime
- IT Request
- IT Request Approval
- Activity Log
- Permission & Role
- Media Library
- Notification

 Untuk database baru, jalankan:

```
php artisan migrate
```

 Jika ingin menjalankan migration sekaligus seeder:

```
php artisan migrate --seed
```

 > **Catatan:** Jangan menggunakan `migrate:fresh` pada database yang sudah memiliki data karena command tersebut akan menghapus seluruh tabel dan data.

---

 # 9\. Database Seeder

 Project memiliki beberapa seeder penting pada:

```
database/seeders/
```

 Seeder utama:

```
RolePermissionSeeder.php
UserSeeder.php
```

 Seeder yang paling penting untuk sistem permission dan user adalah:

```
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=RolePermissionSeeder
```

---

 # 10\. Role & Permission

 Project menggunakan **Spatie Permission**.

 Role utama:

```
super_admin
user
staff_it
kepala_bagian
```

---

 ## 10.1 Super Admin

 Role:

```
super_admin
```

 Super Admin mendapatkan akses penuh melalui mekanisme:

```
Gate::before()
```

 Super Admin tidak membutuhkan permission satu per satu.

 Role `super_admin` sengaja tidak diberikan permission melalui `role_has_permissions`.

---

 ## 10.2 User

 Role:

```
user
```

 User biasa tidak mendapatkan permission resource secara default.

 Permission user dapat diberikan secara individual oleh Super Admin melalui:

```
User Management
```

 Permission individual disimpan melalui:

```
model_has_permissions
```

---

 ## 10.3 Staff IT

 Role:

```
staff_it
```

 Staff IT mendapatkan permission modul Permintaan IT melalui role.

 Permission:

```
itrequest.view
itrequest.create
itrequest.update
itrequest.delete
```

 Permission tersebut diberikan melalui:

```
role_has_permissions
```

---

 ## 10.4 Kepala Bagian

 Role:

```
kepala_bagian
```

 Kepala Bagian mendapatkan permission untuk proses approval Permintaan IT.

 Permission:

```
itrequest.approval.view
itrequest.approval.approve
itrequest.approval.reject
```

 Permission tersebut digunakan untuk:

 - Melihat request bawahan
- Menyetujui request
- Menolak request

 Permission diberikan melalui:

```
role_has_permissions
```

---

 # 11\. Permission Resource

 Permission resource utama menggunakan format:

```
resource.action
```

 Contoh:

```
mstasset.view
mstasset.create
mstasset.update
mstasset.delete
```

 Resource yang memiliki permission antara lain:

```
mstasset
mstdepartemen
mstkaryawan
mstlokasi
mstperusahaan
mstruangan
mstsambungan
mstsoftware
mstsoftwarelicense
mstvendor
trxcctvassignment
trxmutasiasset
trxpabxassignment
trxretireasset
trxserviceasset
trxsoftwareassignment
```

 Setiap resource memiliki permission:

```
.view
.create
.update
.delete
```

---

 # 12\. Konfigurasi Akun Seeder

 Credential akun hasil seeder dapat dikonfigurasi melalui `.env`.

 ## Super Admin

 Tambahkan:

```
SEED_SUPER_ADMIN_NAME=Super Admin
SEED_SUPER_ADMIN_EMAIL=superadmin@example.com
SEED_SUPER_ADMIN_PASSWORD=12345678
```

 ## User

 Tambahkan:

```
SEED_USER_NAME=User
SEED_USER_EMAIL=user@example.com
SEED_USER_PASSWORD=12345678
```

 Setelah konfigurasi, jalankan:

```
php artisan db:seed
```

 Seeder menggunakan:

```
updateOrCreate()
```

 sehingga akun dapat dibuat atau diperbarui tanpa membuat data user duplikat.

 > Untuk environment production, gunakan password yang kuat dan jangan menggunakan password contoh.

---

 # 13\. Konfigurasi Storage

 Project menggunakan filesystem Laravel dan media library.

 Setelah instalasi, jalankan:

```
php artisan storage:link
```

 Jika berhasil, Laravel akan membuat symbolic link:

```
public/storage
```

 Command ini diperlukan terutama untuk fitur yang menggunakan upload atau media.

---

 # 14\. Build Frontend

 Untuk membuat production build:

```
npm run build
```

 Untuk development:

```
npm run dev
```

 Project menggunakan:

```
Vite
Tailwind CSS
Livewire
Filament
```

---

 # 15\. Clear Laravel Cache

 Setelah melakukan perubahan konfigurasi `.env`, permission, atau aplikasi, jalankan:

```
php artisan optimize:clear
```

 Untuk cache component Filament:

```
php artisan filament:cache-components
```

 Jika terjadi masalah cache atau permission:

```
php artisan optimize:clear
```

 Kemudian login kembali.

---

 # 16\. Menjalankan Project

 Jalankan Laravel:

```
php artisan serve
```

 Jika berhasil akan tersedia pada:

```
http://127.0.0.1:8000
```

 Untuk development frontend, gunakan terminal kedua:

```
npm run dev
```

 Sehingga:

```
Terminal 1:
php artisan serve
```

```
Terminal 2:
npm run dev
```

---

 # 17\. Sistem Login

 Project menggunakan **satu sistem login utama** dengan guard:

```
web
```

 Login tersedia melalui:

```
/login
```

 Authentication utama menggunakan route:

```
routes/auth.php
```

 Tidak menggunakan sistem login terpisah untuk Kepala Bagian.

---

 ## 17.1 User Biasa

 Setelah login, user biasa diarahkan ke:

```
/permintaan-it
```

 User dapat mengakses fitur Permintaan IT sesuai authorization yang dimiliki.

---

 ## 17.2 Kepala Bagian

 User dengan role:

```
kepala_bagian
```

 diarahkan ke:

```
/kepala-bagian/permintaan-it
```

 Halaman ini digunakan untuk proses approval Permintaan IT.

---

 ## 17.3 Super Admin

 Super Admin dapat mengakses:

```
/admin
```

 Super Admin memiliki akses penuh melalui mekanisme authorization aplikasi.

---

 # 18\. Filament Admin Panel

 Panel administrator menggunakan:

```
Filament
```

 URL:

```
http://127.0.0.1:8000/admin
```

 Konfigurasi panel berada pada:

```
app/Providers/Filament/AdminPanelProvider.php
```

---

 ## RedirectUnauthorizedFilamentUser

 Middleware:

```
app/Http/Middleware/RedirectUnauthorizedFilamentUser.php
```

 memiliki aturan utama:

 - `super_admin` dapat masuk Filament.
- `staff_it` dapat masuk Filament.
- User yang memiliki permission dapat masuk Filament.
- User tanpa permission tidak dapat menggunakan panel Filament.
- User tanpa permission akan diarahkan ke:

```
/permintaan-it
```

 Hal ini membuat user biasa tanpa permission resource tetap dapat menggunakan modul Permintaan IT di luar Filament.

---

 # 19\. Modul Aplikasi

 ## Dashboard

 Dashboard menyediakan informasi seperti:

 - Total/statistik Asset
- Asset berdasarkan perusahaan
- Asset berdasarkan departemen
- Asset berdasarkan lokasi
- Status Asset
- Jenis Asset
- Service Asset berdasarkan tahun
- CCTV Assignment
- PABX Location
- Software Assignment
- Software License
- Warranty Asset

 Widget dashboard berada pada:

```
app/Filament/Widgets/
```

---

 ## Asset Management

 Modul Asset Management meliputi:

 - Asset
- Mutasi Asset
- Service Asset
- Retire Asset
- CCTV Assignment
- PABX Assignment

 Resource Filament berada pada:

```
app/Filament/Resources/
```

---

 ## Master Data

 Master Data meliputi:

 - Departemen
- Karyawan
- Kepala Bagian
- Perusahaan
- Lokasi
- Ruangan
- Vendor
- Sambungan
- ISP

---

 ## Software Management

 Software Management meliputi:

 - Software
- Software License
- Software Assignment

 Project juga menyediakan monitoring:

 - License expiration
- License overview
- License summary
- End of support
- Software assignment berdasarkan perusahaan

---

 ## ISP Management

 ISP Management meliputi:

 - ISP
- ISP Bandwidth
- ISP Downtime

 Resource terkait berada pada:

```
app/Filament/Resources/MstIsp
app/Filament/Resources/TrxIspBandwidths
app/Filament/Resources/TrxIspDowntimes
```

---

 ## IT Request

 Modul Permintaan IT digunakan untuk:

 - Membuat permintaan IT
- Melihat permintaan
- Mengubah permintaan
- Menghapus permintaan
- Menambahkan related user
- Menambahkan catatan
- Melihat detail request
- Proses approval
- Serah terima

 Route utama:

```
/permintaan-it
```

 Controller:

```
app/Http/Controllers/ItRequestController.php
```

 Model utama:

```
app/Models/ItRequest.php
```

---

 ## Kepala Bagian

 Kepala Bagian dapat melihat request bawahannya melalui:

```
/kepala-bagian/permintaan-it
```

 Fitur utama:

 - Melihat request
- Melihat detail request
- Approve request
- Reject request

 Controller:

```
app/Http/Controllers/KepalaBagian/ItRequestApprovalController.php
```

---

 ## User Management

 Super Admin dapat mengelola user melalui:

```
User Management
```

 Resource:

```
app/Filament/Resources/UserManagements/
```

 User Management digunakan untuk mengatur:

 - User
- Role
- Permission
- Permission individual

---

 ## Activity Log

 Project menggunakan activity log untuk mencatat aktivitas/perubahan data.

 Konfigurasi:

```
config/activitylog.php
```

 Migration:

```
database/migrations/2026_09_07_035411_change_subject_id_to_string_in_activity_log_table.php
```

---

 # 20\. Struktur Project Penting

 Struktur utama project:

```
app/
├── Console/
├── Exports/
├── Filament/
│   ├── Exports/
│   ├── Forms/
│   ├── Pages/
│   ├── Resources/
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
│   └── Concerns/
├── Policies/
├── Providers/
└── View/

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
└── views/

routes/
├── api.php
├── auth.php
├── console.php
└── web.php
```

---

 # 21\. File Penting

 Beberapa file utama yang perlu diketahui developer:

```
.env
.env.example
composer.json
composer.lock
package.json
package-lock.json
vite.config.js
tailwind.config.js
artisan
```

---

 ## Konfigurasi Filament

```
app/Providers/Filament/AdminPanelProvider.php
```

---

 ## Role & Permission

```
database/seeders/RolePermissionSeeder.php
```

---

 ## User Seeder

```
database/seeders/UserSeeder.php
```

---

 ## Database Seeder

```
database/seeders/DatabaseSeeder.php
```

---

 ## Routing Utama

```
routes/web.php
```

---

 ## Authentication

```
routes/auth.php
```

---

 ## Middleware Filament

```
app/Http/Middleware/RedirectUnauthorizedFilamentUser.php
```

---

 ## Middleware Role

```
app/Http/Middleware/CheckRole.php
```

---

 ## IT Request Controller

```
app/Http/Controllers/ItRequestController.php
```

---

 ## Kepala Bagian Controller

```
app/Http/Controllers/KepalaBagian/ItRequestApprovalController.php
```

---

 # 22\. Troubleshooting

 ## Error: SQLSTATE Connection Refused

 Contoh:

```
SQLSTATE[HY000] [2002] Connection refused
```

 Solusi:

 Pastikan:

 - MySQL berjalan.
- Database sudah dibuat.
- `DB_HOST` benar.
- `DB_PORT` benar.
- Username database benar.
- Password database benar.
- Nama database pada `.env` benar.

 Setelah memperbaiki `.env`, jalankan:

```
php artisan optimize:clear
```

---

 ## Error: Class Not Found

 Jalankan:

```
composer dump-autoload
```

 Kemudian:

```
php artisan optimize:clear
```

---

 ## Error: Vite Manifest Not Found

 Jalankan:

```
npm install
npm run build
```

 Untuk development:

```
npm run dev
```

---

 ## Error Filament

 Jalankan:

```
php artisan filament:cache-components
php artisan optimize:clear
```

 Kemudian coba login kembali.

---

 ## Permission Tidak Berubah

 Clear cache:

```
php artisan optimize:clear
```

 Pastikan user memiliki role yang benar.

 Periksa juga permission melalui:

```
User Management
```

 Untuk user biasa, permission resource diberikan secara individual.

 Untuk Staff IT dan Kepala Bagian, permission tertentu berasal dari role.

---

 ## Storage / File Upload Bermasalah

 Jalankan:

```
php artisan storage:link
```

 Kemudian:

```
php artisan optimize:clear
```

---

 # 23\. Perhatian Database

 Jangan sembarangan menjalankan:

```
php artisan migrate:fresh
```

 Command tersebut akan:

 - Menghapus seluruh tabel.
- Menghapus seluruh data.
- Menjalankan ulang seluruh migration.

 Untuk database yang sudah memiliki data, gunakan:

```
php artisan migrate
```

 Jika perlu melakukan perubahan struktur database, buat migration baru:

```
php artisan make:migration nama_migration
```

 Kemudian jalankan:

```
php artisan migrate
```

---

 # 24\. Quick Installation

 Jika semua kebutuhan sudah tersedia:

```
git clone https://github.com/jamesalejandros/MatapelProject2.git

cd MatapelProject2

composer install

npm install

copy .env.example .env

php artisan key:generate
```

 Konfigurasi database pada:

```
.env
```

 Kemudian buat database:

```
CREATE DATABASE matapel_asset;
```

 Jalankan migration dan seeder:

```
php artisan migrate --seed
```

 Buat storage link:

```
php artisan storage:link
```

 Clear cache:

```
php artisan optimize:clear
```

 Build frontend:

```
npm run build
```

 Jalankan Laravel:

```
php artisan serve
```

---

 # 25\. URL Aplikasi

 Login:

```
http://127.0.0.1:8000/login
```

 Dashboard:

```
http://127.0.0.1:8000/dashboard
```

 Filament Admin Panel:

```
http://127.0.0.1:8000/admin
```

 Permintaan IT:

```
http://127.0.0.1:8000/permintaan-it
```

 Kepala Bagian:

```
http://127.0.0.1:8000/kepala-bagian/permintaan-it
```

 Profile:

```
http://127.0.0.1:8000/profile
```

---

 # 26\. Development Mode

 Untuk menjalankan aplikasi dalam mode development:

 ### Terminal 1

```
php artisan serve
```

 ### Terminal 2

```
npm run dev
```

 Setelah perubahan database:

```
php artisan migrate
```

 Setelah perubahan konfigurasi:

```
php artisan optimize:clear
```

 Setelah perubahan frontend untuk production:

```
npm run build
```

---

 # 27\. Catatan Developer

 Beberapa hal penting yang perlu diperhatikan:

 - Jangan menghapus migration lama yang sudah digunakan database.
- Gunakan migration baru untuk perubahan struktur database.
- Jangan menjalankan `migrate:fresh` pada database yang berisi data penting.
- Jangan menyimpan password production di repository.
- Pastikan `.env` tidak ikut di-commit.
- Setelah perubahan `.env`, jalankan `php artisan optimize:clear`.
- Setelah perubahan frontend production, jalankan `npm run build`.
- Setelah perubahan permission, lakukan clear cache dan login kembali.
- Permission user biasa diberikan secara individual melalui User Management.
- `super_admin` mendapatkan akses penuh melalui mekanisme `Gate::before()`.
- Staff IT menggunakan permission Permintaan IT melalui role `staff_it`.
- Kepala Bagian menggunakan permission approval melalui role `kepala_bagian`.

---

 # End of Guide