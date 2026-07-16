<x-filament-panels::page>
<style>
    .guide-page {
        font-family: 'Inter', sans-serif;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .guide-header-card {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 1.25rem;
        padding: 3rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3);
    }
    .guide-header-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 75%);
        border-radius: 50%;
    }
    .guide-header-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
        letter-spacing: -0.02em;
    }
    .guide-header-subtitle {
        font-size: 1rem;
        opacity: 0.95;
        max-width: 800px;
        line-height: 1.7;
    }
    
    /* Role Indicator Badge */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.5rem;
        border-radius: 9999px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        font-weight: 800;
        font-size: 0.9rem;
        width: fit-content;
    }
    .dark .role-badge {
        background: rgba(59, 130, 246, 0.12);
        border-color: rgba(59, 130, 246, 0.25);
        color: #93c5fd;
    }

    /* Content Cards */
    .guide-card {
        border-radius: 1.25rem;
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .dark .guide-card {
        background: #18181b;
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    }
    .guide-card-header {
        padding: 1.75rem 2.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 1.3rem;
        font-weight: 850;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .guide-card-header {
        border-bottom-color: rgba(255, 255, 255, 0.06);
        color: #f8fafc;
    }
    .guide-card-body {
        padding: 2.25rem;
    }

    /* Detail Section Block */
    .detail-section {
        margin-bottom: 2.5rem;
    }
    .detail-section:last-child {
        margin-bottom: 0;
    }
    .detail-section-title {
        font-size: 1.15rem;
        font-weight: 850;
        color: #1e3a8a;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        border-bottom: 2px solid #eff6ff;
        padding-bottom: 0.625rem;
    }
    .dark .detail-section-title {
        color: #3b82f6;
        border-bottom-color: rgba(59, 130, 246, 0.1);
    }

    /* Steps & Lists styling */
    .guide-step-list {
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
    }
    .guide-step-item {
        display: flex;
        gap: 1.5rem;
    }
    .guide-step-badge {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: #eff6ff;
        color: #1e40af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(30, 64, 175, 0.05);
    }
    .dark .guide-step-badge {
        background: rgba(59, 130, 246, 0.12);
        color: #93c5fd;
    }
    .guide-step-content {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .guide-step-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #1e293b;
    }
    .dark .guide-step-title {
        color: #f1f5f9;
    }
    .guide-step-desc {
        font-size: 0.925rem;
        color: #475569;
        line-height: 1.7;
    }
    .dark .guide-step-desc {
        color: #94a3b8;
    }
    .guide-step-desc code {
        background: #f1f5f9;
        color: #0f172a;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-family: monospace;
        font-size: 0.85em;
    }
    .dark .guide-step-desc code {
        background: #27272a;
        color: #f4f4f5;
    }

    /* Accordion FAQ Styling */
    .faq-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
    }
    .faq-item {
        border: 1px solid #e2e8f0;
        border-radius: 0.875rem;
        overflow: hidden;
        background: #f8fafc;
        transition: all 0.2s ease;
    }
    .dark .faq-item {
        border-color: rgba(255, 255, 255, 0.06);
        background: rgba(30, 41, 59, 0.2);
    }
    .faq-question {
        padding: 1.375rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        font-weight: 800;
        font-size: 1rem;
        color: #1e293b;
        user-select: none;
    }
    .dark .faq-question {
        color: #cbd5e1;
    }
    .faq-answer {
        padding: 0 1.75rem 1.375rem;
        font-size: 0.925rem;
        color: #475569;
        line-height: 1.7;
        border-top: 1px solid transparent;
    }
    .dark .faq-answer {
        color: #94a3b8;
    }
    .faq-icon {
        transition: transform 0.2s ease;
        width: 1.35rem;
        height: 1.35rem;
        color: #64748b;
    }
    .dark .faq-icon {
        color: #94a3b8;
    }
</style>

<div class="guide-page" x-data="{ 
    activeFaq: null
}">

    {{-- Banner Header --}}
    <div class="guide-header-card">
        <div class="guide-header-title">Dokumentasi &amp; Panduan SIMPAD</div>
        <div class="guide-header-subtitle">
            Selamat datang di pusat bantuan interaktif sistem SIMPAD (Sistem Presensi &amp; Alumni Digital Sekolah). Halaman ini berisi petunjuk cara penggunaan fitur utama, pengelolaan data harian, serta solusi kendala yang dirancang khusus untuk peran Anda saat ini.
        </div>
    </div>

    {{-- Role Indicator --}}
    <div>
        <span class="role-badge">
            <x-heroicon-o-user-circle style="width:20px;height:20px;" />
            Peran Aktif Anda: 
            <strong>
                @if($userRole === 'super_admin')
                    PENGELOLA UTAMA SISTEM (SUPER ADMIN)
                @elseif($userRole === 'admin')
                    ADMINISTRATOR SEKOLAH (ADMIN SEKOLAH)
                @elseif($userRole === 'teacher')
                    GURU MATA PELAJARAN &amp; WALI KELAS
                @else
                    {{ strtoupper($userRole) }}
                @endif
            </strong>
        </span>
    </div>

    {{-- ==================== RENDER: SUPER ADMIN GUIDE ==================== --}}
    @if($userRole === 'super_admin')
        <div class="guide-card">
            <div class="guide-card-header">
                <x-heroicon-o-shield-check style="width:26px;height:26px;color:#3b82f6;" />
                <span>Modul Panduan Lengkap: Pengelola Utama (Super Admin)</span>
            </div>
            <div class="guide-card-body">
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-building-office style="width:20px;height:20px;" />
                        <span>1. Pendaftaran Sekolah Baru &amp; Pembatasan Akses Multi-Sekolah</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Langkah Pendaftaran Sekolah Baru</span>
                                <span class="guide-step-desc">Masuk ke menu <strong>Sekolah</strong> di bilah kiri, lalu klik tombol <strong>Tambah</strong>. Isi seluruh kolom wajib secara detail, seperti Nama Resmi Sekolah, Nomor Pokok Sekolah Nasional (NPSN), nama Kepala Sekolah, alamat lengkap, dan nomor kontak. Pendaftaran ini akan menginisialisasi pangkalan data khusus sekolah tersebut agar tidak saling bercampur dengan sekolah lain.</span>
                            </div>
                        </div>
                        <div class="guide-step-item">
                            <div class="guide-step-badge">B</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Mengaktifkan Kalender Belajar</span>
                                <span class="guide-step-desc">Setelah data sekolah tersimpan, buat kalender dasar sekolah tersebut dengan mengaitkan data Tahun Akademik yang sedang berjalan dan Semester aktif agar Admin Sekolah yang ditunjuk dapat mulai menyusun kelas dan jadwal belajar.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-users style="width:20px;height:20px;" />
                        <span>2. Manajemen Akun Pengguna Tingkat Global</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Pembuatan Akun Admin Sekolah Terdedikasi</span>
                                <span class="guide-step-desc">Buka menu <strong>Pengguna</strong>, lalu buat akun baru. Pada dropdown peran (Role), pilih <strong>Admin</strong>, dan pilih sekolah tempat admin tersebut ditugaskan pada kolom pilihan sekolah. Akun ini akan memegang kuasa penuh operasional sekolah tersebut dan tidak bisa mengintip data sekolah lainnya.</span>
                            </div>
                        </div>
                        <div class="guide-step-item">
                            <div class="guide-step-badge">B</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Aktivasi Instan &amp; Pengisian Tanggal Verifikasi</span>
                                <span class="guide-step-desc">Untuk memudahkan pembuatan akun di lapangan, kolom <strong>Diverifikasi Pada</strong> akan secara otomatis terisi tanggal dan jam saat ini. Fitur ini memotong kewajiban pengguna baru melakukan verifikasi email secara eksternal sehingga akun bisa langsung login.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-cog-6-tooth style="width:20px;height:20px;" />
                        <span>3. Pemeliharaan Infrastruktur Server &amp; Pengiriman Pesan</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Pemantauan Antrean Pengiriman Notifikasi</span>
                                <span class="guide-step-desc">Super Admin wajib memantau kestabilan proses antrean pengiriman pesan latar belakang di database server. Hal ini penting untuk memastikan pengiriman notifikasi absen otomatis ke WhatsApp orang tua berjalan tanpa ada pesan yang tersangkut.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- ==================== RENDER: SCHOOL ADMIN GUIDE ==================== --}}
    @if($userRole === 'admin')
        <div class="guide-card">
            <div class="guide-card-header">
                <x-heroicon-o-academic-cap style="width:26px;height:26px;color:#10b981;" />
                <span>Modul Panduan Lengkap: Administrator Sekolah (Admin Sekolah)</span>
            </div>
            <div class="guide-card-body">
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-arrow-up-tray style="width:20px;height:20px;" />
                        <span>1. Alur Memasukkan Data Siswa &amp; Guru via Excel</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Panduan Menyiapkan Template Berkas</span>
                                <span class="guide-step-desc">Buka menu data <strong>Siswa</strong> atau <strong>Guru</strong> di menu utama, lalu klik tombol **Impor**. Klik tautan untuk mengunduh format template file Excel yang sudah disediakan. Isi baris data dengan teliti. Di kolom penamaan Kelas, pastikan nama kelas yang dimasukkan sudah terdaftar sebelumnya di database sekolah Anda (contoh: <code>X IPA 1</code>).</span>
                            </div>
                        </div>
                        <div class="guide-step-item">
                            <div class="guide-step-badge">B</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Mengatasi Kegagalan Simpan (Data Truncated)</span>
                                <span class="guide-step-desc">Apabila proses unggah gagal dengan kesalahan kolom terpotong, periksa isi kolom Excel Anda. Sistem hanya menerima penulisan tingkatan kelas dalam format angka romawi pendek seperti <code>X</code>, <code>XI</code>, atau <code>XII</code>. Jangan menuliskan kata panjang seperti "Kelas Sepuluh" atau "Kelas X".</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-check-badge style="width:20px;height:20px;" />
                        <span>2. Verifikasi Berkas Pendaftaran Akun Alumni Baru</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Memeriksa Pengajuan yang Masuk</span>
                                <span class="guide-step-desc">Alumni yang mengisi formulir registrasi mandiri akan berstatus <em>Menunggu Verifikasi</em> di sistem Anda. Masuk ke halaman menu <strong>Alumni</strong>, lalu periksa berkas profil, tahun kelulusan, dan kesesuaian data mereka dengan buku induk kelulusan sekolah.</span>
                            </div>
                        </div>
                        <div class="guide-step-item">
                            <div class="guide-step-badge">B</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Menyetujui &amp; Mengaktifkan Akun Alumni</span>
                                <span class="guide-step-desc">Klik tombol **Verifikasi** di kolom tindakan pada baris alumni bersangkutan untuk mengaktifkan akun mereka. Alumni akan segera mendapatkan notifikasi sukses, dan mereka bisa masuk ke sistem untuk melihat lowongan pekerjaan serta berinteraksi di forum alumni.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-chat-bubble-left-right style="width:20px;height:20px;" />
                        <span>3. Penyambungan Server WhatsApp Gateway Sekolah</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Memindai QR Code di Handphone Resmi</span>
                                <span class="guide-step-desc">Buka menu **WhatsApp Gateway** pada grup menu Pengaturan Sistem. Apabila tertulis status *Belum Terhubung*, siapkan ponsel resmi sekolah yang dipasang WhatsApp, buka menu **Perangkat Tertaut** (Linked Devices) pada WhatsApp ponsel, lalu arahkan kamera untuk memindai QR Code yang tampil di layar komputer Anda.</span>
                            </div>
                        </div>
                        <div class="guide-step-item">
                            <div class="guide-step-badge">B</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Memastikan Detail Koneksi Aktif</span>
                                <span class="guide-step-desc">Setelah pemindaian sukses, layar akan memuat ulang halaman secara otomatis dan menampilkan widget profil berisi nama dan nomor WhatsApp yang aktif digunakan sebagai nomor resmi pengirim pesan notifikasi absensi.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-document-arrow-down style="width:20px;height:20px;" />
                        <span>4. Pengunduhan Laporan &amp; Rekapitulasi Absensi</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Membuat Laporan dalam Format Excel &amp; PDF</span>
                                <span class="guide-step-desc">Pergi ke menu **Laporan Presensi**, tentukan rentang tanggal laporan awal dan tanggal akhir yang ingin ditarik, pilih kelas target, kemudian klik tombol **Ekspor Excel** atau **Ekspor PDF** untuk mengunduh rekapitulasi data kehadiran siswa.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- ==================== RENDER: TEACHER GUIDE ==================== --}}
    @if($userRole === 'teacher')
        <div class="guide-card">
            <div class="guide-card-header">
                <x-heroicon-o-presentation-chart-bar style="width:26px;height:26px;color:#f59e0b;" />
                <span>Modul Panduan Lengkap: Guru Mata Pelajaran &amp; Wali Kelas</span>
            </div>
            <div class="guide-card-body">
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-qr-code style="width:20px;height:20px;" />
                        <span>1. Pembukaan Absen Mandiri Siswa Melalui QR Code</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Langkah Membuka Sesi Absen Baru</span>
                                <span class="guide-step-desc">Di halaman dashboard utama Anda, klik tombol **Buka Sesi Presensi**. Tentukan kelas dan mata pelajaran yang sedang Anda ampu saat ini. Setelah diaktifkan, layar komputer Anda akan memunculkan gambar QR Code absensi yang terus diperbarui setiap beberapa detik.</span>
                            </div>
                        </div>
                        <div class="guide-step-item">
                            <div class="guide-step-badge">B</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Proses Pemindaian Oleh Siswa</span>
                                <span class="guide-step-desc">Tampilkan QR Code tersebut melalui proyektor kelas atau perlihatkan layar komputer Anda agar para siswa dapat memindainya menggunakan handphone mereka masing-masing untuk mencatat kehadiran mereka secara mandiri.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-pencil-square style="width:20px;height:20px;" />
                        <span>2. Mengisi atau Mengubah Absen Siswa Secara Manual</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Mengoreksi Ketidakhadiran Siswa</span>
                                <span class="guide-step-desc">Jika ada siswa yang tidak memiliki handphone, lupa melakukan pemindaian, atau memerlukan penyesuaian absen, buka menu <strong>Kehadiran Siswa</strong>. Cari nama siswa tersebut pada tanggal hari ini, klik tombol edit, ubah pilihannya (menjadi Hadir, Terlambat, Sakit, Izin, atau Alpha), lalu simpan untuk memperbarui database.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">
                        <x-heroicon-o-envelope-open style="width:20px;height:20px;" />
                        <span>3. Memeriksa dan Menyetujui Surat Izin / Sakit Siswa</span>
                    </div>
                    <div class="guide-step-list">
                        <div class="guide-step-item">
                            <div class="guide-step-badge">A</div>
                            <div class="guide-step-content">
                                <span class="guide-step-title">Meninjau Lampiran Bukti Foto Surat</span>
                                <span class="guide-step-desc">Siswa dapat mengunggah foto surat dokter atau surat izin dari orang tua melalui akun siswa mereka. Sebagai guru pengajar atau wali kelas, buka menu <strong>Persetujuan Izin</strong>, periksa foto surat bukti yang dilampirkan, lalu klik tombol **Setujui** untuk memvalidasi absen siswa menjadi 'Izin' atau 'Sakit'.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- ==================== FAQ SECTION (ROLE FILTERED: 25 FAQS PER ROLE) ==================== --}}
    <div class="guide-card">
        <div class="guide-card-header">
            <x-heroicon-o-question-mark-circle style="width:26px;height:26px;color:#8b5cf6;" />
            <span>Tanya Jawab Penggunaan Sistem (FAQ Khusus Peran Anda - Sangat Deskriptif)</span>
        </div>
        <div class="guide-card-body">
            <div class="faq-wrapper">

                {{-- ==================== FAQ: SUPER ADMIN (25) ==================== --}}
                @if($userRole === 'super_admin')
                    @php
                        $superAdminFaqs = [
                            1 => ["Apa cakupan hak akses tingkat tinggi yang dimiliki oleh Super Admin?", 
                                  "Super Admin memiliki kontrol administratif tertinggi di atas seluruh infrastruktur sistem. Ini meliputi kewenangan mutlak untuk mendaftarkan sekolah baru, mengelola semua akun pengguna di semua tingkat sekolah secara terpusat, mengawasi database sistem secara menyeluruh, serta melakukan konfigurasi teknis yang berlaku global tanpa dibatasi oleh kode sekolah mana pun."],
                            
                            2 => ["Mengapa akun Super Admin bisa mengakses dan melihat seluruh data sekolah di dalam sistem?", 
                                  "SIMPAD dirancang menggunakan arsitektur pemisahan sekolah (multitenancy) demi keamanan. Data setiap sekolah dipisahkan di database. Namun, akun Super Admin bertindak sebagai administrator sistem utama yang berada di luar batas isolasi sekolah tersebut agar dapat memantau dan membantu jika ada sekolah yang mengalami kendala teknis."],
                            
                            3 => ["Bagaimana langkah-langkah detail untuk mendaftarkan sekolah baru di sistem ini?", 
                                  "Masuk ke menu utama 'Sekolah' di bilah kiri, lalu klik tombol 'Tambah Sekolah Baru' di kanan atas. Lengkapi isian data wajib, meliputi Nama Resmi Sekolah, Nomor Pokok Sekolah Nasional (NPSN), nama Kepala Sekolah aktif, alamat lengkap fisik sekolah, alamat email sekolah, serta unggah logo sekolah. Setelah disimpan, sistem akan secara otomatis membuatkan lingkungan khusus untuk sekolah tersebut."],
                            
                            4 => ["Apakah diperbolehkan menghapus sekolah yang sudah memiliki riwayat absensi atau data siswa?", 
                                  "Sangat dilarang untuk menghapus sekolah yang sudah memiliki transaksi data aktif. Menghapusnya akan memicu kegagalan relasi data (Foreign Key constraint) di database, yang berakibat pada rusaknya data riwayat absensi, data guru, data siswa, dan kelas yang terhubung. Langkah terbaik adalah mengubah status sekolah tersebut menjadi 'Tidak Aktif' (Inactive) agar tidak bisa diakses tanpa merusak database."],
                            
                            5 => ["Bagaimana cara membuatkan akun khusus untuk Admin Sekolah?", 
                                  "Buka menu 'Pengguna' di panel administrasi, klik tombol tambah pengguna baru. Isi nama lengkap, alamat email resmi admin tersebut, dan buatkan password sementara. Pada kolom dropdown Peran (Role), pilih opsi 'Admin'. Pada dropdown pilihan Sekolah, pilih nama sekolah tempat admin tersebut ditugaskan, lalu simpan agar ia bisa masuk ke panel admin sekolahnya."],
                            
                            6 => ["Apakah Super Admin bisa mengetes koneksi pengiriman pesan WhatsApp secara langsung?", 
                                  "Ya, Anda bisa mengunjungi menu WhatsApp Gateway di panel sistem. Di sana terdapat tombol khusus bernama 'Kirim Pesan Uji Coba'. Anda cukup memasukkan nomor telepon tujuan dan isi pesan pengetesan untuk menguji apakah program gateway responsif mengirimkan pesan tersebut ke server WhatsApp Web."],
                            
                            7 => ["Bagaimana cara mereset password pengguna lain yang lupa kata sandi mereka?", 
                                  "Buka menu data 'Pengguna', cari nama pengguna yang terkendala menggunakan kolom pencarian, klik tombol 'Edit' di baris datanya. Ketikkan kata sandi baru minimal 8 karakter pada kolom password, lalu tekan tombol simpan. Pengguna tersebut kini bisa masuk kembali menggunakan kata sandi baru yang Anda buatkan."],
                            
                            8 => ["Apa kegunaan utama dari pengaturan Semester di menu global?", 
                                  "Menu Semester digunakan untuk mengaktifkan masa semester yang sedang berjalan saat ini (seperti Semester Ganjil atau Semester Genap). Pilihan semester aktif ini akan berlaku sebagai rujukan kalender akademik di seluruh sekolah terdaftar agar pencatatan absensi merujuk pada semester yang benar."],
                            
                            9 => ["Bagaimana cara mengonfigurasi Tahun Akademik yang aktif di seluruh sekolah?", 
                                  "Masuk ke menu 'Tahun Akademik' di grup Pengaturan Sistem, klik tambah untuk mendaftarkan tahun ajaran baru (contoh: 2026/2027), isi tanggal mulai dan tanggal berakhir ajaran. Setelah data disimpan, atur statusnya menjadi tahun ajaran yang aktif agar seluruh sekolah merujuk pada tahun pelajaran tersebut."],
                            
                            10 => ["Bagaimana cara menonaktifkan atau menangguhkan akun pengguna yang menyalahgunakan sistem?", 
                                   "Buka menu 'Pengguna', cari akun pengguna tersebut, lalu klik edit. Pada kolom dropdown status akun, ubah pilihannya dari 'Aktif' menjadi 'Ditangguhkan' (Suspended) atau 'Tidak Aktif' (Inactive) kemudian tekan simpan. Pengguna tersebut secara otomatis tidak akan bisa masuk ke sistem sejak detik itu."],
                            
                            11 => ["Mengapa pemutusan tautan login Google OAuth pengguna tidak boleh dilakukan sembarangan?", 
                                   "Memutuskan tautan Google OAuth (Single Sign-On) akan menghentikan kemampuan pengguna masuk ke sistem secara cepat menggunakan tombol 'Masuk dengan Google'. Pemutusan tautan ini hanya boleh dilakukan atas permintaan pengguna sendiri karena ganti email, atau jika ada indikasi akun Google milik mereka diretas."],
                            
                            12 => ["Apa peran penting Super Admin dalam pemeliharaan kapasitas memori database?", 
                                   "Super Admin bertanggung jawab menjaga performa server dengan memantau kapasitas penyimpanan database. Ini termasuk melakukan pengosongan berkas sampah sistem, memantau riwayat log yang terlalu besar di folder `storage/logs/`, serta memastikan indeks query database optimal agar pencarian data tetap cepat meskipun data bertambah."],
                            
                            13 => ["Bagaimana cara memantau antrean pengiriman notifikasi berjalan lancar?", 
                                   "Pesan notifikasi absensi dikirim secara asinkron (antrean di background) agar sistem tidak lambat. Anda bisa memantau kelancaran antrean ini pada status Queue di database atau menjalankan perintah daemon antrean di server. Jika antrean berjalan, status pesan akan berubah dari 'Pending' menjadi 'Sent'."],
                            
                            14 => ["Langkah apa yang harus diambil jika log server mencatat kegagalan pengiriman WhatsApp?", 
                                   "Periksa file catatan error di `storage/logs/laravel.log`. Kegagalan biasanya terjadi disebabkan oleh tiga hal: URL API WhatsApp di setelan berubah, server Node.js mati, atau nomor WhatsApp pengirim terputus koneksinya (ter-logout). Pastikan koneksi internet server stabil dan periksa status gateway Anda."],
                            
                            15 => ["Bagaimana cara menghidupkan kembali service pengirim WhatsApp (Node.js) di server?", 
                                   "Masuk ke terminal server Anda melalui SSH atau Terminal cPanel. Periksa proses program Node.js menggunakan PM2 dengan perintah `pm2 status`. Jika program dalam kondisi mati (stopped), jalankan kembali menggunakan perintah `pm2 restart whatsapp-gateway` atau ketik manual `node whatsapp-service.js`."],
                            
                            16 => ["Dapatkah Super Admin mengubah status kehadiran siswa di salah satu sekolah secara langsung?", 
                                   "Secara teknis Anda bisa melakukannya karena memiliki hak akses global. Namun, sangat tidak disarankan demi menjaga keabsahan data. Perubahan kehadiran siswa sebaiknya diserahkan kepada Guru yang mengajar atau Admin Sekolah setempat yang memiliki wewenang langsung atas kelas tersebut."],
                            
                            17 => ["Bagaimana langkah membersihkan cache atau berkas sampah sistem (clear cache) di cPanel?", 
                                   "Jika Anda melakukan perubahan setelan penting namun tidak terlihat hasilnya, bersihkan berkas sampah sistem dengan cara membuka file pembantu di browser Anda di alamat: `domain-web-anda.com/delete-cache.php`. Script ini akan otomatis memanggil fungsi pembersihan cache konfigurasi Laravel."],
                            
                            18 => ["Apa yang dimaksud dengan Kunci VAPID Web Push di file konfigurasi server?", 
                                   "Kunci VAPID (VAPID Keys) adalah sepasang kunci enkripsi (kunci publik dan privat) yang dipasang di berkas setelan `.env` server. Kunci ini berguna untuk mengamankan pengiriman notifikasi browser langsung ke laptop atau HP pengguna tanpa perlu berlangganan pihak ketiga yang berbayar."],
                            
                            19 => ["Bagaimana mengatasi masalah program Google Chrome yang mendadak menutup sendiri di server Windows?", 
                                   "Bila menjalankan WhatsApp Gateway di server Windows, pastikan setelan peluncur Puppeteer menggunakan folder data profil yang terpisah dengan menyertakan perintah `--user-data-dir=./.chrome-data`. Cara ini mencegah program Chrome sistem bertubrukan dengan program Chrome pribadi Anda saat dibuka."],
                            
                            20 => ["Mengapa grafik statistik di halaman utama Super Admin menunjukkan angka yang sangat besar?", 
                                   "Statistik pada dashboard utama Super Admin menampilkan data gabungan (agregat) dari seluruh sekolah yang terdaftar di sistem SIMPAD. Berbeda dengan halaman dashboard Admin Sekolah yang hanya menampilkan angka statistik dari satu sekolah tertentu saja."],
                            
                            21 => ["Apakah ada batas maksimal pendaftaran sekolah di dalam sistem SIMPAD?", 
                                   "Tidak ada batasan bawaan di dalam software. Jumlah sekolah yang dapat ditampung sepenuhnya bergantung pada kekuatan kapasitas spesifikasi server hosting Anda, seperti ukuran RAM, kecepatan prosesor (CPU), dan sisa ruang penyimpanan database di harddisk server."],
                            
                            22 => ["Bagaimana cara mencadangkan (backup) database sistem secara aman?", 
                                   "Masuk ke menu cPanel hosting Anda, buka fitur phpMyAdmin, pilih nama database SIMPAD Anda, klik tab 'Ekspor', pilih metode ekspor cepat dengan format SQL, lalu klik tombol 'Ekspor'. Simpan file unduhan SQL tersebut di tempat yang aman sebagai cadangan rutin."],
                            
                            23 => ["Mengapa akun utama Super Admin yang dibuat pertama kali tidak boleh dihapus?", 
                                   "Karena akun tersebut memegang kendali sistem utama. Menghapusnya tanpa membuat akun Super Admin pengganti terlebih dahulu akan menyebabkan sistem terkunci secara permanen, dan Anda tidak akan bisa melakukan pengelolaan sistem lagi."],
                            
                            24 => ["Bagaimana cara mengubah nama aplikasi yang tampil di pojok atas layar?", 
                                   "Anda dapat mengubah nama aplikasi dengan cara masuk ke berkas konfigurasi server `.env` di cPanel, cari baris setelan bertuliskan `APP_NAME=\"Nama Aplikasi Baru\"`, ganti teks di dalam tanda kutip tersebut dengan nama sekolah atau instansi Anda, lalu simpan."],
                            
                            25 => ["Apa solusi jika notifikasi sistem diblokir oleh browser Super Admin?", 
                                   "Klik gambar ikon gembok kecil di sebelah kiri bilah penulisan alamat website (URL) di browser Anda. Pada opsi pengaturan notifikasi (Notifications), ubah setelannya dari 'Blokir' (Block) menjadi 'Izinkan' (Allow), kemudian muat ulang halaman browser Anda."]
                        ];
                    @endphp

                    @foreach($superAdminFaqs as $id => $faq)
                        <div class="faq-item">
                            <div class="faq-question" @click="activeFaq === {{ $id }} ? activeFaq = null : activeFaq = {{ $id }}">
                                <span>{{ $id }}. {{ $faq[0] }}</span>
                                <x-heroicon-m-chevron-down class="faq-icon" ::style="activeFaq === {{ $id }} ? 'transform: rotate(180deg)' : ''" />
                            </div>
                            <div class="faq-answer" x-show="activeFaq === {{ $id }}" x-collapse x-cloak>
                                {{ $faq[1] }}
                            </div>
                        </div>
                    @endforeach

                {{-- ==================== FAQ: SCHOOL ADMIN (25 - NO JAM PULANG) ==================== --}}
                @elseif($userRole === 'admin')
                    @php
                        $schoolAdminFaqs = [
                            1 => ["Apa saja batasan hak akses operasional bagi Admin Sekolah?", 
                                  "Admin Sekolah memegang kekuasaan penuh untuk mengelola data operasional di sekolah tempat ia ditugaskan. Batasan keamanannya adalah Admin Sekolah tidak diizinkan untuk melihat, mengubah, menambah, atau menghapus data apa pun dari sekolah lain yang terdaftar di dalam sistem SIMPAD."],
                            
                            2 => ["Bagaimana langkah mendetail memasukkan banyak data siswa sekaligus menggunakan file Excel?", 
                                  "Masuk ke menu 'Siswa' di bilah kiri, klik tombol 'Impor Siswa' di kanan atas. Klik tombol unduh format template untuk mengunduh file Excel contoh. Buka file Excel tersebut, isi kolom-kolom data siswa (Nama Lengkap, NISN, nomor WhatsApp Orang Tua). Pastikan tidak ada kolom wajib yang terlewat. Setelah selesai, simpan file tersebut lalu unggah kembali di halaman web."],
                            
                            3 => ["Mengapa muncul pesan kesalahan Data truncated saat mengunggah daftar siswa?", 
                                  "Kesalahan ini terjadi karena penulisan format kolom di Excel Anda tidak sesuai dengan aturan database. Penyebab paling sering adalah pada kolom tingkat kelas (grade) dituliskan kata panjang seperti 'Kelas Sepuluh' atau 'Kelas X'. Sistem hanya mendeteksi format huruf romawi pendek seperti 'X', 'XI', atau 'XII'. Perbaiki kolom tersebut dan unggah ulang."],
                            
                            4 => ["Bagaimana cara mengimpor daftar data Guru baru menggunakan file Excel?", 
                                  "Masuk ke menu 'Guru' di panel utama, klik tombol 'Impor Guru'. Unduh file template Excel yang sudah disediakan sistem, masukkan data NIP, Nama Lengkap, dan email aktif para guru. Setelah data terisi rapi, simpan file dan unggah kembali di halaman tersebut untuk mendaftarkan semua guru sekaligus."],
                            
                            5 => ["Bagaimana cara menetapkan seorang guru sebagai Wali Kelas di kelas tertentu?", 
                                  "Pergi ke menu data 'Kelas', cari nama kelas yang ingin diatur (misal: X IPA 1), klik tombol 'Edit' di baris data kelas tersebut. Pada pilihan kolom dropdown bertuliskan 'Wali Kelas', pilih nama guru yang ditugaskan sebagai wali kelasnya, kemudian klik simpan."],
                            
                            6 => ["Bagaimana langkah-langkah membuat kelas baru di tahun pelajaran aktif?", 
                                  "Buka menu 'Kelas', klik tombol 'Tambah Kelas'. Tuliskan nama kelas (contoh: X IPA 1), pilih tingkat kelas (romawi X), tentukan rumpun jurusan (contoh: IPA), tentukan wali kelas pengampunya, kemudian klik simpan agar kelas tersebut terdaftar di sekolah Anda."],
                            
                            7 => ["Apa arti sebenarnya dari status alumni Menunggu Verifikasi?", 
                                  "Status tersebut menunjukkan bahwa alumni mendaftar akun secara mandiri lewat formulir pendaftaran umum di website. Akun tersebut belum aktif karena datanya perlu diperiksa dahulu oleh Admin Sekolah untuk memastikan bahwa pendaftar tersebut benar-benar alumni lulusan sekolah Anda."],
                            
                            8 => ["Bagaimana langkah melakukan verifikasi pendaftaran alumni baru?", 
                                  "Buka menu 'Alumni', cari nama alumni yang berstatus 'Menunggu Verifikasi'. Periksa data profil mereka seperti NISN, tahun kelulusan, dan foto bukti jika diunggah. Klik tombol tindakan 'Verifikasi' atau setujui pada baris data alumni tersebut untuk mengubah statusnya menjadi terverifikasi."],
                            
                            9 => ["Apa yang terjadi setelah pendaftaran akun alumni disetujui oleh Admin Sekolah?", 
                                  "Setelah Anda klik verifikasi, status akun alumni tersebut berubah menjadi aktif. Alumni akan otomatis menerima pemberitahuan via email/WhatsApp, dan mereka kini bisa masuk ke sistem untuk melihat lowongan pekerjaan, berinteraksi di forum alumni, serta memperbarui data pekerjaan mereka secara mandiri."],
                            
                            10 => ["Bagaimana cara menghubungkan WhatsApp sekolah agar bisa mengirimkan pesan otomatis?", 
                                   "Masuk ke menu 'WhatsApp Gateway' di bawah grup Pengaturan Sistem. Jika status tertulis offline, mintalah staf pemegang HP resmi sekolah untuk membuka aplikasi WhatsApp, pilih menu 'Perangkat Tertaut' (Linked Devices), lalu arahkan kamera ponsel ke QR Code yang tampil di layar komputer Anda untuk menghubungkannya."],
                            
                            11 => ["Apakah saya harus melakukan pemindaian (scan) QR Code ulang apabila handphone resmi sekolah mati?", 
                                   "Tidak perlu. Selama koneksi WhatsApp di handphone sekolah Anda tidak secara manual diputuskan (di-logout) dari daftar perangkat tertaut, sistem pengirim pesan akan otomatis tersambung kembali saat handphone sekolah dinyalakan kembali."],
                            
                            12 => ["Bagaimana cara mengetahui jika ada pesan notifikasi WhatsApp absensi yang gagal dikirim?", 
                                   "Anda dapat masuk ke halaman dashboard WhatsApp Gateway di admin panel. Jika server pengirim mati atau nomor terputus, akan muncul peringatan warna merah beserta keterangan errornya di layar status utama."],
                            
                            13 => ["Bagaimana cara menyusun jadwal pelajaran mingguan yang baru untuk suatu kelas?", 
                                   "Pergi ke menu 'Jadwal Pelajaran', klik tambah jadwal baru. Tentukan kelas target, hari pelajaran (misal: Senin), mata pelajaran (misal: Matematika), nama guru pengampu, serta isi jam mulai pelajaran berlangsung. Setelah itu tekan tombol simpan."],
                            
                            14 => ["Bagaimana cara mengatur jam pelajaran sekolah di sistem?", 
                                   "Anda cukup mendaftarkan dan menyesuaikan jam pelajaran aktif di menu Jadwal Pelajaran untuk masing-masing kelas. Waktu mulai yang tertera pada jadwal tersebut akan menjadi rujukan bagi guru untuk membuka sesi absen masuk saat jam pelajaran dimulai."],
                            
                            15 => ["Apakah siswa diperbolehkan melakukan absensi saat jam istirahat sekolah?", 
                                   "Sistem secara otomatis akan mengunci sementara sesi pemindaian absensi selama masa jam istirahat pelajaran berlangsung. Hal ini untuk menghindari penyalahgunaan absen oleh siswa saat guru tidak berada di dalam kelas."],
                            
                            16 => ["Bagaimana cara melihat laporan kehadiran harian seluruh siswa hari ini?", 
                                   "Buka menu 'Laporan Presensi', tentukan filter tanggal hari ini pada kolom pencarian, pilih nama kelas yang ingin ditinjau kehadirannya, kemudian klik tombol filter untuk menampilkan rekap persentase kehadiran siswa hari ini di layar."],
                            
                            17 => ["Bagaimana cara menarik rekap laporan kehadiran bulanan?", 
                                   "Di menu 'Laporan Presensi', atur filter rentang tanggal mulai dari tanggal 1 di awal bulan hingga tanggal terakhir di akhir bulan berjalan. Tentukan kelas yang ingin direkap, lalu klik tampilkan untuk memuat seluruh ringkasan absensi bulanan."],
                            
                            18 => ["Apakah berkas laporan absensi siswa bisa diunduh ke komputer dalam format Excel atau PDF?", 
                                   "Sangat bisa. Setelah Anda memfilter data laporan kehadiran di halaman Laporan Presensi, sistem akan memunculkan tombol 'Ekspor Excel' dan 'Ekspor PDF' di atas tabel data. Klik tombol tersebut untuk mengunduh berkas laporan secara instan."],
                            
                            19 => ["Bagaimana cara mengganti nomor WhatsApp orang tua siswa jika ada perubahan?", 
                                   "Buka menu 'Siswa', cari nama siswa bersangkutan, klik edit. Pada kolom data orang tua, temukan kolom nomor telepon, hapus nomor lama, masukkan nomor WhatsApp baru orang tua siswa tersebut dengan format nomor yang benar, lalu simpan."],
                            
                            20 => ["Bagaimana penanganan di sistem jika ada siswa yang pindah atau keluar sekolah?", 
                                   "Cari nama siswa tersebut di menu 'Siswa', klik tombol edit data, lalu ubah status akunnya menjadi 'Tidak Aktif' (Inactive) atau hapus keanggotaan kelasnya. Ini dilakukan agar nama siswa tersebut tidak lagi muncul di dalam daftar absen harian guru."],
                            
                            21 => ["Mengapa menu pencarian filter data di halaman kelas sekarang berbentuk modal pop-up?", 
                                   "Perubahan ini dilakukan untuk menyederhanakan tampilan layar. Filter pencarian yang diletakkan di dalam modal pop-up tidak akan menutupi daftar tabel data di bawahnya dan sangat memudahkan navigasi bagi pengguna laptop berlayar kecil."],
                            
                            22 => ["Apakah Admin Sekolah diperbolehkan mengubah kehadiran siswa jika guru absen mengajar?", 
                                   "Boleh. Sebagai Admin Sekolah, Anda memegang izin penuh untuk melakukan koreksi data kehadiran siswa. Anda bisa masuk ke menu 'Kehadiran Siswa', cari nama siswa, lalu ubah status absennya secara manual kapan saja diperlukan."],
                            
                            23 => ["Bagaimana cara menambahkan mata pelajaran baru di dalam sekolah saya?", 
                                   "Buka menu 'Mata Pelajaran', klik tombol tambah mata pelajaran baru. Isi kolom kode mata pelajaran (contoh: MTK-10) dan nama lengkap mata pelajaran (contoh: Matematika Wajib Kelas X), lalu klik simpan untuk mendaftarkannya."],
                            
                            24 => ["Bagaimana cara membagikan lowongan pekerjaan agar bisa dibaca oleh alumni?", 
                                   "Masuk ke menu 'Lowongan Pekerjaan', klik tambah lowongan baru. Masukkan informasi detail pekerjaan meliputi nama perusahaan pengirim lowongan, posisi jabatan, persyaratan lamaran, gaji (opsional), dan kontak narahubung, lalu klik simpan."],
                            
                            25 => ["Apakah aman menghapus data tahun akademik yang sudah lewat dari sistem?", 
                                   "Sangat berbahaya dan tidak dianjurkan. Menghapus tahun akademik yang sudah lewat dari sistem akan menghapus seluruh data siswa, kelas, dan riwayat presensi yang terkait pada tahun tersebut secara permanen tanpa bisa dikembalikan."]
                        ];
                    @endphp

                    @foreach($schoolAdminFaqs as $id => $faq)
                        <div class="faq-item">
                            <div class="faq-question" @click="activeFaq === {{ $id }} ? activeFaq = null : activeFaq = {{ $id }}">
                                <span>{{ $id }}. {{ $faq[0] }}</span>
                                <x-heroicon-m-chevron-down class="faq-icon" ::style="activeFaq === {{ $id }} ? 'transform: rotate(180deg)' : ''" />
                            </div>
                            <div class="faq-answer" x-show="activeFaq === {{ $id }}" x-collapse x-cloak>
                                {{ $faq[1] }}
                            </div>
                        </div>
                    @endforeach

                {{-- ==================== FAQ: TEACHER (25 - NO JAM PULANG) ==================== --}}
                @elseif($userRole === 'teacher')
                    @php
                        $teacherFaqs = [
                            1 => ["Apa saja batasan tugas utama Guru di dalam aplikasi SIMPAD?", 
                                  "Guru memiliki wewenang untuk melihat jadwal mengajar pribadi, membuka sesi pemindaian absen kelas melalui QR Code, mengoreksi data absensi siswa secara manual apabila ada kesalahan, serta meninjau dan menyetujui berkas surat izin/sakit yang diajukan oleh siswa."],
                            
                            2 => ["Bagaimana cara masuk ke dalam akun Guru di website?", 
                                  "Gunakan alamat email resmi Anda dan kata sandi yang telah didaftarkan oleh Admin Sekolah Anda. Setelah login berhasil, halaman dashboard utama Anda akan langsung menampilkan modul khusus guru secara otomatis."],
                            
                            3 => ["Bagaimana langkah mendetail untuk membuka sesi absen kelas saat pelajaran dimulai?", 
                                  "Pada halaman dashboard utama Anda, klik tombol 'Buka Sesi Presensi'. Pilih nama kelas dan mata pelajaran yang sedang Anda ajar saat itu. Klik tombol aktifkan, dan layar komputer Anda akan memunculkan gambar QR Code yang siap dipindai oleh siswa."],
                            
                            4 => ["Berapa lama sesi presensi QR Code siswa akan tetap aktif?", 
                                  "Sesi presensi akan tetap aktif selama jam mata pelajaran Anda berlangsung sesuai dengan jadwal. Namun, Anda juga dapat menutup sesi absensi tersebut secara manual kapan saja dengan mengeklik tombol 'Tutup Sesi' di layar."],
                            
                            5 => ["Apa solusi jika ada siswa yang handphone-nya mati atau tidak membawa HP untuk absen?", 
                                  "Siswa tersebut tidak perlu khawatir. Anda sebagai guru pengajar dapat mengabsenkan siswa tersebut secara manual. Caranya, cari nama siswa tersebut di daftar absensi kelas hari ini lalu ubah statusnya secara langsung."],
                            
                            6 => ["Bagaimana langkah-langkah mengabsen siswa secara manual?", 
                                  "Masuk ke menu 'Kehadiran Siswa' di panel kiri, cari nama siswa bersangkutan pada tanggal hari ini, klik tombol edit. Pada kolom status kehadiran, ubah pilihannya (Hadir, Sakit, Izin, Terlambat, atau Alpha) lalu klik simpan."],
                            
                            7 => ["Apa perbedaan dari masing-masing pilihan status absen siswa di kelas?", 
                                  "Status 'Hadir' berarti siswa masuk tepat waktu. 'Terlambat' berarti siswa hadir namun setelah batas toleransi jam masuk. 'Sakit' digunakan jika ada surat dokter. 'Izin' jika ada surat dari orang tua, dan 'Alpha' berarti siswa bolos tanpa kabar."],
                            
                            8 => ["Bagaimana cara mencatat siswa yang datang terlambat agar tetap terekam?", 
                                  "Anda cukup mengubah pilihan status kehadiran siswa tersebut menjadi 'Terlambat'. Sistem akan tetap menghitung siswa tersebut masuk sekolah, namun memiliki tanda keterlambatan yang akan terekam di rekap laporan bulanan."],
                            
                            9 => ["Di mana saya bisa melihat riwayat kehadiran siswa pada pertemuan minggu lalu?", 
                                  "Buka menu 'Kehadiran Siswa', gunakan filter tanggal untuk memilih tanggal pertemuan kelas minggu lalu. Layar akan menampilkan daftar lengkap siswa beserta status kehadiran yang Anda simpan pada hari itu."],
                            
                            10 => ["Apakah orang tua siswa akan tahu jika anaknya tidak masuk kelas Anda?", 
                                   "Ya, sistem secara otomatis akan mengirim pesan WhatsApp notifikasi ke nomor HP orang tua/wali siswa sesaat setelah Anda mengonfirmasi kehadiran siswa tersebut dengan status 'Alpha' (Tidak Hadir Tanpa Keterangan)."],
                            
                            11 => ["Bagaimana cara siswa mengirim surat izin sakit agar bisa saya periksa?", 
                                   "Siswa harus masuk ke akun siswa SIMPAD mereka masing-masing, memilih menu pengajuan izin, mengisi tanggal izin, menuliskan alasan, serta wajib mengunggah foto bukti surat dokter atau surat izin dari orang tua."],
                            
                            12 => ["Bagaimana cara menyetujui surat izin atau sakit siswa perwalian saya?", 
                                   "Buka menu 'Persetujuan Izin' di panel kiri, pilih surat pengajuan siswa yang masuk. Periksa alasan dan klik foto surat bukti untuk melihat keasliannya. Jika berkas sudah sesuai, klik tombol hijau 'Setujui'."],
                            
                            13 => ["Apakah saya diperbolehkan menolak pengajuan surat izin sakit siswa?", 
                                   "Tentu saja. Apabila foto surat buram, surat tidak ditandatangani orang tua, atau tidak sah, Anda bisa mengeklik tombol merah 'Tolak' dan menuliskan alasan penolakannya agar siswa bisa mengunggah berkas yang benar."],
                            
                            14 => ["Di mana saya bisa melihat jadwal mengajar saya sendiri dalam seminggu?", 
                                   "Jadwal mengajar Anda secara otomatis ditampilkan di halaman utama dashboard Anda saat pertama kali masuk. Jadwal ini merangkum hari, jam masuk mata pelajaran, kelas, dan mata pelajaran yang Anda ampu."],
                            
                            15 => ["Bagaimana cara memantau rekap absensi bulanan untuk siswa di kelas perwalian saya?", 
                                   "Jika Anda ditugaskan sebagai Wali Kelas, Anda memiliki tab menu khusus bernama 'Kelas Perwalian'. Di menu tersebut, Anda bisa melihat tabel persentase kehadiran bulanan siswa kelas Anda secara menyeluruh."],
                            
                            16 => ["Bolehkah saya mengedit data absen kelas hari kemarin apabila ada salah klik?", 
                                   "Boleh, selama batas waktu penguncian absensi mingguan dari sekolah belum berakhir. Anda bisa kembali ke tanggal kemarin di menu 'Kehadiran Siswa', cari nama siswa, lalu ubah statusnya dan simpan kembali."],
                            
                            17 => ["Mengapa saya tidak diizinkan untuk melihat atau mengedit data absensi kelas milik guru lain?", 
                                   "Ini adalah batasan keamanan demi menjaga keaslian data absensi. Setiap guru hanya diberikan izin penuh untuk mengelola kelas dan mata pelajaran yang terdaftar atas nama dirinya saja di jadwal mengajar resmi."],
                            
                            18 => ["Bagaimana cara mengetes apakah sistem WhatsApp pengirim notifikasi sedang aktif?", 
                                   "Jika diizinkan oleh pihak sekolah, Anda bisa mengunjungi halaman WhatsApp Gateway lalu klik tombol 'Kirim Pesan Uji Coba'. Masukkan nomor tujuan dan kirim pesan untuk memeriksa apakah sistem merespons dengan cepat."],
                            
                            19 => ["Apa yang harus dilakukan apabila sesi absensi kelas tidak sengaja tertutup saat mengajar?", 
                                   "Anda tidak perlu khawatir. Cukup klik tombol 'Buka Sesi Presensi' kembali di halaman utama untuk membuat sesi absen yang baru pada kelas tersebut agar siswa yang belum sempat absen bisa melanjutkan scan QR."],
                            
                            20 => ["Bagaimana cara melihat daftar nama mantan siswa perwalian saya yang sudah lulus (Alumni)?", 
                                   "Masuk ke menu 'Alumni' di panel navigasi kiri, gunakan filter pencarian kelas dan tahun kelulusan untuk melacak riwayat kelulusan dan status pekerjaan para alumni yang dulu pernah Anda bimbing."],
                            
                            21 => ["Mengapa notifikasi suara browser tidak muncul saat ada siswa mengajukan izin baru?", 
                                   "Pastikan Anda telah mengaktifkan izin notifikasi di browser Anda. Klik ikon gembok kecil di sebelah kiri penulisan alamat website (URL) di atas browser, lalu pastikan setelan Notifikasi diubah menjadi 'Izinkan' (Allow)."],
                            
                            22 => ["Mengapa filter tanggal pencarian absen sekarang ditata menyamping (horizontal)?", 
                                   "Filter tanggal penarikan absensi sengaja ditata berdampingan secara menyamping agar tampilan form pencarian menjadi sangat ringkas, padat, dan tidak menghabiskan ruang vertikal layar monitor Anda."],
                            
                            23 => ["Apakah Guru bisa mengunduh rekap absensi kelas ke komputer dalam format file Excel?", 
                                   "Ya, Anda bisa mengunduh rekap kehadiran siswa khusus untuk kelas yang Anda ajar dalam format Excel dengan cara memfilter laporan presensi kelas Anda lalu mengeklik tombol ekspor Excel."],
                            
                            24 => ["Kepada siapa saya harus melaporkan kendala apabila website mengalami error?", 
                                   "Silakan hubungi Admin Sekolah Anda yang memegang kendali teknis operasional sekolah. Admin Sekolah yang nantinya dapat berkoordinasi dengan Super Admin jika ada kerusakan server database."],
                            
                            25 => ["Apakah Guru diperbolehkan mendaftarkan siswa baru atau memindahkan kelas siswa?", 
                                   "Tidak boleh. Penambahan siswa baru, pengimporan data awal, pemindahan kelas siswa, serta penunjukan wali kelas sepenuhnya merupakan tugas administratif yang hanya bisa dilakukan oleh Admin Sekolah."]
                        ];
                    @endphp

                    @foreach($teacherFaqs as $id => $faq)
                        <div class="faq-item">
                            <div class="faq-question" @click="activeFaq === {{ $id }} ? activeFaq = null : activeFaq = {{ $id }}">
                                <span>{{ $id }}. {{ $faq[0] }}</span>
                                <x-heroicon-m-chevron-down class="faq-icon" ::style="activeFaq === {{ $id }} ? 'transform: rotate(180deg)' : ''" />
                            </div>
                            <div class="faq-answer" x-show="activeFaq === {{ $id }}" x-collapse x-cloak>
                                {{ $faq[1] }}
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>

</div>

<x-filament-actions::modals />
</x-filament-panels::page>
