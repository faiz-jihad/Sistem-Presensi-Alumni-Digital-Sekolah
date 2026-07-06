<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPAD - Sistem Informasi Presensi & Alumni Digital</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bbdaf6',
                            300: '#93c2ed',
                            400: '#6ea4e2',
                            500: '#3b82f6',
                            600: '#1d63d8',
                            700: '#174fb0',
                            800: '#184490',
                            900: '#183a74',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.08), transparent 40%),
                        radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.05), transparent 40%),
                        #fafafa;
            min-height: 100vh;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .pulse-soft {
            animation: pulse-key 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-key {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .7; transform: scale(0.96); }
        }
        
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="text-slate-800 antialiased flex flex-col min-h-screen">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 w-full bg-white/70 backdrop-blur-md border-b border-slate-100 px-6 py-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-brand-600 to-emerald-500 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-brand-500/20">
                    S
                </div>
                <div>
                    <h1 class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-brand-600 to-emerald-600 bg-clip-text text-transparent">SIMPAD</h1>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Presensi Digital</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Notification Bell -->
                <div class="relative hidden" id="notification-bell-container">
                    <button id="notification-bell-btn" class="p-2.5 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors border border-slate-100 flex items-center justify-center relative" aria-label="Notifikasi">
                        <svg class="w-4 h-4 text-slate-650" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <!-- Badges -->
                        <span id="notification-badge" class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-rose-500 animate-ping hidden"></span>
                        <span id="notification-badge-static" class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-rose-500 hidden"></span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div id="notification-dropdown" class="absolute right-0 mt-3 w-80 bg-white border border-slate-200 shadow-xl rounded-2xl p-4 hidden z-50">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
                            <h4 class="font-extrabold text-xs text-slate-700">Pemberitahuan</h4>
                            <button onclick="clearNotifications()" class="text-[10px] text-brand-650 hover:underline font-bold">Hapus</button>
                        </div>
                        <div id="notification-list" class="space-y-2 max-h-60 overflow-y-auto text-xs pr-1">
                            <div class="text-slate-400 text-center py-4">Belum ada notifikasi baru.</div>
                        </div>
                    </div>
                </div>

                <span id="user-info" class="hidden text-sm font-medium text-slate-600 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100"></span>
                <button id="logout-btn" class="hidden text-xs font-semibold text-rose-600 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition-colors">
                    Keluar
                </button>
                <a href="/admin" class="text-xs font-bold text-slate-600 hover:text-brand-600 bg-slate-100 hover:bg-brand-50 px-4 py-2 rounded-xl transition-all">
                    Portal Admin
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-6xl w-full mx-auto px-4 py-8 flex flex-col justify-center">
        
        <!-- ==================== PAGE 1: LOGIN ==================== -->
        <section id="login-section" class="w-full max-w-md mx-auto">
            <div class="glass-card rounded-3xl p-8 shadow-xl shadow-slate-200/50">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Selamat Datang di SIMPAD</h2>
                    <p class="text-slate-500 text-sm mt-1">Masuk untuk melihat jadwal hari ini dan melakukan presensi.</p>
                </div>

                <form id="login-form" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Email / NIS</label>
                        <div class="relative">
                            <input type="text" id="login-email" required
                                class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all"
                                placeholder="guru@smkn1demo.sch.id atau NIS siswa">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" id="login-password" required
                                class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" id="login-submit"
                        class="w-full py-4 bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-700 hover:to-brand-600 text-white font-bold rounded-2xl shadow-lg shadow-brand-500/30 transition-all flex items-center justify-center gap-2">
                        <span>Masuk Sekarang</span>
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-400">Akun demo Guru: <strong class="text-slate-600">guru@smkn1demo.sch.id</strong> | Kata Sandi: <strong class="text-slate-600">password</strong></p>
                </div>
            </div>
        </section>

        <!-- ==================== PAGE 2: TEACHER DASHBOARD ==================== -->
        <section id="teacher-section" class="hidden space-y-6">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-brand-600 to-brand-850 rounded-3xl p-6 text-white shadow-xl shadow-brand-900/10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-widest text-brand-200">Panel Guru</span>
                    <h2 id="teacher-welcome-name" class="text-2xl font-bold mt-0.5">Halo, Guru!</h2>
                    <p id="teacher-today-date" class="text-xs text-brand-100">Hari ini</p>
                </div>
                <button id="refresh-schedule-btn" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl text-xs font-bold transition-all border border-white/10">
                    Segarkan Jadwal
                </button>
            </div>

            <!-- Weekend / Simulation Alert Notice -->
            <div id="simulation-notice" class="hidden bg-amber-50 border border-amber-200/50 p-4 rounded-2xl text-xs text-amber-800 font-semibold flex items-center gap-3">
                <div>
                    <span class="font-bold">Mode Simulasi Uji Coba:</span> 
                    Hari ini libur/akhir pekan. Kami otomatis memunculkan jadwal <strong id="simulated-day-name">Senin</strong> agar Anda dapat mencoba membuka kelas dan mencoba presensi langsung!
                </div>
            </div>

            <!-- Day Selector Tabs for Easy Testing -->
            <div class="bg-white p-2.5 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-slate-800">Simulasikan Hari Mengajar</h4>
                        <p class="text-[10px] text-slate-400">Pilih hari untuk melihat & mencoba buka kelas presensi jadwal tersebut.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <button onclick="loadTeacherDashboard('monday')" id="tab-day-monday" class="day-tab px-3.5 py-2 text-xs font-bold rounded-xl transition-all text-slate-600 hover:bg-slate-50 border border-slate-100 bg-white">Senin</button>
                    <button onclick="loadTeacherDashboard('tuesday')" id="tab-day-tuesday" class="day-tab px-3.5 py-2 text-xs font-bold rounded-xl transition-all text-slate-600 hover:bg-slate-50 border border-slate-100 bg-white">Selasa</button>
                    <button onclick="loadTeacherDashboard('wednesday')" id="tab-day-wednesday" class="day-tab px-3.5 py-2 text-xs font-bold rounded-xl transition-all text-slate-600 hover:bg-slate-50 border border-slate-100 bg-white">Rabu</button>
                    <button onclick="loadTeacherDashboard('thursday')" id="tab-day-thursday" class="day-tab px-3.5 py-2 text-xs font-bold rounded-xl transition-all text-slate-600 hover:bg-slate-50 border border-slate-100 bg-white">Kamis</button>
                    <button onclick="loadTeacherDashboard('friday')" id="tab-day-friday" class="day-tab px-3.5 py-2 text-xs font-bold rounded-xl transition-all text-slate-600 hover:bg-slate-50 border border-slate-100 bg-white">Jumat</button>
                </div>
            </div>

            <!-- List Schedule Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-lg">Jadwal Pelajaran Tersedia</h3>
                    <span id="schedule-count-badge" class="bg-slate-100 text-slate-700 text-xs font-extrabold px-3 py-1 rounded-full">0 Sesi</span>
                </div>

                <!-- Unified Cards List -->
                <div id="schedule-list" class="grid gap-6 md:grid-cols-1 lg:grid-cols-2">
                    <!-- Dynamic cards will be populated here -->
                </div>
            </div>
        </section>

        <!-- ==================== PAGE 3: STUDENT SCANNER WORKSPACE ==================== -->
        <section id="student-section" class="hidden space-y-6 max-w-md mx-auto">
            <div class="glass-card rounded-3xl p-8 shadow-xl text-center space-y-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Scan QR Presensi Siswa</h2>
                    <p class="text-slate-500 text-sm mt-1" id="student-welcome-name">Halo, Siswa!</p>
                </div>

                <!-- Scanner Simulator Box -->
                <div class="bg-slate-900 rounded-2xl aspect-square flex flex-col items-center justify-center p-6 text-white relative overflow-hidden border-4 border-slate-800">
                    <div class="absolute inset-10 border-2 border-brand-400 border-dashed rounded-xl opacity-30 animate-pulse"></div>
                    <div class="absolute h-0.5 left-6 right-6 bg-brand-400 top-1/2 animate-bounce"></div>
                    
                    <p class="text-xs text-slate-400 z-10">Kamera aktif mendeteksi QR code...</p>
                </div>

                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Masukkan Token QR Manual</label>
                    <input type="text" id="manual-scan-token" 
                        class="w-full text-center px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all font-mono"
                        placeholder="Contoh: token_dari_layar_guru">
                </div>

                <button id="submit-scan-btn" class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-2xl shadow-lg shadow-brand-500/20 transition-all flex items-center justify-center gap-2">
                    <span>Kirim Presensi Sekarang</span>
                </button>
            </div>
        </section>

    </main>

    <!-- QR Code Modal (Desain overlay bersih untuk proyeksi ke siswa) -->
    <div id="qr-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full flex flex-col items-center gap-5 shadow-2xl relative">
            <button id="close-qr-modal-btn" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-lg">✕</button>
            
            <div class="text-center">
                <span class="bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                    QR PRESENSI AKTIF
                </span>
                <h3 id="qr-modal-class-title" class="font-extrabold text-slate-900 mt-2 text-lg">Kelas</h3>
                <p id="qr-modal-subject-title" class="text-xs text-slate-500">Mata Pelajaran</p>
            </div>
            
            <div id="modal-qr-container" class="bg-slate-50 border border-slate-100 rounded-2xl p-4 w-full aspect-square flex items-center justify-center max-w-[220px]">
                <!-- Image filled by JS -->
            </div>

            <div class="text-center w-full">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Token QR</div>
                <div id="modal-qr-token" class="font-mono text-sm bg-slate-50 py-1.5 px-3 rounded-lg text-slate-600 font-bold border border-slate-100 truncate"></div>
                <div id="modal-qr-timer" class="text-xs font-semibold text-emerald-600 mt-2">Masa berlaku...</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full text-center py-6 text-xs text-slate-400 border-t border-slate-100 bg-white/50 mt-12">
        <p>© 2026 SIMPAD — Sistem Informasi Presensi & Alumni Digital Sekolah. Hak Cipta Dilindungi.</p>
    </footer>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-50 space-y-2 max-w-sm w-full"></div>

    <!-- ==================== FRONTEND CONTROLLER JAVASCRIPT ==================== -->
    <script>
        // Core State
        let apiToken = localStorage.getItem('simpad_api_token') || '';
        let userData = JSON.parse(localStorage.getItem('simpad_user_data') || 'null');
        let activeSessionTimers = {}; // { sessionId: timerId }
        let modalSessionId = null;
        let modalTimer = null;
        let notificationsList = JSON.parse(localStorage.getItem('simpad_notifications') || '[]');

        // API Endpoint Base
        const API_URL = '/api/v1';

        // DOM elements
        const loginSection = document.getElementById('login-section');
        const teacherSection = document.getElementById('teacher-section');
        const studentSection = document.getElementById('student-section');
        const loginForm = document.getElementById('login-form');
        const logoutBtn = document.getElementById('logout-btn');
        const userInfoSpan = document.getElementById('user-info');
        const qrModal = document.getElementById('qr-modal');
        
        // Notifications Elements
        const bellContainer = document.getElementById('notification-bell-container');
        const bellBtn = document.getElementById('notification-bell-btn');
        const notificationBadge = document.getElementById('notification-badge');
        const notificationBadgeStatic = document.getElementById('notification-badge-static');
        const notificationDropdown = document.getElementById('notification-dropdown');
        const notificationListDiv = document.getElementById('notification-list');

        // Notification Helper Functions
        function addNotification(title, body, type = 'info') {
            const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            notificationsList.unshift({ title, body, type, time, read: false });
            
            // Limit to 15 notifications
            if (notificationsList.length > 15) {
                notificationsList.pop();
            }
            
            localStorage.setItem('simpad_notifications', JSON.stringify(notificationsList));
            renderNotifications();
            
            // Trigger animation/sound indicator if bell is visible
            if (apiToken) {
                notificationBadge.classList.remove('hidden');
                notificationBadgeStatic.classList.remove('hidden');
            }
        }

        function clearNotifications() {
            notificationsList = [];
            localStorage.removeItem('simpad_notifications');
            renderNotifications();
        }

        function renderNotifications() {
            if (notificationsList.length === 0) {
                notificationListDiv.innerHTML = '<div class="text-slate-400 text-center py-4">Belum ada notifikasi baru.</div>';
                notificationBadge.classList.add('hidden');
                notificationBadgeStatic.classList.add('hidden');
                return;
            }

            notificationListDiv.innerHTML = '';
            notificationsList.forEach((notif, index) => {
                const item = document.createElement('div');
                item.className = `p-2.5 rounded-xl border transition-all text-xs ${
                    notif.read ? 'bg-slate-50/50 border-slate-100 text-slate-550' : 'bg-brand-50/40 border-brand-100/50 text-slate-800 font-medium'
                }`;
                
                const typeColors = {
                    'success': 'text-emerald-600',
                    'danger': 'text-rose-600',
                    'warning': 'text-amber-600',
                    'info': 'text-brand-600'
                };
                const color = typeColors[notif.type] || 'text-slate-600';

                item.innerHTML = `
                    <div class="flex justify-between items-start gap-1">
                        <span class="font-bold ${color}">${notif.title}</span>
                        <span class="text-[9px] text-slate-400 font-mono">${notif.time}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 leading-snug">${notif.body}</p>
                `;
                notificationListDiv.appendChild(item);
            });

            // If there are unread notifications, show static badge
            const hasUnread = notificationsList.some(n => !n.read);
            if (hasUnread) {
                notificationBadgeStatic.classList.remove('hidden');
            } else {
                notificationBadge.classList.add('hidden');
                notificationBadgeStatic.classList.add('hidden');
            }
        }

        // Toggle notifications dropdown
        bellBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
            
            // Mark all as read when opened
            if (!notificationDropdown.classList.contains('hidden')) {
                notificationsList.forEach(n => n.read = true);
                localStorage.setItem('simpad_notifications', JSON.stringify(notificationsList));
                renderNotifications();
            }
        });

        // Hide dropdown when clicking elsewhere
        document.addEventListener('click', () => {
            notificationDropdown.classList.add('hidden');
        });
        notificationDropdown.addEventListener('click', (e) => e.stopPropagation());

        // Toast Notification Helper
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `flex items-center gap-3 p-4 rounded-2xl shadow-lg border text-sm transition-all duration-300 transform translate-y-2 opacity-0 ${
                type === 'success' ? 'bg-emerald-50 border-emerald-100 text-emerald-800' :
                type === 'danger' ? 'bg-rose-50 border-rose-100 text-rose-800' :
                'bg-amber-50 border-amber-100 text-amber-800'
            }`;
            
            const bulletColor = type === 'success' ? 'bg-emerald-500' : type === 'danger' ? 'bg-rose-500' : 'bg-amber-500';
            toast.innerHTML = `<span class="h-2 w-2 rounded-full ${bulletColor} inline-block shrink-0"></span><div class="flex-1 font-semibold">${message}</div>`;
            
            toastContainer.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Setup Headers for Fetch
        function getHeaders() {
            return {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${apiToken}`
            };
        }

        // Navigate views
        function showPage(pageId) {
            loginSection.classList.add('hidden');
            teacherSection.classList.add('hidden');
            studentSection.classList.add('hidden');

            document.getElementById(pageId).classList.remove('hidden');
        }

        // Handle Session Status check on startup
        function checkAuthState() {
            if (apiToken && userData) {
                userInfoSpan.textContent = `${userData.name} (${userData.role === 'teacher' ? 'Guru' : 'Siswa'})`;
                userInfoSpan.classList.remove('hidden');
                logoutBtn.classList.remove('hidden');
                
                // Show notification bell and render current notifications
                bellContainer.classList.remove('hidden');
                renderNotifications();

                if (userData.role === 'teacher') {
                    showPage('teacher-section');
                    loadTeacherDashboard();
                } else if (userData.role === 'student') {
                    showPage('student-section');
                    document.getElementById('student-welcome-name').textContent = `Selamat datang, ${userData.name}`;
                }
            } else {
                userInfoSpan.classList.add('hidden');
                logoutBtn.classList.add('hidden');
                bellContainer.classList.add('hidden');
                showPage('login-section');
            }
        }

        // Login Handler
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const submitBtn = document.getElementById('login-submit');

            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Memproses... ⏳';

            try {
                const response = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const resData = await response.json();

                if (resData.success) {
                    apiToken = resData.data.token;
                    userData = resData.data.user;

                    localStorage.setItem('simpad_api_token', apiToken);
                    localStorage.setItem('simpad_user_data', JSON.stringify(userData));

                    // Add Notification
                    addNotification('Masuk Sistem', `Selamat datang kembali, ${userData.name}!`, 'success');

                    showToast('Login berhasil!', 'success');
                    checkAuthState();
                } else {
                    showToast(resData.message || 'Email atau password salah.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showToast('Gagal terhubung ke server.', 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Masuk Sekarang</span><span>🚀</span>';
            }
        });

        // Logout Handler
        logoutBtn.addEventListener('click', () => {
            localStorage.removeItem('simpad_api_token');
            localStorage.removeItem('simpad_user_data');
            apiToken = '';
            userData = null;
            
            // Clear all active timers
            Object.values(activeSessionTimers).forEach(clearInterval);
            activeSessionTimers = {};
            clearInterval(modalTimer);

            showToast('Anda telah keluar.', 'success');
            checkAuthState();
        });

        // ==================== TEACHER FLOW (SINGLE DASHBOARD WITH INLINE CONTROLS) ====================

        let currentSelectedDay = ''; // mode default

        async function loadTeacherDashboard(day = null) {
            if (day) currentSelectedDay = day;

            const scheduleList = document.getElementById('schedule-list');
            const welcomeName = document.getElementById('teacher-welcome-name');
            const todayDateSpan = document.getElementById('teacher-today-date');
            const countBadge = document.getElementById('schedule-count-badge');
            const simNotice = document.getElementById('simulation-notice');
            const simDayName = document.getElementById('simulated-day-name');

            welcomeName.textContent = `Halo, ${userData.name}!`;
            scheduleList.innerHTML = '<div class="col-span-2 text-center text-slate-400 py-8">Memuat jadwal Anda...</div>';

            try {
                const url = currentSelectedDay ? `${API_URL}/teacher/today?day=${currentSelectedDay}` : `${API_URL}/teacher/today`;
                const res = await fetch(url, {
                    headers: getHeaders()
                });
                const data = await res.json();

                if (data.success) {
                    todayDateSpan.textContent = data.data.date;
                    const schedules = data.data.schedules;
                    countBadge.textContent = `${schedules.length} Sesi`;

                    // Highlight Active Day Tab
                    const activeDay = data.data.resolved_day; // 'monday', 'tuesday', etc.
                    const activeDayLabel = data.data.resolved_day_label; // 'Senin', 'Selasa', etc.
                    
                    document.querySelectorAll('.day-tab').forEach(tab => {
                        tab.classList.remove('bg-brand-600', 'text-white', 'hover:bg-slate-50', 'border-transparent');
                        tab.classList.add('text-slate-600', 'hover:bg-slate-50', 'border-slate-100', 'bg-white');
                    });
                    const activeTab = document.getElementById(`tab-day-${activeDay}`);
                    if (activeTab) {
                        activeTab.classList.remove('text-slate-600', 'hover:bg-slate-50', 'border-slate-100', 'bg-white');
                        activeTab.classList.add('bg-brand-600', 'text-white', 'border-transparent');
                    }

                    // Tampilkan info simulasi jika hari ini adalah akhir pekan (Sabtu/Minggu)
                    const todayDayOfWeek = new Date().getDay(); // 0 = Sunday, 6 = Saturday
                    if (todayDayOfWeek === 0 || todayDayOfWeek === 6 || currentSelectedDay) {
                        simNotice.classList.remove('hidden');
                        simDayName.textContent = activeDayLabel;
                    } else {
                        simNotice.classList.add('hidden');
                    }

                    if (schedules.length === 0) {
                        scheduleList.innerHTML = `
                            <div class="col-span-2 text-center py-12 bg-white rounded-3xl border border-slate-200 border-dashed p-8">
                                <h4 class="font-bold text-slate-800 mt-3">Tidak Ada Jadwal Hari ${activeDayLabel}</h4>
                                <p class="text-slate-400 text-xs mt-1">Anda tidak memiliki jadwal mengajar pada hari ${activeDayLabel}.</p>
                            </div>
                        `;
                        return;
                    }

                    scheduleList.innerHTML = '';
                    schedules.forEach(item => {
                        const statusColors = {
                            'teaching': 'bg-emerald-500 text-white border-emerald-600',
                            'eligible': 'bg-brand-500 text-white border-brand-600',
                            'upcoming': 'bg-slate-200 text-slate-700 border-slate-300',
                            'completed': 'bg-slate-600 text-white border-slate-700',
                            'missed': 'bg-rose-500 text-white border-rose-600'
                        };

                        const statusLabels = {
                            'teaching': 'Kelas Sedang Berlangsung',
                            'eligible': 'Siap Dibuka',
                            'upcoming': 'Akan Datang',
                            'completed': 'Sudah Selesai',
                            'missed': 'Terlewat'
                        };

                        const statusBadge = `<span class="px-2.5 py-1 text-[10px] font-bold rounded-lg ${statusColors[item.status] || 'bg-slate-100'}">${statusLabels[item.status] || item.status}</span>`;

                        // Action Buttons based on status
                        let controlPanel = '';
                        if (item.status === 'eligible') {
                            controlPanel = `
                                <div class="pt-4 border-t border-slate-100 mt-4">
                                    <button onclick="openClass(${item.schedule_id})" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-brand-500/10">
                                        Buka Kelas Sekarang
                                    </button>
                                </div>
                            `;
                        } else if (item.status === 'teaching') {
                            const sessionId = item.session.id;
                            controlPanel = `
                                <div class="pt-4 border-t border-slate-100 mt-4 space-y-4">
                                    <!-- Buttons to toggle QR and Manual inline -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <button onclick="showQrModal(${sessionId}, '${item.class.name}', '${item.subject.name}')" class="py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 shadow-md shadow-emerald-500/10">
                                            Tampilkan QR
                                        </button>
                                        <button onclick="toggleManualPresensi(${sessionId})" class="py-2.5 border border-brand-500 text-brand-600 hover:bg-brand-50 font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-1.5">
                                            Presensi Manual
                                        </button>
                                    </div>
                                    
                                    <!-- Inline Manual Attendance List (Hidden by default, toggled inline) -->
                                    <div id="manual-panel-${sessionId}" class="hidden space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                            <h5 class="font-bold text-xs text-slate-700">Daftar Kehadiran Siswa</h5>
                                            <button onclick="saveManualAttendance(${sessionId})" class="bg-brand-600 hover:bg-brand-700 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold">Simpan</button>
                                        </div>
                                        <div id="student-list-${sessionId}" class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                                            Memuat list siswa...
                                        </div>
                                    </div>

                                    <!-- Close Class Button -->
                                    <button onclick="closeClass(${sessionId})" class="w-full py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-xl text-xs transition-all">
                                        Tutup Sesi Kelas
                                    </button>
                                </div>
                            `;
                        } else if (item.status === 'completed') {
                            const sessionId = item.session.id;
                            controlPanel = `
                                <div class="pt-4 border-t border-slate-100 mt-4">
                                    <button onclick="toggleManualPresensi(${sessionId}, true)" class="w-full py-2.5 bg-slate-700 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-1.5">
                                        Lihat Hasil Kehadiran
                                    </button>
                                    
                                    <!-- Inline Manual Attendance List for finished sessions (read-only) -->
                                    <div id="manual-panel-${sessionId}" class="hidden space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 mt-3">
                                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                            <h5 class="font-bold text-xs text-slate-500">Rekap Kehadiran (Selesai)</h5>
                                            <span class="text-[10px] text-slate-400 font-semibold">Terkunci</span>
                                        </div>
                                        <div id="student-list-${sessionId}" class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                                            Memuat list siswa...
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            controlPanel = `
                                <div class="pt-4 border-t border-slate-100 mt-4 text-center text-xs text-slate-400 font-medium">
                                    Belum masuk jam pelajaran
                                </div>
                            `;
                        }

                        const card = document.createElement('div');
                        card.className = 'glass-card rounded-3xl p-6 shadow-sm hover:shadow-lg hover:border-slate-300/80 transition-all duration-300 flex flex-col justify-between';
                        
                        const formattedStartTime = (item.start_time || '').substring(0, 5);
                        const formattedEndTime = (item.end_time || '').substring(0, 5);

                        card.innerHTML = `
                            <div class="space-y-4">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-extrabold text-brand-700 bg-brand-50 border border-brand-100/50 px-2.5 py-1 rounded-xl uppercase tracking-wider">${item.class.name}</span>
                                    ${statusBadge}
                                </div>
                                <h4 class="font-extrabold text-slate-850 text-base leading-tight">${item.subject.name}</h4>
                                <div class="flex flex-wrap items-center gap-2 pt-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-50 border border-slate-100 text-slate-550">
                                        <span>Waktu:</span> <span>${formattedStartTime} - ${formattedEndTime}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-50 border border-slate-100 text-slate-550">
                                        <span>Ruang:</span> <span>${item.room || '-'}</span>
                                    </span>
                                </div>
                            </div>
                            ${controlPanel}
                        `;
                        scheduleList.appendChild(card);
                    });

                } else {
                    showToast(data.message || 'Gagal memuat jadwal.', 'danger');
                }
            } catch (err) {
                console.error(err);
                scheduleList.innerHTML = '<div class="col-span-2 text-center text-rose-500 py-8">Koneksi gagal atau terputus.</div>';
            }
        }

        // Open Class
        async function openClass(scheduleId) {
            try {
                const res = await fetch(`${API_URL}/attendance/open`, {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ schedule_id: scheduleId })
                });

                const data = await res.json();
                if (data.success) {
                    const className = data.data.schedule?.class?.name || 'Kelas';
                    const subjectName = data.data.schedule?.subject?.name || 'Mata Pelajaran';
                    addNotification('Kelas Dibuka', `Sesi presensi kelas ${className} (${subjectName}) berhasil dibuka.`, 'success');
                    showToast('Sesi presensi berhasil dibuka!', 'success');
                    loadTeacherDashboard(); // Refresh UI inline
                } else {
                    showToast(data.message || 'Gagal membuka kelas.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showToast('Kesalahan jaringan.', 'danger');
            }
        }

        // Toggle Manual Presensi List inline
        async function toggleManualPresensi(sessionId, readOnly = false) {
            const panel = document.getElementById(`manual-panel-${sessionId}`);
            const listDiv = document.getElementById(`student-list-${sessionId}`);
            
            if (!panel.classList.contains('hidden')) {
                panel.classList.add('hidden');
                return;
            }

            panel.classList.remove('hidden');
            listDiv.innerHTML = '<div class="text-center text-slate-400 text-xs py-4">Memuat daftar siswa...</div>';

            try {
                const res = await fetch(`${API_URL}/attendance/session/${sessionId}`, {
                    headers: getHeaders()
                });
                const data = await res.json();

                if (data.success) {
                    const records = data.data.attendance_records || [];
                    if (records.length === 0) {
                        listDiv.innerHTML = '<div class="text-center text-slate-400 text-xs py-4">Belum ada data siswa di kelas ini.</div>';
                        return;
                    }

                    listDiv.innerHTML = '';
                    records.forEach(rec => {
                        const s = rec.student;
                        const status = rec.status;

                        const row = document.createElement('div');
                        row.className = 'p-3.5 rounded-2xl bg-white border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm hover:border-slate-200 transition-all';

                        const options = [
                            { val: 'present', label: 'Hadir' },
                            { val: 'late', label: 'Terlambat' },
                            { val: 'permission', label: 'Izin' },
                            { val: 'sick', label: 'Sakit' },
                            { val: 'absent', label: 'Alpha' }
                        ];

                        let selectOptions = '';
                        options.forEach(opt => {
                            selectOptions += `<option value="${opt.val}" ${status === opt.val ? 'selected' : ''}>${opt.label}</option>`;
                        });

                        const badgeColors = {
                            'present': 'bg-emerald-50 border border-emerald-100 text-emerald-700',
                            'late': 'bg-amber-50 border border-amber-100 text-amber-700',
                            'permission': 'bg-blue-50 border border-blue-100 text-blue-700',
                            'sick': 'bg-purple-50 border border-purple-100 text-purple-700',
                            'absent': 'bg-rose-50 border border-rose-100 text-rose-700'
                        };
                        
                        const label = options.find(o => o.val === status)?.label || status;
                        const badgeClass = badgeColors[status] || 'bg-slate-50 border border-slate-100 text-slate-700';

                        let noteHtml = '';
                        if (rec.note) {
                            noteHtml = `<span class="block text-[10px] text-slate-400 mt-1 font-semibold text-right">Catatan: ${rec.note}</span>`;
                        }

                        const formControl = readOnly 
                            ? `<div class="text-right">
                                  <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-xl uppercase tracking-wider ${badgeClass}">${label}</span>
                                  ${noteHtml}
                               </div>`
                            : `
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <select data-student-id="${s.id}" class="select-status-${sessionId} bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] font-bold text-slate-700 focus:bg-white focus:border-brand-500 outline-none transition-all">
                                        ${selectOptions}
                                    </select>
                                    <input type="text" data-student-id="${s.id}" class="note-input-${sessionId} bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] w-full sm:w-32 focus:bg-white focus:border-brand-500 outline-none text-slate-600 transition-all" placeholder="Catatan..." value="${rec.note || ''}">
                                </div>
                            `;

                        row.innerHTML = `
                            <div class="flex-1 min-w-0">
                                <div class="font-extrabold text-[13px] text-slate-800 truncate">${s.name}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">NIS: ${s.nis}</div>
                            </div>
                            ${formControl}
                        `;
                        listDiv.appendChild(row);
                    });
                } else {
                    listDiv.innerHTML = '<div class="text-center text-rose-500 text-xs py-4">Gagal memuat data siswa.</div>';
                }
            } catch (err) {
                console.error(err);
                listDiv.innerHTML = '<div class="text-center text-rose-500 text-xs py-4">Kesalahan jaringan.</div>';
            }
        }

        // Save manual attendance inline
        async function saveManualAttendance(sessionId) {
            const selects = document.querySelectorAll(`.select-status-${sessionId}`);
            const notes = document.querySelectorAll(`.note-input-${sessionId}`);
            
            const attendances = [];
            selects.forEach(select => {
                const studentId = select.getAttribute('data-student-id');
                const status = select.value;
                
                let note = '';
                notes.forEach(noteInput => {
                    if (noteInput.getAttribute('data-student-id') === studentId) {
                        note = noteInput.value;
                    }
                });

                attendances.push({
                    student_id: parseInt(studentId),
                    status: status,
                    note: note
                });
            });

            try {
                const res = await fetch(`${API_URL}/attendance/manual`, {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({
                        session_id: sessionId,
                        attendances: attendances
                    })
                });

                const data = await res.json();
                if (data.success) {
                    addNotification('Presensi Manual', `Berhasil menyimpan presensi manual untuk ${data.count} siswa.`, 'success');
                    showToast('Presensi manual berhasil disimpan!', 'success');
                    // Toggle off then on to refresh data
                    document.getElementById(`manual-panel-${sessionId}`).classList.add('hidden');
                    toggleManualPresensi(sessionId);
                } else {
                    showToast(data.message || 'Gagal menyimpan.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showToast('Kesalahan koneksi.', 'danger');
            }
        }

        // Close Session
        async function closeClass(sessionId) {
            if (!confirm('Apakah Anda yakin ingin menutup sesi kelas ini? Setelah ditutup, siswa tidak bisa scan QR dan presensi terkunci.')) {
                return;
            }

            try {
                const res = await fetch(`${API_URL}/attendance/close`, {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ session_id: sessionId })
                });

                const data = await res.json();
                if (data.success) {
                    const className = data.data.schedule?.class?.name || 'Kelas';
                    addNotification('Kelas Ditutup', `Sesi presensi untuk kelas ${className} telah resmi ditutup.`, 'info');
                    showToast('Sesi presensi ditutup!', 'success');
                    loadTeacherDashboard(); // Refresh full UI
                } else {
                    showToast(data.message || 'Gagal menutup sesi.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showToast('Kesalahan koneksi.', 'danger');
            }
        }

        // ==================== OVERLAY QR CODE MODAL ====================

        async function showQrModal(sessionId, className, subjectName) {
            qrModal.classList.remove('hidden');
            modalSessionId = sessionId;
            
            document.getElementById('qr-modal-class-title').textContent = className;
            document.getElementById('qr-modal-subject-title').textContent = subjectName;
            
            await fetchModalQrToken();
        }

        async function fetchModalQrToken() {
            if (!modalSessionId) return;

            try {
                const res = await fetch(`${API_URL}/attendance/generate-qr`, {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ session_id: modalSessionId })
                });
                const data = await res.json();

                if (data.success) {
                    const tokenData = data.data;
                    document.getElementById('modal-qr-token').textContent = tokenData.token;

                    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(tokenData.token)}`;
                    document.getElementById('modal-qr-container').innerHTML = `<img src="${qrUrl}" alt="Scan QR" class="border rounded-xl shadow-sm" width="200" height="200">`;

                    let timeLeft = tokenData.expires_in_seconds;
                    document.getElementById('modal-qr-timer').textContent = `Masa berlaku QR: ${timeLeft} detik`;

                    clearInterval(modalTimer);
                    modalTimer = setInterval(() => {
                        timeLeft--;
                        if (timeLeft <= 0) {
                            clearInterval(modalTimer);
                            fetchModalQrToken(); // Auto-renew
                        } else {
                            document.getElementById('modal-qr-timer').textContent = `Masa berlaku QR: ${timeLeft} detik`;
                        }
                    }, 1000);
                } else {
                    showToast('Gagal generate token QR.', 'danger');
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Close Modal
        document.getElementById('close-qr-modal-btn').addEventListener('click', () => {
            qrModal.classList.add('hidden');
            modalSessionId = null;
            clearInterval(modalTimer);
        });

        // Bind Refresh Button
        document.getElementById('refresh-schedule-btn').addEventListener('click', loadTeacherDashboard);


        // ==================== STUDENT FLOW JAVASCRIPT ====================

        document.getElementById('submit-scan-btn').addEventListener('click', async () => {
            const token = document.getElementById('manual-scan-token').value;
            const submitBtn = document.getElementById('submit-scan-btn');

            if (!token) {
                showToast('Masukkan Token QR terlebih dahulu.', 'amber');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Memproses...';

            try {
                const res = await fetch(`${API_URL}/attendance/scan`, {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ token: token })
                });

                const data = await res.json();
                if (data.success) {
                    addNotification('Presensi Berhasil', `Presensi scan QR Anda berhasil dicatat dengan status: ${data.data.status_label || 'Hadir'}.`, 'success');
                    showToast('Presensi Anda berhasil dicatat!', 'success');
                    document.getElementById('manual-scan-token').value = '';
                } else {
                    showToast(data.message || 'Token tidak valid.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showToast('Gagal mengirim data presensi.', 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Kirim Kehadiran</span>';
            }
        });

        // Init App on Load
        checkAuthState();
    </script>
</body>
</html>
