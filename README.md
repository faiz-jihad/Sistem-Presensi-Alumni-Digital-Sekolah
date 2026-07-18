# 🎓 Sistem Presensi & Alumni Digital Sekolah (SIMPAD)

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11%2F12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-v3-FAA700?style=for-the-badge&logo=filament&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Flutter](https://img.shields.io/badge/Flutter-Mobile_App-02569B?style=for-the-badge&logo=flutter&logoColor=white)
![WhatsApp](https://img.shields.io/badge/WhatsApp-Node.js_Gateway-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)
![Firebase FCM](https://img.shields.io/badge/Firebase-FCM-FFCA28?style=for-the-badge&logo=firebase&logoColor=black)

**Sistem Manajemen Presensi Sekolah Real-Time (Manual & QR Code), Portal Alumni Digital, Kehadiran Guru Mandiri, Notifikasi Push FCM, serta Integrasi Dual-Mode WhatsApp Gateway.**

[Fitur Utama](#-fitur-unggulan) • [Arsitektur Sistem](#-arsitektur--tech-stack) • [Instalasi & Quick Start](#-quick-start-local-development) • [Konfigurasi Integrasi](#%EF%B8%8F-konfigurasi-layanan-pihak-ketiga) • [Data Dummy & Akun Demo](#-data-dummy--akun-demo-real-time) • [API Documentation](#-daftar-endpoint-api-mobile)

</div>

---

## 📋 Deskripsi

**Sistem Presensi & Alumni Digital Sekolah (SIMPAD)** 
adalah platform manajemen sekolah terpadu yang dirancang khusus untuk memodernisasi aktivitas presensi jam pelajaran oleh guru di kelas, kehadiran guru mandiri, pemantauan real-time oleh sekolah dan orang tua via WhatsApp & Push Notification, hingga penelusuran lulusan (*Tracer Study*) dan verifikasi admin untuk alumni SMA/SMK.

Sistem ini dikembangkan menggunakan **clean architecture** dengan **Service Layer**, **Form Request**, **API Resource**, **Spatie Permission**, dan **Database Transaction** yang memastikan keandalan tinggi dan kemudahan pengembangan di masa depan.

---

## 🌟 Fitur Unggulan

### 1. 🕒 Presensi Berbasis Sesi & Jam Pelajaran (Real-Time)
* **Presensi Detil**: Kehadiran dicatat berdasarkan **jam pelajaran aktif** (J1 - J8) sesuai jadwal mengajar guru, bukan sekadar presensi harian biasa.
* **Allow Early Open**: Guru dapat membuka kelas sebelum jam mulai jika diizinkan oleh sistem/admin.
* **Pencegahan Duplikasi**: Satu jadwal hanya diperbolehkan memiliki satu sesi aktif (*Open*), dan setiap siswa dicatat secara unik per sesi.

### 2. 📱 Mode Presensi Fleksibel (Manual & QR Code)
* **Presensi Manual**: Guru dapat menandai status siswa (*Hadir, Terlambat, Izin, Sakit, Alpha*) beserta catatan khusus (misal: *"Izin ke toilet"*).
* **QR Code Attendance**: Backend mengenerate **QR Token** dinamis dengan masa berlaku 5 menit. Siswa melakukan scan via mobile app untuk melakukan presensi instan mandiri.

### 3. 💬 Dual-Mode WhatsApp Notification Gateway
* **Local Gateway (Node.js)**: Menyediakan server WhatsApp lokal mandiri menggunakan [whatsapp-service.js](file:///c:/laragon/www/Sistem-Presensi-Alumni-Digital-Sekolah/whatsapp-service.js) (berbasis `whatsapp-web.js` dan Express) dengan pendeteksian path Google Chrome otomatis di Windows.
* **Fonnte API Gateway**: Integrasi opsional dengan layanan cloud Fonnte API menggunakan authorization token.
* **Asinkron & Handal**: Pengiriman pesan ke orang tua/wali siswa saat presensi diproses asinkron menggunakan *Queue & Background Job* (`SendWhatsAppNotification`).

### 🔔 4. Push Notifications & Web Push (FCM & VAPID)
* Notifikasi push asinkron langsung ke perangkat mobile (Flutter) menggunakan **Firebase Cloud Messaging (FCM)**.
* Notifikasi web push langsung ke web browser admin panel (menggunakan keys VAPID/WebPush).
* Endpoint khusus pendaftaran token FCM perangkat (`POST /api/v1/device-token`) dan subscribe Web Push (`POST /webpush/subscribe`).

### 🔑 5. Google OAuth 2.0 Single Sign-On (SSO)
* **Web Login**: Admin/Super Admin dapat login ke Filament Admin Panel menggunakan akun Google mereka secara aman.
* **Mobile Login**: API Auth Google (`POST /api/v1/auth/google`) memverifikasi ID Token Google dari aplikasi Flutter/mobile.

### 👨‍🏫 6. Kehadiran Guru Mandiri (Teacher Attendance)
* Guru dapat melakukan check-in dan check-out kehadiran harian mereka sendiri langsung dari mobile app (`POST /api/v1/teacher/check-in` & `/api/v1/teacher/check-out`).

### 🎓 7. Portal Alumni & Tracer Study Terpadu
* Registrasi alumni baru, penelusuran lulusan (*Tracer Study*: Bekerja, Kuliah, Wirausaha, Belum Bekerja), publikasi Lowongan Kerja (Job Vacancies), dan Agenda/Event Alumni.
* **Sistem Verifikasi Alumni**: Admin memiliki kontrol penuh untuk menyetujui (*Approve*), menolak (*Reject* dengan alasan), atau mengembalikan status ke pending (*Reset*) pada registrasi alumni baru.

### 📊 8. Dashboard Admin & Laporan (Filament v3)
* Widget statistik karir alumni interaktif (Alumni Chart), rekap presensi harian/bulanan, dan status live kelas hari ini.
* Dukungan ekspor laporan presensi dan data alumni ke berkas Excel/CSV.

### 🛡️ 9. Cloudflare Tunnel & HTTPS Proxy Support
* Middleware dikonfigurasi untuk mempercayai proksi SSL, memastikan seluruh asset CSS/JS dan URL redirect ter-load secara aman via HTTPS saat dideploy di balik Cloudflare Tunnel.

---

## 🏗️ Arsitektur & Tech Stack

```
┌─────────────────────────────────────────────────────────────┐
│                      FRONTEND & MOBILE                      │
├─────────────────────────────────────────────────────────────┤
│  📱 Flutter App (Android/iOS)  │  🌐 Filament Web Admin    │
│  - Guru (Presensi, Check-in)   │  - Super Admin & Admin    │
│  - Siswa (Scan QR & Izin)      │  - Verifikasi Alumni & Job│
│  - Notifikasi Push FCM         │  - Laporan & Web Push     │
└──────────────────────────────┬──────────────────────────────┘
                               │ REST API (JSON) + Sanctum Token
┌──────────────────────────────▼──────────────────────────────┐
│                      BACKEND (LARAVEL 13)                   │
├─────────────────────────────────────────────────────────────┤
│  ⚡ Service Layer │ 🛡️ Spatie RBAC │ 📦 API Resource / Request │
│  - AttendanceService  - Role & Permission  - Clean Architecture │
│  - WhatsAppService    - 6 Role Khusus      - Database Tx        │
│  - FirebaseNotificationService                              │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                    DATABASE & QUEUE                         │
├─────────────────────────────────────────────────────────────┤
│  🗄️ MySQL 8.0                 │  📨 Redis / Database Queue │
└─────────────────────────────────────────────────────────────┘
```

### Stack Teknologi
* **Backend Framework**: Laravel 13 (PHP 8.3+)
* **Admin Panel**: Filament PHP v5.6
* **Authentication**: Laravel Sanctum & Google OAuth 2.0
* **Role & Permission**: Spatie Laravel Permission
* **Database**: MySQL 8.0
* **Push Notifications**: Firebase Cloud Messaging (FCM) & Web Push
* **WhatsApp Service**: Local Node.js Gateway (`whatsapp-web.js`)
* **Testing**: PHPUnit / Laravel Feature Tests

---

## 🚀 Quick Start (Local Development)

### 1. Prasyarat Sistem
* PHP >= 8.2
* Composer >= 2.x
* MySQL >= 8.0
* Node.js & NPM (untuk gateway WhatsApp lokal & build asset)

### 2. Langkah Instalasi

```powershell
# 1. Clone repositori & masuk ke direktori proyek
git clone https://github.com/your-org/sistem-presensi-alumni.git
cd "Sistem-Presensi-Alumni-Digital-Sekolah"

# 2. Install dependensi PHP & JavaScript
composer install
npm install

# 3. Salin file environment & generate application key
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi Database di dalam file .env (buat database kosong terlebih dahulu)
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

# 7. Jalankan development server Laravel
php artisan serve
```

Aplikasi sekarang dapat diakses melalui:
* **Admin Panel Web**: `http://127.0.0.1:8000/admin`
* **API Base URL**: `http://127.0.0.1:8000/api/v1`

---

## ⚙️ Konfigurasi Layanan Pihak Ketiga

Perbarui konfigurasi berikut pada file `.env` Anda untuk menggunakan fitur integrasi:

### A. WhatsApp Gateway (Pilih Salah Satu)

1. **Menggunakan Local Gateway (Node.js)**:
   * Biarkan `WHATSAPP_API_URL` bernilai default `http://localhost:5000/send` dan kosongkan `WHATSAPP_API_TOKEN`.
   * Jalankan server WhatsApp lokal pada terminal baru:
     ```powershell
     node whatsapp-service.js
     ```
   * Scan QR Code yang muncul di terminal menggunakan aplikasi WhatsApp HP Anda.

2. **Menggunakan Fonnte API Gateway**:
   * Ubah `WHATSAPP_API_URL` ke `https://api.fonnte.com/send`.
   * Isi `WHATSAPP_API_TOKEN` dengan token Fonnte Anda.

### B. Firebase Cloud Messaging (FCM)
Isi variabel berikut untuk mengaktifkan notifikasi push pada mobile/web:
```env
FIREBASE_API_KEY=your_key
FIREBASE_AUTH_DOMAIN=your_project.firebaseapp.com
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_STORAGE_BUCKET=your_project.appspot.com
FIREBASE_MESSAGING_SENDER_ID=your_sender_id
FIREBASE_APP_ID=your_app_id
FIREBASE_VAPID_KEY=your_vapid_key
```
*Jangan lupa untuk meletakkan file kredensial Service Account Firebase Anda di `storage/app/firebase/service-account.json`.*

### C. Google OAuth 2.0
Dapatkan kredensial OAuth 2.0 Web Client dari Google Developer Console, lalu pasang:
```env
WEB_CLIENT_ID=your_client_id.apps.googleusercontent.com
WEB_CLIENT_SECRET=your_client_secret
```

### D. Web Push VAPID Keys
Jalankan perintah berikut untuk menggenerasi VAPID keys baru:
```powershell
php artisan webpush:vapid
```

### E. Background Jobs (Queue Worker)
Karena pengiriman email, notifikasi WhatsApp, dan notifikasi FCM dikirim secara asinkron untuk performa optimal, pastikan Anda menjalankan queue worker:
```powershell
php artisan queue:work
```

---

## 🧪 Data Dummy & Akun Demo Real-Time

Sistem dilengkapi dengan seeder skala besar ([DummyDataSeeder.php](file:///c:/laragon/www/Sistem-Presensi-Alumni-Digital-Sekolah/database/seeders/DummyDataSeeder.php) & [PresensiSessionSeeder.php](file:///c:/laragon/www/Sistem-Presensi-Alumni-Digital-Sekolah/database/seeders/PresensiSessionSeeder.php)) yang mensimulasikan lingkungan sekolah nyata:
* **1 Sekolah SMK** (`SMK Negeri 1 Digital Presensi & Alumni`)
* **10 Guru Pengajar** dengan mata pelajaran berbeda (Web Dev, Matematika, B. Inggris, Jaringan, dll.)
* **8 Kelas** (X, XI, XII RPL & TKJ)
* **160 Siswa & 160 Akun Orang Tua** dengan nomor WhatsApp aktif.
* **Riwayat Presensi 14 Hari** (Ribuan record presensi untuk grafik statistik).
* **Sesi Live Hari Ini**: Ada sesi pagi (selesai) dan sesi yang **sedang berlangsung (*Open*) tepat saat ini** lengkap dengan QR Token aktif!
* **60+ Alumni (2021-2025)** dengan profil karir, 10 lowongan kerja, dan 5 agenda event.

### 🔑 Akun Login Siap Pakai (Password semua akun: `password`)

| Role | Email Login | Keterangan / Kegunaan |
| :--- | :--- | :--- |
| **👑 Super Admin** | `superadmin@simpad.app` | Akses penuh seluruh konfigurasi & sistem |
| **🛡️ Admin Sekolah** | `admin@smkn1demo.sch.id` | Kelola master data, jadwal, dan rekap presensi |
| **👨‍🏫 Guru Utama** | `guru@smkn1demo.sch.id` | *Budi Santoso, S.Kom.* (Demo buka kelas, input presensi & check-in/out) |
| **👨‍🎓 Siswa Utama** | `siswa@smkn1demo.sch.id` | Demo scan QR Code, ajukan izin, & lihat riwayat |
| **🎓 Alumni Utama** | `ahmad.fauzi2024@alumni.test` | Demo portal alumni, lowongan kerja, dan event |
| **👨‍👩‍👦 Orang Tua** | `ortu_202601001@sekolah.id` | Demo pemantauan kehadiran anak & notifikasi WA |

---

## 🔐 Role & Hak Akses (Spatie RBAC)

| Permission | Super Admin | Admin Sekolah | Guru | Siswa | Orang Tua | Alumni |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **Kelola Master Sekolah & User** | ✅ | ✅ | ❌ | ❌ | ❌ | role |
| **Kelola Jadwal & Jam Pelajaran** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Buka Sesi & Input Presensi** | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Generate & Scan QR Attendance** | ❌ | ✅ | ✅ (Gen) | ✅ (Scan) | ❌ | ❌ |
| **Ajukan Izin & Sakit** | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| **Verifikasi Izin Siswa** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Lihat Rekap Presensi** | ✅ | ✅ | ✅ (Kelas) | ✅ (Sendiri) | ✅ (Anak) | ❌ |
| **Kelola Data & Verifikasi Alumni** | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ (Sendiri) |
| **Posting Lowongan & Event** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Akses Admin Panel Web** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |

---

## 📡 Daftar Endpoint API Mobile

Semua endpoint API dilindungi oleh middleware Sanctum (`auth:sanctum`) kecuali endpoint login/register/public. Sertakan header `Authorization: Bearer <token>` dan `Accept: application/json`.

### 1. Autentikasi (`/api/v1`)
* `POST /login` - Login pengguna (mengembalikan token & role)
* `POST /auth/login` - Alias login pengguna
* `POST /logout` - Revoke token aktif saat ini
* `GET /me` - Mengambil profil pengguna saat ini
* `POST /forgot-password` - Kirim kode OTP reset password ke email/WhatsApp
* `POST /verify-otp` - Verifikasi kode OTP
* `POST /reset-password` - Mengatur password baru
* `POST /auth/google` - Login pengguna menggunakan Google ID Token

### 2. Kehadiran & Kelas Guru (`/api/v1`)
* `GET /teacher/today` - Mengambil daftar kelas/jadwal ajar hari ini (Urutan: Live/Open, Akan Dimulai, Selesai)
* `POST /teacher/check-in` - Guru check-in harian mandiri
* `POST /teacher/check-out` - Guru check-out harian mandiri
* `GET /teacher-attendance/today` - Status check-in/out guru hari ini
* `GET /teacher/schedules` - Semua jadwal mengajar guru aktif
* `POST /attendance/open` - Membuka sesi presensi (Mendukung *Allow Early Open*)
* `POST /attendance/manual` - Menyimpan atau memperbarui status presensi manual siswa
* `POST /attendance/generate-qr` - Mengenerate token QR Code presensi (berlaku 5 menit)
* `POST /attendance/close` - Menutup sesi kelas
* `GET /attendance/session/{id}` - Detail sesi presensi beserta daftar kehadiran siswa
* `GET /attendance/history` - Riwayat sesi mengajar guru

### 3. Presensi & Izin Siswa (`/api/v1/attendances`)
* `POST /attendances/presensi` - Siswa melakukan presensi mandiri via scan QR Code
* `POST /attendances/izin` - Siswa mengajukan izin/sakit dengan melampirkan berkas bukti
* `POST /attendances/{id}/verify` - Guru/Admin memverifikasi status pengajuan izin siswa
* `GET /student/attendances` - Riwayat kehadiran pribadi siswa
* `GET /student/schedule` - Jadwal pelajaran siswa hari ini

### 4. Portal & Penelusuran Alumni (`/api/v1/alumni`)
* `POST /alumni/register` - Pendaftaran alumni baru (Status awal: Pending)
* `GET /alumni/profile` - Lihat profil & tracer study alumni saat ini
* `PUT /alumni/profile` - Perbarui profil tracer study (Status karir, pendidikan, wirausaha)
* `GET /alumni/jobs` - Daftar lowongan kerja aktif khusus alumni
* `GET /alumni-events` - Daftar event alumni aktif
* `GET /alumni/verification/pending` - (Admin/Super Admin) Daftar alumni menunggu verifikasi
* `POST /alumni/verification/{id}/approve` - (Admin/Super Admin) Menyetujui pendaftaran alumni
* `POST /alumni/verification/{id}/reject` - (Admin/Super Admin) Menolak pendaftaran alumni dengan menyertakan alasan
* `POST /alumni/verification/{id}/reset` - (Admin/Super Admin) Mengembalikan status verifikasi alumni ke pending

### 5. Notifikasi & Laporan (`/api/v1`)
* `POST /device-token` - Mendaftarkan token FCM perangkat user
* `GET /notifications` - Mengambil notifikasi milik user
* `GET /notifications/unread-count` - Jumlah notifikasi yang belum dibaca
* `POST /notifications/mark-all-read` - Tandai semua notifikasi sudah dibaca
* `GET /education-news` - Mengambil berita pendidikan aktif
* `GET /export/attendance` - Ekspor laporan presensi siswa ke file Excel
* `GET /export/alumni` - Ekspor data tracer study alumni ke file Excel

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
│   ├── Resources/             # CRUD Sekolah, Guru, Siswa, Jadwal, Alumni, Laporan
│   └── Widgets/               # RealtimeAttendanceOverview, StatsOverview, AlumniChart
├── Http/
│   ├── Controllers/Api/       # Controller API Mobile (Tanpa Business Logic)
│   ├── Requests/              # Form Request Validation
│   └── Resources/             # API Resource JSON Transformation
├── Jobs/                      # Background Jobs (SendWhatsAppNotification, dll)
├── Models/                    # Eloquent Models (19+ Models dengan Relationship)
├── Policies/                  # Authorization Policies (SchedulePolicy, AlumniPolicy)
└── Services/                  # Business Logic Layer (Clean Service Layer)
    ├── [AlumniService.php](file:///c:/laragon/www/Sistem-Presensi-Alumni-Digital-Sekolah/app/Services/AlumniService.php)                  # Logika registrasi alumni
    ├── [AlumniVerificationService.php](file:///c:/laragon/www/Sistem-Presensi-Alumni-Digital-Sekolah/app/Services/AlumniVerificationService.php)      # Verifikasi pendaftaran alumni
    ├── [AttendanceService.php](file:///c:/laragon/www/Sistem-Presensi-Alumni-Digital-Sekolah/app/Services/AttendanceService.php)              # Logika presensi, validasi sesi, & rekap
    ├── [FirebaseNotificationService.php](file:///c:/laragon/www/Sistem-Presensi-Alumni-Digital-Sekolah/app/Services/FirebaseNotificationService.php)    # Pengiriman notifikasi push via FCM
    └── [WhatsAppService.php](file:///c:/laragon/www/Sistem-Presensi-Alumni-Digital-Sekolah/app/Services/WhatsAppService.php)                  # Gateway Fonnte & Gateway Lokal Node.js
```

---

## 📄 Lisensi

Proyek **Sistem Presensi & Alumni Digital Sekolah** dikembangkan untuk kebutuhan manajemen internal sekolah dan pengujian sistem canggih. Hak Cipta Dilindungi Undang-Undang.

*Terakhir Diperbarui: **18 Juli 2026***
