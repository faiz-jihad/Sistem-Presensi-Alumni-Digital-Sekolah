## 📄 **README.md - Sistem Presensi & Alumni Digital Sekolah**

```markdown
Sistem Presensi & Alumni Digital Sekolah
Sistem Manajemen Presensi dan Alumni berbasis Laravel + Filament (Backend) dan Flutter (Frontend)

📋 Deskripsi
Sistem ini adalah aplikasi manajemen sekolah yang mencakup presensi siswa, manajemen alumni, dan tracer study. Dibangun dengan arsitektur REST API menggunakan Laravel dan dilengkapi dengan admin panel Filament. Aplikasi mobile dikembangkan dengan Flutter untuk guru, orang tua, dan alumni.

Status Pengembangan: 🚧 MVP (Minimum Viable Product) - Fase 1

🎯 Fitur MVP
No	Fitur	Status
1	Login Multi-Role (Admin, Guru, Orang Tua, Alumni)	✅
2	Master Data (Siswa, Guru, Kelas, Tahun Ajaran)	✅
3	Presensi Manual oleh Guru	✅
4	Rekap Presensi Harian & Bulanan	✅
5	Notifikasi WhatsApp untuk Orang Tua	✅
6	Registrasi Alumni	✅
7	Profil Alumni & Tracer Study	✅
8	Statistik Tracer Study	✅
9	Export Excel (Presensi & Alumni)	✅
🏗️ Arsitektur
text
┌─────────────────────────────────────────────────────────────┐
│                        FRONTEND                             │
├─────────────────────────────────────────────────────────────┤
│  Flutter App (Android/iOS/Web)                             │
│  - Aplikasi Guru (Input Presensi)                          │
│  - Aplikasi Orang Tua (Monitoring Anak)                    │
│  - Aplikasi Alumni (Profil & Tracer Study)                │
└────────────────┬────────────────────────────────────────────┘
                 │ REST API + Sanctum Token
┌────────────────▼────────────────────────────────────────────┐
│                        BACKEND                              │
├─────────────────────────────────────────────────────────────┤
│  Laravel 10/11 + API + Filament Admin Panel                │
│  - Authentication (Sanctum)                                │
│  - Role Management (Spatie Permission)                     │
│  - Export Excel/PDF                                        │
│  - WhatsApp Notification Gateway                            │
└────────────────┬────────────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────────────┐
│                        DATABASE                             │
├─────────────────────────────────────────────────────────────┤
│  MySQL (Relational Database)                               │
└─────────────────────────────────────────────────────────────┘
📁 Struktur Database
Core Tables
users - User authentication & roles

schools - Data sekolah

classes - Data kelas

students - Data siswa

teachers - Data guru

Presensi Tables
student_attendances - Log presensi siswa

Alumni Tables
alumni - Data alumni terverifikasi

alumni_profiles - Profil lengkap & tracer study

System Tables
exports - Log export file

🔐 Role & Hak Akses
Role	Hak Akses Utama
Super Admin	Kelola seluruh sekolah, paket, user, konfigurasi sistem
Admin Sekolah	Kelola master data, presensi, alumni, laporan, export
Guru	Input presensi, lihat kelas diampu, rekap kelas
Orang Tua	Lihat kehadiran anak, terima notifikasi
Siswa	Lihat riwayat presensi pribadi
Alumni	Registrasi, update profil, isi tracer study
🛠️ Tech Stack
Backend
Framework: Laravel 10/11

Authentication: Laravel Sanctum

Authorization: Spatie Laravel Permission

Admin Panel: Filament PHP v3

Database: MySQL

Export: Laravel Excel (Maatwebsite)

Notifications: WhatsApp Gateway API

Frontend (Mobile)
Framework: Flutter

State Management: Provider / Riverpod / GetX (sesuai kebutuhan)

HTTP Client: Dio

Local Storage: Shared Preferences

📡 API Endpoints (MVP)
Authentication
Method	Endpoint	Fungsi
POST	/api/login	Login & generate token
POST	/api/logout	Logout & revoke token
GET	/api/me	Ambil data user login
Master Data
Method	Endpoint	Fungsi
GET	/api/classes	Daftar kelas
GET	/api/classes/{id}/students	Daftar siswa per kelas
Presensi
Method	Endpoint	Fungsi
POST	/api/attendance/submit	Simpan presensi siswa
GET	/api/attendance/daily	Rekap presensi harian
GET	/api/attendance/monthly	Rekap presensi bulanan
Alumni
Method	Endpoint	Fungsi
POST	/api/alumni/register	Registrasi alumni
GET	/api/alumni/profile	Ambil profil alumni
PUT	/api/alumni/profile	Update profil alumni
GET	/api/admin/alumni/tracer-study	Statistik tracer study
Export
Method	Endpoint	Fungsi
GET	/api/export/attendance	Export presensi Excel
GET	/api/export/alumni	Export alumni Excel
📋 Checklist Pengembangan
Backend Laravel
Setup Laravel + Sanctum

Setup Spatie Permission

Setup Filament

Migrasi database

CRUD master data

API presensi

API alumni

Export Excel

Frontend Flutter
Halaman login

Routing per role

Dashboard guru

Form presensi

Riwayat presensi

Register alumni

Profil alumni

Tracer study form

Integrasi & Testing
Bearer token

Validasi response API

Loading & error state

WhatsApp gateway

Export file

Testing role

Testing data sekolah

Deploy staging

🚀 Quick Start (Local Development)
Backend (Laravel)
bash
# Clone repository
git clone https://github.com/your-repo/sistem-presensi-alumni.git
cd sistem-presensi-alumni

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate key
php artisan key:generate

# Setup database (sesuaikan di .env)
php artisan migrate --seed

# Create storage link
php artisan storage:link

# Start development server
php artisan serve

# Start filament (optional, di terminal terpisah)
php artisan filament:serve
Frontend (Flutter)
bash
# Masuk ke direktori flutter app
cd flutter-app

# Install dependencies
flutter pub get

# Run aplikasi
flutter run
Testing API dengan Postman
Import collection Postman dari folder docs/postman-collection.json

📁 Folder Structure Backend
text
app/
├── Filament/
│   ├── Resources/
│   │   ├── Schools/
│   │   ├── Students/
│   │   ├── Teachers/
│   │   ├── Classes/
│   │   └── Alumni/
│   └── Widgets/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── AuthController.php
│   │   │   ├── AttendanceController.php
│   │   │   └── AlumniController.php
│   │   └── Admin/
│   └── Middleware/
├── Models/
│   ├── User.php
│   ├── School.php
│   ├── Student.php
│   ├── Teacher.php
│   ├── Class.php
│   ├── StudentAttendance.php
│   └── Alumni.php
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php

database/
├── migrations/
└── seeders/

routes/
├── api.php      # API routes
└── web.php      # Filament routes
📊 Fitur Fase Selanjutnya
Setelah MVP stabil, fitur berikut akan dikembangkan:

QR Code Presensi

Kartu Alumni Digital

Event Alumni

Lowongan Kerja Alumni

Dashboard Grafik Interaktif

Export PDF

Notifikasi Push

Multi-Sekolah Support

🤝 Kontribusi
Untuk kontribusi dalam pengembangan, silakan:

Fork repository

Buat branch fitur (git checkout -b feature/AmazingFeature)

Commit perubahan (git commit -m 'Add some AmazingFeature')

Push ke branch (git push origin feature/AmazingFeature)

Buat Pull Request

📝 Lisensi
Proyek ini untuk keperluan magang internal dan pengembangan sistem sekolah.

👥 Tim Pengembang
Project Lead: [Nama]

Backend Developer: [Nama]

Frontend Developer: [Nama]

UI/UX Designer: [Nama]

📞 Kontak & Support
Email: support@sekolahdigital.com

Documentation: [Link Dokumentasi Lengkap]

Issue Tracker: [Link Project Management]

Dibuat untuk keperluan magang internal - MVP Fase 1

Last Updated: 2026


```

┌──────────────────────────────────────────────────────────┐
│                   MOBILE APP (Flutter)                    │
│              (Siswa & Guru)                               │
└─────────────────────┬────────────────────────────────────┘
                      │ REST API (Sanctum Token)
┌─────────────────────▼────────────────────────────────────┐
│              LARAVEL BACKEND                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────────┐│
│  │Controllers│ │ Services │ │  Models  │ │    Jobs      ││
│  └──────────┘ └──────────┘ └──────────┘ └──────────────┘│
│  ┌──────────────────────────────────────────────────────┐│
│  │              MySQL Database                          ││
│  └──────────────────────────────────────────────────────┘│
└─────────────────────┬────────────────────────────────────┘
                      │
        ┌─────────────┼─────────────┐
        ▼                            ▼
┌──────────────┐            ┌──────────────┐
│  WhatsApp API │            │  Redis Queue  │
│  (Fonnte)     │            │  (Jobs)       │
└──────────────┘            └──────────────┘

```

---

## 🗄️ STRUKTUR DATABASE

### Tabel Utama (19 Tabel)

| No | Tabel | Deskripsi | Records Estimasi |
|---|---|---|---|
| 1 | `schools` | Data sekolah | 1-10 |
| 2 | `users` | Akun pengguna | 1000+ |
| 3 | `teachers` | Data guru | 50-100 |
| 4 | `students` | Data siswa | 500-2000 |
| 5 | `classes` | Data kelas | 20-50 |
| 6 | `academic_years` | Tahun ajaran | 5-10 |
| 7 | `semesters` | Semester | 10-20 |
| 8 | `subjects` | Mata pelajaran | 20-50 |
| 9 | `class_hours` | Jam pelajaran | 10-15 |
| 10 | `schedules` | Jadwal pelajaran | 200-500 |
| 11 | `presensi_sessions` | Sesi presensi | 1000+/tahun |
| 12 | `student_attendances` | Presensi siswa | 100.000+/tahun |
| 13 | `alumni` | Data alumni | 500-2000 |
| 14 | `alumni_profiles` | Profil alumni | 500-2000 |
| 15 | `exports` | Riwayat ekspor | 100-500 |
| 16 | `permissions` | Hak akses | 50-100 |
| 17 | `roles` | Peran | 6 |
| 18 | `sessions` | Session user | - |
| 19 | `personal_access_tokens` | Token API | - |

### Entity Relationship Diagram (ERD)
```

schools ──┬── users ──┬── teachers
          │            │── students ──┬── student_attendances
          │            │── alumni ──┬── alumni_profiles
          │            │── parents  │
          │
          ├── classes ──┬── students
          │              │── schedules ──┬── subjects
          │              │               │── teachers
          │              │               │── class_hours
          │              │               │── presensi_sessions
          │              │
          ├── academic_years ── semesters
          ├── subjects
          ├── class_hours
          └── exports

```

---

## 📁 STRUKTUR FOLDER
```

sistem-presensi-alumni/
│
├── app/
│   ├── Console/
│   │   └── Commands/           # Custom artisan commands
│   ├── Exceptions/             # Custom exceptions
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/            # API controllers (mobile)
│   │   │   └── Web/            # Web controllers (fallback)
│   │   ├── Middleware/         # Custom middleware
│   │   ├── Requests/           # Form validation
│   │   └── Resources/          # API resources (transform data)
│   ├── Interfaces/             # Contracts/interfaces (SOLID)
│   ├── Models/                 # Eloquent models
│   ├── Policies/               # Authorization policies
│   ├── Services/               # Business logic
│   └── Traits/                 # Reusable traits
│
├── database/
│   ├── factories/              # Model factories (dummy data)
│   ├── migrations/             # Database migrations (19 files)
│   └── seeders/                # Database seeders
│
├── routes/
│   ├── api.php                 # API routes
│   ├── web.php                 # Web routes
│   └── console.php             # Console routes
│
├── tests/
│   ├── Feature/                # Feature tests
│   └── Unit/                   # Unit tests
│
├── docs/
│   ├── api-documentation.md    # API documentation
│   └── task-breakdown.xlsx     # Task breakdown (390 tasks)
│
├── .env.example                # Environment template
├── composer.json               # PHP dependencies
├── package.json                # JS dependencies
└── README.md                   # This file

```

---

## 🚀 PANDUAN INSTALASI

### Prasyarat
- PHP 8.2 atau lebih tinggi
- Composer 2.x
- MySQL 8.0 atau MariaDB 10.6+
- Node.js 18+ & NPM
- Redis (opsional, untuk queue)
- Git

### Langkah Instalasi Development

```bash
# 1. Clone repository
git clone https://github.com/your-org/sistem-presensi-alumni.git
cd sistem-presensi-alumni

# 2. Install PHP dependencies
composer install

# 3. Install JavaScript dependencies
npm install && npm run build

# 4. Copy file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database di file .env
# DB_DATABASE=sistem_presensi_alumni_digital_sekolah
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migration dan seeder
php artisan migrate --seed

# 8. Buat symbolic link untuk storage
php artisan storage:link

# 9. Install Sanctum (API Auth)
php artisan install:api

# 10. Buat user admin Filament
php artisan make:filament-user

# 11. Jalankan development server
php artisan serve

# 12. (Opsional) Jalankan queue worker
php artisan queue:work

# 13. (Opsional) Jalankan scheduler
php artisan schedule:work
```

### Akses Aplikasi

- **API Base URL:** `http://localhost:8000/api/v1`
- **Admin Panel:** `http://localhost:8000/admin`
- **API Documentation:** `http://localhost:8000/docs/api`

---

## ⚙️ KONFIGURASI ENVIRONMENT

### `.env` File Template

```env
APP_NAME="Sistem Presensi & Alumni"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_presensi_alumni_digital_sekolah
DB_USERNAME=root
DB_PASSWORD=

# Redis (Cache & Queue)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (Untuk forgot password)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password

# WhatsApp Gateway (Fonnte)
WHATSAPP_API_URL=https://api.fonnte.com/send
WHATSAPP_API_TOKEN=your-token-here

# Sanctum (SPA Auth)
SANCTUM_STATEFUL_DOMAINS=localhost:8000
SESSION_DOMAIN=localhost

# Filament
FILAMENT_FILESYSTEM_DISK=public
```

---

## 📡 DAFTAR ENDPOINT API

### Autentikasi (`/api/v1/auth`)

| Method   | Endpoint             | Deskripsi           | Auth |
| -------- | -------------------- | ------------------- | ---- |
| `POST` | `/login`           | Login user          | ❌   |
| `POST` | `/register`        | Register user baru  | ❌   |
| `POST` | `/forgot-password` | Lupa password       | ❌   |
| `POST` | `/reset-password`  | Reset password      | ❌   |
| `POST` | `/logout`          | Logout user         | ✅   |
| `POST` | `/logout-all`      | Logout semua device | ✅   |
| `GET`  | `/me`              | Profile user        | ✅   |

### Presensi (`/api/v1/attendances`)

| Method   | Endpoint             | Deskripsi                | Role              |
| -------- | -------------------- | ------------------------ | ----------------- |
| `GET`  | `/`                | List presensi            | Admin, Guru       |
| `POST` | `/`                | Input presensi           | Guru              |
| `POST` | `/bulk`            | Input presensi massal    | Guru              |
| `POST` | `/presensi`        | Presensi mandiri (siswa) | Siswa             |
| `POST` | `/izin`            | Ajukan izin/sakit        | Siswa             |
| `POST` | `/{id}/verify`     | Verifikasi izin          | Admin, Wali Kelas |
| `GET`  | `/report/daily`    | Rekap harian             | Admin, Guru       |
| `GET`  | `/report/monthly`  | Rekap bulanan            | Admin, Guru       |
| `GET`  | `/report/semester` | Rekap semester           | Admin             |

### Alumni (`/api/v1/alumni`)

| Method   | Endpoint         | Deskripsi         | Role          |
| -------- | ---------------- | ----------------- | ------------- |
| `GET`  | `/`            | List alumni       | Admin         |
| `POST` | `/`            | Tambah alumni     | Admin         |
| `GET`  | `/{id}`        | Detail alumni     | Admin, Alumni |
| `PUT`  | `/{id}`        | Update alumni     | Admin         |
| `POST` | `/{id}/verify` | Verifikasi alumni | Admin         |
| `GET`  | `/statistics`  | Statistik alumni  | Admin         |

**Dokumentasi API lengkap:** `http://localhost:8000/docs/api`

---

## 🔐 ROLE & PERMISSION

### Matrix Role

| Permission           | Super Admin | Admin Sekolah | Guru | Wali Kelas | Siswa        | Orang Tua | Alumni |
| -------------------- | ----------- | ------------- | ---- | ---------- | ------------ | --------- | ------ |
| Kelola Sekolah       | ✅          | ❌            | ❌   | ❌         | ❌           | ❌        | ❌     |
| Kelola User          | ✅          | ✅            | ❌   | ❌         | ❌           | ❌        | ❌     |
| Kelola Guru          | ✅          | ✅            | ❌   | ❌         | ❌           | ❌        | ❌     |
| Kelola Siswa         | ✅          | ✅            | ❌   | ✅         | ❌           | ❌        | ❌     |
| Input Presensi       | ❌          | ✅            | ✅   | ✅         | ❌           | ❌        | ❌     |
| Presensi Mandiri     | ❌          | ❌            | ❌   | ❌         | ✅           | ❌        | ❌     |
| Lihat Rekap          | ✅          | ✅            | ✅   | ✅         | ✅ (sendiri) | ✅ (anak) | ❌     |
| Ajukan Izin          | ❌          | ❌            | ❌   | ❌         | ✅           | ❌        | ❌     |
| Kelola Alumni        | ✅          | ✅            | ❌   | ❌         | ❌           | ❌        | ❌     |
| Update Profil Alumni | ❌          | ❌            | ❌   | ❌         | ❌           | ❌        | ✅     |
| Dashboard            | ✅          | ✅            | ✅   | ✅         | ❌           | ❌        | ❌     |

---

## 🔄 WORKFLOW PRESENSI

```
1. Admin setup Tahun Ajaran & Semester
2. Admin/Guru setup Jadwal Pelajaran
3. Scheduler generate Sesi Presensi otomatis setiap hari
4. Guru membuka sesi presensi
5a. SISWA: Presensi mandiri via mobile app
5b. GURU: Input manual presensi kelas
6. Sistem deteksi keterlambatan (>15 menit = Terlambat)
7. Siswa bisa ajukan Izin/Sakit
8. Wali Kelas verifikasi izin
9. Jika Alpha → Trigger WhatsApp ke Orang Tua
10. Akumulasi rekap harian/bulanan/semester
11. Export rekap ke Excel/PDF
```

---

## 💻 KONVENSI KODE

### Standar Penulisan

- **Bahasa:** Inggris untuk kode, Indonesia untuk komentar
- **Style:** PSR-12 (Laravel standard)
- **Indentasi:** 4 spasi
- **Naming Convention:**
  - Class: `PascalCase` (UserController)
  - Method: `camelCase` (getUserById)
  - Variable: `camelCase` ($userId)
  - Constant: `UPPER_SNAKE_CASE` (MAX_LOGIN_ATTEMPTS)
  - Database Table: `snake_case` (student_attendances)
  - Route: `kebab-case` (/student-attendances)

### Prinsip SOLID

- **S**ingle Responsibility: 1 class = 1 tugas
- **O**pen/Closed: Buka untuk ekstensi, tutup untuk modifikasi
- **L**iskov Substitution: Child class harus bisa ganti parent
- **I**nterface Segregation: Interface spesifik, tidak gemuk
- **D**ependency Inversion: Depend pada abstraction, bukan concrete

### Struktur Controller

```php
class ExampleController extends BaseController
{
    public function __construct(
        private readonly ExampleService $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            $data = $this->service->getAll();
            return $this->success($data, 'Data berhasil diambil');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
```

---

## 🧪 TESTING

### Menjalankan Test

```bash
# Semua test
php artisan test

# Test spesifik
php artisan test --filter=LoginTest

# Dengan coverage (butuh Xdebug)
php artisan test --coverage-html=coverage/

# Parallel testing (lebih cepat)
php artisan test --parallel
```

### Target Coverage

- **Unit Test:** >80%
- **Feature Test:** >70%
- **Integration Test:** >60%

### Contoh Test

```php
public function test_user_can_login(): void
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'status' => 'active',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'data' => ['token']]);
}
```

---

## 🚢 DEPLOYMENT

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate `APP_KEY`
- [ ] Konfigurasi database production
- [ ] Setup Redis untuk queue
- [ ] Setup Supervisor untuk queue worker
- [ ] Setup Cron untuk scheduler
- [ ] Setup SSL (Let's Encrypt)
- [ ] Optimize Laravel: `php artisan optimize`
- [ ] Backup database rutin

### Deploy ke VPS (Laravel Forge)

```bash
# 1. Push ke GitHub
git push origin main

# 2. Deploy via Laravel Forge (otomatis)
# Atau manual:
cd /var/www/sistem-presensi
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan optimize
```

### Deploy ke Shared Hosting (cPanel)

```bash
# 1. Zip project (tanpa node_modules & vendor)
zip -r project.zip . -x "node_modules/*" "vendor/*" ".git/*"

# 2. Upload ke public_html/
# 3. Extract zip
# 4. Upload vendor & node_modules terpisah
# 5. Update .env
# 6. Setup cron job: php artisan schedule:run
```

---

## 📋 TASK MANAGEMENT

Semua task sudah di-breakdown menjadi **390 atomic tasks** dalam 16 Epics.

### Daftar Epic

| #               | Epic                              | Task          | Status         |
| --------------- | --------------------------------- | ------------- | -------------- |
| 1               | Project Setup                     | 15            | ⬜ Not Started |
| 2               | Authentication                    | 20            | ⬜ Not Started |
| 3               | Authorization (Role & Permission) | 20            | ⬜ Not Started |
| 4               | User Management                   | 25            | ⬜ Not Started |
| 5               | Master Sekolah                    | 15            | ⬜ Not Started |
| 6               | Master Guru                       | 20            | ⬜ Not Started |
| 7               | Master Siswa                      | 30            | ⬜ Not Started |
| 8               | Master Kelas                      | 20            | ⬜ Not Started |
| 9               | Presensi                          | 60            | ⬜ Not Started |
| 10              | WhatsApp Notification             | 15            | ⬜ Not Started |
| 11              | Alumni                            | 45            | ⬜ Not Started |
| 12              | Reporting                         | 20            | ⬜ Not Started |
| 13              | Dashboard                         | 20            | ⬜ Not Started |
| 14              | API                               | 20            | ⬜ Not Started |
| 15              | Testing                           | 30            | ⬜ Not Started |
| 16              | Deployment                        | 15            | ⬜ Not Started |
| **TOTAL** |                                   | **390** |                |

📥 **Download Task Breakdown Excel:** [`docs/task-breakdown.xlsx`](docs/task-breakdown.xlsx)

---

## ❓ FAQ

### 1. Kenapa pakai Laravel Sanctum bukan JWT?

Sanctum lebih simpel, built-in Laravel, dan cukup untuk SPA + mobile app. JWT overkill untuk case ini.

### 2. Kenapa tidak pakai Filament untuk mobile?

Filament hanya untuk admin panel web. Mobile app dibuat terpisah dengan Flutter.

### 3. Bagaimana cara generate sesi presensi otomatis?

Gunakan Laravel Scheduler + Command yang dijalankan setiap hari jam 00:01.

### 4. Apakah support multi-sekolah?

Ya, 1 instalasi bisa untuk banyak sekolah (yayasan).

### 5. Bagaimana cara integrasi WhatsApp?

Pakai Fonnte WhatsApp Gateway. Kirim via HTTP Client Laravel.

### 6. Berapa estimasi waktu pengerjaan?

Dengan 4-6 developer, estimasi **3-4 bulan** (390 task).

---

## 👥 TIM PENGEMBANG

| Nama   | Role               | Tanggung Jawab                |
| ------ | ------------------ | ----------------------------- |
| [Nama] | Project Manager    | Manajemen proyek & komunikasi |
| [Nama] | Senior Backend     | Arsitektur, API, database     |
| [Nama] | Backend Developer  | CRUD, business logic          |
| [Nama] | Frontend Developer | Filament admin panel          |
| [Nama] | Mobile Developer   | Flutter app (Siswa & Guru)    |
| [Nama] | QA Engineer        | Testing & bug report          |

---

## 📞 KONTAK & DUKUNGAN

- **Email:** support@sistem-presensi.id
- **WhatsApp:** +62 8xx-xxxx-xxxx
- **Issue Tracker:** [GitHub Issues](https://github.com/your-org/sistem-presensi-alumni/issues)
- **Dokumentasi API:** `http://localhost:8000/docs/api`

---

## 📄 LISENSI

Proyek ini bersifat **proprietary**. Hak cipta dilindungi undang-undang.

**© 2026 Sistem Presensi & Alumni Digital Sekolah. All Rights Reserved.**

---

## 🙏 UCAPAN TERIMA KASIH

- **Laravel** - Framework PHP terbaik
- **Filament** - Admin panel modern
- **Fonnte** - WhatsApp Gateway Indonesia
- **Spatie** - Package Laravel berkualitas
- **Komunitas Laravel Indonesia** - Inspirasi & dukungan

---

**Dibuat dengan ❤️ untuk memajukan pendidikan Indonesia** 🇮🇩

---

*Dokumentasi terakhir diperbarui: **3 Juli 2026***

```

---

## 📝 **Cara Menggunakan README.md**

1. **Copy** seluruh kode di atas
2. **Paste** ke file `README.md` di root project
3. **Sesuaikan** bagian:
   - URL GitHub
   - Nama tim
   - Kontak
   - Konfigurasi spesifik

---

**README.md ini akan membantu developer memahami:**
- Tujuan proyek
- Cara install & setup
- Struktur kode
- Konvensi yang digunakan
- Workflow presensi
- Role & permission
- Endpoint API
- Cara testing & deployment

Ada yang perlu ditambahkan atau diubah?
```
