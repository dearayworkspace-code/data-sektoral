# Portal Mapping Data Dinas & Google Spreadsheet - BPS

Aplikasi web portal berbasis **Laravel 10** dan **Docker** yang dirancang untuk memudahkan Badan Pusat Statistik (BPS) dan Dinas Pemerintah Daerah dalam proses pengumpulan data terkoordinasi melalui Google Spreadsheet.

---

## 🌟 Fitur Utama

### 1. Role Dinas
* **Login Searchable**: Dropdown dengan fitur pencarian instan untuk memilih instansi/dinas dan input PIN akses.
* **Aktivasi Pertama Kali**: Otomatis mendeteksi jika dinas login pertama kali untuk mengisi data Penanggung Jawab (Nama PIC, Nomor WhatsApp) serta pilihan opsional untuk mengganti PIN.
* **Halaman Perantara (Landing Page)**: Menyajikan ringkasan profil PIC, instruksi BPS, tombol perubahan profil/PIN, serta tombol utama **"Buka Spreadsheet"**.
* **Embedded Google Spreadsheet**: Menampilkan Google Spreadsheet langsung di dalam antarmuka website tanpa melempar pengguna ke luar (*tanpa tombol buka tab baru*).

### 2. Role Administrator BPS
* **Dashboard Statistik**: Memantau total dinas, status aktivasi/PIC, dinas yang belum login, dan dinas yang belum dimapping tautan spreadsheet-nya.
* **Visibilitas PIN Dinas**: Admin dapat melihat PIN asli setiap dinas kapan saja menggunakan tombol ikon mata (dekripsi simetris aman `Crypt::decryptString`).
* **Manajemen CRUD Manual**: Menambah, mengedit, mereset PIN, dan menghapus dinas.
* **Import Massal**:
  - Mengunggah berkas Excel (`.xlsx`) atau CSV (`.csv`) dengan 2 kolom: `Nama Dinas` dan `Link`.
  - Sistem otomatis menetapkan PIN default `123456` untuk dinas baru yang diimpor.
* **Unduh Template Import**: Template CSV siap pakai untuk import massal.
* **Ekspor Rekap CSV**: Mengunduh seluruh rekap dinas, kontak PIC, nomor WhatsApp, status pengisian, dan PIN ke dalam satu berkas CSV.

---

## 🚀 Panduan Menjalankan dengan Docker

### 1. Pastikan Docker Desktop Aktif
Pastikan aplikasi **Docker Desktop** di komputer/server Anda telah berjalan.

### 2. Jalankan Container Docker
Buka terminal pada direktori proyek ini (`/Users/zaki/Documents/Project/data-dinas`), lalu jalankan:

```bash
docker compose up -d --build
```

Container berikut akan otomatis dibuat dan dijalankan:
* `data_dinas_app` (PHP 8.2-FPM & Composer)
* `data_dinas_web` (Nginx Web Server di port `8000`)
* `data_dinas_db` (MariaDB Database di port `33066`)

### 3. Inisialisasi Dependensi & Database
Jalankan perintah berikut di terminal untuk memasang paket Composer dan mengisi database awal:

```bash
# 1. Pasang dependensi Laravel & PhpSpreadsheet
docker compose exec app composer install

# 2. Generate Application Key (jika diperlukan)
docker compose exec app php artisan key:generate

# 3. Jalankan Migrasi Database & Seeder Data Awal
docker compose exec app php artisan migrate --seed
```

---

## 🔑 Akun & Kredensial Default

### 1. Administrator BPS
* **URL**: [http://localhost:8000/admin/login](http://localhost:8000/admin/login)
* **Username**: `admin`
* **Password**: `admin123`

### 2. Role Dinas (Sample Data dari Seeder)
* **URL**: [http://localhost:8000](http://localhost:8000)
* **Daftar Dinas Contoh**:
  - Dinas Kesehatan
  - Dinas Pendidikan dan Kebudayaan
  - Badan Perencanaan Pembangunan Daerah (Bappeda)
  - Dinas Komunikasi dan Informatika
  - Dinas Sosial
* **PIN Default**: `123456`

---

## 📁 Struktur Berkas Penting

```
data-dinas/
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminAuthController.php
│   │   ├── AdminDashboardController.php
│   │   ├── DinasAuthController.php
│   │   └── DinasPortalController.php
│   ├── Http/Middleware/
│   │   ├── AdminAuth.php
│   │   └── DinasAuth.php
│   └── Models/
│       ├── Admin.php
│       └── Dinas.php
├── database/
│   ├── migrations/
│   └── seeders/
├── docker/
│   ├── Dockerfile
│   ├── nginx/default.conf
│   └── php/local.ini
├── resources/views/
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   └── login.blade.php
│   ├── dinas/
│   │   ├── dashboard.blade.php
│   │   ├── embed.blade.php
│   │   ├── first-login.blade.php
│   │   └── login.blade.php
│   └── layouts/app.blade.php
├── routes/web.php
├── docker-compose.yml
└── composer.json
```

