# 🎓 Sistem Presensi & Alumni Digital Sekolah (SIMPAD)

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11%2F12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-v3-FAA700?style=for-the-badge&logo=filament&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Flutter](https://img.shields.io/badge/Flutter-Mobile_App-02569B?style=for-the-badge&logo=flutter&logoColor=white)
![WhatsApp](https://img.shields.io/badge/WhatsApp_API-Fonnte-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)

**Sistem Manajemen Presensi Sekolah Real-Time (Manual & QR Code) dan Portal Alumni Digital berbasis Laravel, Filament Admin Panel, serta Flutter Mobile App.**

[Fitur Utama](#-fitur-unggulan) • [Arsitektur Sistem](#-arsitektur--tech-stack) • [Instalasi & Quick Start](#-quick-start-local-development) • [Data Dummy & Akun Demo](#-data-dummy--akun-demo-real-time) • [API Documentation](#-daftar-endpoint-api-mobile)

</div>

---

## 📋 Deskripsi

**Sistem Presensi & Alumni Digital Sekolah (SIMPAD)** adalah platform manajemen sekolah terpadu yang dirancang khusus untuk memodernisasi aktivitas presensi jam pelajaran oleh guru di kelas, pemantauan realtime oleh sekolah dan orang tua, hingga penelusuran lulusan (*Tracer Study*) untuk alumni SMK/SMA.

Sistem ini dikembangkan menggunakan **clean architecture** dengan **Service Layer**, **Form Request**, **API Resource**, **Spatie Permission**, dan **Database Transaction** yang memastikan keandalan tinggi dan kemudahan pengembangan di masa depan.

---

## 🌟 Fitur Unggulan

### 1. 🕒 Presensi Berbasis Sesi & Jam Pelajaran (Real-Time)
* **Bukan sekadar presensi tanggal**, tetapi berdasarkan **jam pelajaran aktif** (J1 - J8) sesuai jadwal mengajar guru.
* **Allow Early Open**: Guru dapat membuka kelas sebelum jam dimulai jika diizinkan oleh sistem/admin.
* **Mencegah Duplikasi**: Satu jadwal hanya boleh memiliki satu sesi aktif (*Open*), dan setiap siswa dicatat secara unik per hari/sesi.

### 2. 📱 Mode Presensi Fleksibel (Manual & QR Code)
* **Presensi Manual**: Guru dapat menandai status siswa (*Hadir, Terlambat, Izin, Sakit, Alpha*) beserta catatan khusus (misal: *"Terlambat karena macet"*).
* **QR Code Attendance**: Backend mengenerate **QR Token** dinamis dengan masa berlaku 5 menit. Siswa melakukan scan via mobile app untuk presensi instan.

### 3. 💬 Notifikasi WhatsApp Otomatis (Fonnte API)
* Terintegrasi dengan **Fonnte WhatsApp Gateway** menggunakan *Queue & Background Job* (`SendWhatsAppNotification`).
* Otomatis mengirimkan pesan pemberitahuan status kehadiran anak kepada nomor WhatsApp Orang Tua/Wali secara *real-time*.

### 4. 🎓 Portal Alumni & Tracer Study
* Registrasi, verifikasi admin, dan pembaruan profil karir alumni (*Bekerja, Kuliah, Wirausaha, Belum Bekerja*).
* **Lowongan Kerja (Job Vacancies)**: Bursa kerja dan info magang khusus alumni.
* **Agenda & Event Alumni**: Reuni akbar, webinar karir, dan workshop.
* **Visualisasi Interaktif**: Dilengkapi widget grafik statistik karir alumni di dashboard Filament.

### 5. 📊 Dashboard Admin & Realtime Charts (Filament v3)
* **Realtime Attendance Overview Widget**: Memantau kelas yang sedang berlangsung secara live saat ini, kelas belum dibuka, dan kelas selesai.
* **Filter Lengkap**: Filter laporan berdasarkan Hari, Guru, Kelas, Mata Pelajaran, dan Status.

---

## 🏗️ Arsitektur & Tech Stack

```
┌─────────────────────────────────────────────────────────────┐
│                      FRONTEND & MOBILE                      │
├─────────────────────────────────────────────────────────────┤
│  📱 Flutter App (Android/iOS)  │  🌐 Filament Web Admin    │
│  - Guru (Buka Kelas & Input)   │  - Super Admin & Admin    │
│  - Siswa (Scan QR & Riwayat)   │  - Master Data & Laporan  │
│  - Orang Tua (Monitoring WA)   │  - Portal Alumni & Tracer │
└──────────────────────────────┬──────────────────────────────┘
                               │ REST API (JSON) + Sanctum Token
┌──────────────────────────────▼──────────────────────────────┘
│                      BACKEND (LARAVEL 12)                   │
├─────────────────────────────────────────────────────────────┤
│  ⚡ Service Layer │ 🛡️ Spatie RBAC │ 📦 API Resource / Request │
│  - AttendanceService  - Role & Permission  - Clean Architecture │
│  - WhatsAppService    - 6 Role Khusus      - Database Tx        │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                    DATABASE & QUEUE                         │
├─────────────────────────────────────────────────────────────┤
│  🗄️ MySQL 8.0 (19+ Tabel)      │  📨 Redis / Database Queue │
└─────────────────────────────────────────────────────────────┘
```

### Stack Teknologi
* **Backend Framework**: Laravel 12 (PHP 8.2+)
* **Admin Panel**: Filament PHP v3
* **Authentication**: Laravel Sanctum (Token-based API Auth)
* **Role & Permission**: Spatie Laravel Permission
* **Database**: MySQL 8.0
* **Background Jobs / Notifications**: Laravel Queue + Fonnte WhatsApp API
* **Testing**: PHPUnit / Laravel Feature Tests

---

## 🚀 Quick Start (Local Development)

### 1. Prasyarat Sistem
* PHP >= 8.2
* Composer >= 2.x
* MySQL >= 8.0
* Node.js & NPM (untuk build aset eksternal jika diperlukan)

### 2. Langkah Instalasi

```powershell
# 1. Clone repositori & masuk ke direktori proyek
git clone https://github.com/your-org/sistem-presensi-alumni.git
cd "sistem-presensi-alumni-digital-sekolah"

# 2. Install dependensi Composer
composer install

# 3. Salin file environment & generate application key
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi Database di dalam file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sistem_presensi_alumni_digital_sekolah
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Jalankan migrasi & seed data dummy skala besar
php artisan migrate:fresh --seed

# 6. Buat symbolic link untuk storage gambar/attachment
php artisan storage:link

# 7. Jalankan development server
php artisan serve
```

Aplikasi sekarang dapat diakses melalui:
* **Admin Panel Web**: `http://127.0.0.1:8000/admin`
* **API Base URL**: `http://127.0.0.1:8000/api/v1`

---

## 🧪 Data Dummy & Akun Demo Real-Time

Sistem dilengkapi dengan seeder skala besar ([`DummyDataSeeder.php`](file:///d:/Backend%20Sistem%20Presensi%20&%20Alumni%20Digital%20Sekolah/sistem-presensi-alumni-digital-sekolah/database/seeders/DummyDataSeeder.php) & [`PresensiSessionSeeder.php`](file:///d:/Backend%20Sistem%20Presensi%20&%20Alumni%20Digital%20Sekolah/sistem-presensi-alumni-digital-sekolah/database/seeders/PresensiSessionSeeder.php)) yang mensimulasikan lingkungan sekolah nyata:
* **1 Sekolah SMK** (`SMK Negeri 1 Digital Presensi & Alumni`)
* **10 Guru Pengajar** dengan mata pelajaran berbeda (Web Dev, Matematika, B. Inggris, Jaringan, dll.)
* **8 Kelas** (X, XI, XII RPL & TKJ)
* **160 Siswa & 160 Akun Orang Tua** dengan nomor WhatsApp aktif
* **Riwayat Presensi 14 Hari** (Ribuan record presensi untuk grafik statistik)
* **Sesi Live Hari Ini**: Ada sesi pagi (selesai) dan sesi yang **sedang berlangsung (*Open*) tepat saat ini** lengkap dengan QR Token aktif!
* **60+ Alumni (2021-2025)** dengan profil karir, 10 lowongan kerja, dan 5 agenda event.

### 🔑 Akun Login Siap Pakai (Password semua akun: `password`)

| Role | Email Login | Keterangan / Kegunaan |
| :--- | :--- | :--- |
| **👑 Super Admin** | `superadmin@simpad.app` | Akses penuh seluruh konfigurasi & sistem |
| **🛡️ Admin Sekolah** | `admin@smkn1demo.sch.id` | Kelola master data, jadwal, dan rekap presensi |
| **👨‍🏫 Guru Utama** | `guru@smkn1demo.sch.id` | *Budi Santoso, S.Kom.* (Demo buka kelas & input presensi mobile) |
| **👨‍🎓 Siswa Utama** | `siswa@smkn1demo.sch.id` | Demo scan QR Code & lihat riwayat presensi |
| **🎓 Alumni Utama** | `ahmad.fauzi2024@alumni.test` | Demo portal alumni, lowongan kerja, dan event |
| **👨‍👩‍👦 Orang Tua** | `ortu_202601001@sekolah.id` | Demo pemantauan kehadiran anak & notifikasi WA |

---

## 🔐 Role & Hak Akses (Spatie RBAC)

| Permission | Super Admin | Admin Sekolah | Guru | Siswa | Orang Tua | Alumni |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **Kelola Master Sekolah & User** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Kelola Jadwal & Jam Pelajaran** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Buka Kelas & Input Presensi** | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Generate & Scan QR Attendance** | ❌ | ✅ | ✅ (Gen) | ✅ (Scan) | ❌ | ❌ |
| **Lihat Rekap Presensi** | ✅ | ✅ | ✅ (Kelasnya) | ✅ (Sendiri) | ✅ (Anak) | ❌ |
| **Kelola Data & Verifikasi Alumni** | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ (Sendiri) |
| **Posting Lowongan & Event** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Akses Admin Panel Web** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |

---

## 📡 Daftar Endpoint API Mobile

Semua endpoint API dilindungi oleh middleware Sanctum (`auth:sanctum`) kecuali endpoint login/register. Sertakan header `Authorization: Bearer <token>` dan `Accept: application/json`.

### 1. Autentikasi (`/api/v1`)
* `POST /login` - Login pengguna (mengembalikan token & role)
* `POST /logout` - Revoke token aktif
* `GET /me` - Ambil profil pengguna saat ini

### 2. Mobile Flow Guru (`/api/v1/teacher` & `/api/v1/attendance`)
* `GET /teacher/today` - Mengambil daftar kelas/jadwal yang ajar hari ini (diurutkan: Berlangsung, Akan Dimulai, Selesai)
* `POST /attendance/open` - Membuka kelas / sesi presensi (Mendukung *Allow Early Open*)
* `POST /attendance/manual` - Menyimpan atau memperbarui status presensi manual siswa (*Hadir, Terlambat, Izin, Sakit, Alpha*)
* `POST /attendance/generate-qr` - Mengenerate token QR Code presensi (berlaku 5 menit)
* `POST /attendance/close` - Menutup sesi kelas
* `GET /attendance/session/{id}` - Detail sesi presensi beserta daftar kehadiran siswa
* `GET /attendance/history` - Riwayat sesi mengajar guru

### 3. Presensi Siswa & QR (`/api/v1/student` & `/api/v1/attendance`)
* `POST /attendance/scan` - Siswa scan QR Code untuk presensi instan
* `GET /student/attendances` - Riwayat kehadiran pribadi siswa
* `GET /student/schedule` - Jadwal pelajaran siswa hari ini

### 4. Portal Alumni (`/api/v1/alumni`)
* `POST /alumni/register` - Pendaftaran alumni baru
* `GET /alumni/profile` - Lihat profil & status karir
* `POST /alumni/profile` - Perbarui data tracer study (Bekerja/Kuliah/Wirausaha)
* `GET /alumni/vacancies` - Daftar lowongan kerja aktif
* `GET /alumni/events` - Daftar agenda & event alumni

---

## 🧪 Automated Testing

Proyek ini dilengkapi dengan Feature Testing komprehensif menggunakan PHPUnit / Laravel Testing.

Untuk menjalankan pengujian alur presensi mobile guru secara otomatis:

```powershell
php artisan test --filter=MobileTeacherAttendanceFlowTest
```

**Pengujian yang dicakup dalam test ini:**
1. ✅ Login Guru & Verifikasi Token Sanctum
2. ✅ Pengambilan Jadwal Hari Ini (`/api/v1/teacher/today`)
3. ✅ Membuka Sesi Kelas (`/api/v1/attendance/open`)
4. ✅ Input Presensi Manual 5 Status (`Hadir, Terlambat, Izin, Sakit, Alpha`)
5. ✅ Verifikasi Dispatch Job Notifikasi WhatsApp (`SendWhatsAppNotification`)
6. ✅ Penutupan Kelas (`/api/v1/attendance/close`)

---

## 📁 Struktur Kode & Clean Architecture

```
app/
├── Enums/                     # Enum Status (AttendanceStatus, SessionStatus, dll)
├── Filament/                  # Web Admin Resources, Pages, dan Widgets
│   ├── Resources/             # CRUD Sekolah, Guru, Siswa, Jadwal, Alumni
│   └── Widgets/               # RealtimeAttendanceOverview, StatsOverview, AlumniChart
├── Http/
│   ├── Controllers/Api/       # Controller API Mobile (Tanpa Business Logic)
│   ├── Requests/              # Form Request Validation
│   └── Resources/             # API Resource JSON Transformation
├── Jobs/                      # Background Jobs (SendWhatsAppNotification)
├── Models/                    # Eloquent Models (19+ Models dengan Relationship)
├── Policies/                  # Authorization Policies (SchedulePolicy, AlumniPolicy)
└── Services/                  # Business Logic Layer
    ├── AttendanceService.php  # Logika presensi, validasi sesi, & rekap
    └── WhatsAppService.php    # Gateway Fonnte & formatting nomor telepon
```

---

## 📄 Lisensi

Proyek **Sistem Presensi & Alumni Digital Sekolah** dikembangkan untuk kebutuhan manajemen internal sekolah dan pengujian sistem canggih. Hak Cipta Dilindungi Undang-Undang.

*Terakhir Diperbarui: **6 Juli 2026***
