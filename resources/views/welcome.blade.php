<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPAD — Sistem Presensi Alumni Digital</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts: Inter for body, Lexend for headings -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Lexend:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'lexend': ['Lexend', 'sans-serif'],
                    },
                    colors: {
                        'edu': {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* ============================================================
           RESET & BASE
           ============================================================ */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #172554;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ============================================================
           TYPOGRAPHY UTILITIES
           ============================================================ */
        .heading-xl {
            font-family: 'Lexend', sans-serif;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.05;
        }

        .heading-lg {
            font-family: 'Lexend', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .heading-md {
            font-family: 'Lexend', sans-serif;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .mono-label {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* ============================================================
           DIVIDER LINES (SWISS STYLE)
           ============================================================ */
        .divider-h {
            width: 100%;
            height: 1px;
            background-color: #2563eb;
            opacity: 0.2;
        }

        .divider-v {
            width: 1px;
            height: 100%;
            background-color: #2563eb;
            opacity: 0.2;
        }

        /* ============================================================
           SWIPER CUSTOMIZATION
           ============================================================ */
        .swiper {
            width: 100%;
            height: 360px;
            overflow: hidden;
            border: 1px solid #bfdbfe;
        }

        .swiper-slide {
            position: relative;
            overflow: hidden;
            background-color: #eff6ff;
        }

        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .swiper-slide:hover img {
            transform: scale(1.04);
        }

        /* ============================================================
           INTERSECTION OBSERVER REVEAL
           ============================================================ */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ============================================================
           STAGGERED ITEMS
           ============================================================ */
        .stagger {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .stagger.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ============================================================
           LINK UNDERLINE ANIMATION
           ============================================================ */
        .link-underline {
            position: relative;
            text-decoration: none;
            padding-bottom: 2px;
        }

        .link-underline::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1.5px;
            background-color: #1d4ed8;
            transition: width 0.3s ease;
        }

        .link-underline:hover::after {
            width: 100%;
        }

        /* ============================================================
           BUTTON STYLES
           ============================================================ */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2.25rem;
            background-color: #1d4ed8;
            color: #ffffff;
            font-family: 'Lexend', sans-serif;
            font-weight: 600;
            font-size: 1.05rem;
            letter-spacing: -0.01em;
            border: 2px solid #1d4ed8;
            transition: all 0.25s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary:hover {
            background-color: #1e40af;
            border-color: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(29, 78, 216, 0.25);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2.25rem;
            background-color: transparent;
            color: #1d4ed8;
            font-family: 'Lexend', sans-serif;
            font-weight: 600;
            font-size: 1.05rem;
            letter-spacing: -0.01em;
            border: 2px solid #1d4ed8;
            transition: all 0.25s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-outline:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }

        /* ============================================================
           ACCENT BAR (LEFT COLORED STRIP)
           ============================================================ */
        .accent-bar {
            position: relative;
            padding-left: 1.5rem;
        }

        .accent-bar::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: #2563eb;
        }

        /* ============================================================
           CUSTOM SCROLLBAR
           ============================================================ */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #eff6ff;
        }

        ::-webkit-scrollbar-thumb {
            background: #93c5fd;
            border-radius: 3px;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 768px) {
            .swiper {
                height: 280px;
            }
        }

        @media (max-width: 640px) {
            .swiper {
                height: 240px;
            }
        }
    </style>
</head>
<body class="font-inter">

    <!-- ============================================================
         HEADER BAR
         ============================================================ -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 border-b border-edu-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-edu-700 flex items-center justify-center">
                    <span class="text-white font-lexend font-bold text-sm">S</span>
                </div>
                <span class="font-lexend font-bold text-edu-900 text-lg">SIMPAD</span>
            </div>
            <a href="/admin/login" class="mono-label text-xs text-edu-700 link-underline hidden sm:block">
                Masuk Dashboard →
            </a>
        </div>
    </header>

    <!-- ============================================================
         HERO SECTION
         ============================================================ -->
    <section class="relative pt-28 pb-20 px-4 sm:px-6 lg:px-12 bg-edu-50">
        <div class="max-w-7xl mx-auto">

            <!-- Label -->
            <p class="mono-label text-xs text-edu-600 mb-6 reveal">
                Platform Presensi Digital
            </p>

            <!-- Main Headline -->
            <div class="grid lg:grid-cols-12 gap-8 items-end mb-12">
                <div class="lg:col-span-8 reveal">
                    <h1 class="heading-xl text-5xl sm:text-6xl lg:text-7xl xl:text-8xl text-edu-950">
                        Sistem<br>
                        Presensi<br>
                        <span class="text-edu-600">Alumni</span>
                        <span class="text-edu-300 font-light">Digital</span>
                    </h1>
                </div>
                <div class="lg:col-span-4 reveal">
                    <div class="accent-bar">
                        <p class="text-edu-700 text-lg leading-relaxed mb-6">
                            Kelola presensi dan data alumni dengan sistem digital yang cepat, aman, dan terintegrasi.
                        </p>
                        <a href="/admin/login" class="btn-primary">
                            <span>Cobain Sekarang</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-3 gap-0 reveal">
                <div class="border-t border-edu-200 pt-6">
                    <p class="heading-lg text-3xl sm:text-4xl text-edu-900" id="stat-schools">0</p>
                    <p class="mono-label text-[10px] text-edu-500 mt-1">Sekolah Aktif</p>
                </div>
                <div class="border-t border-edu-200 pt-6">
                    <p class="heading-lg text-3xl sm:text-4xl text-edu-900" id="stat-alumni">0</p>
                    <p class="mono-label text-[10px] text-edu-500 mt-1">Alumni Terdata</p>
                </div>
                <div class="border-t border-edu-200 pt-6">
                    <p class="heading-lg text-3xl sm:text-4xl text-edu-900" id="stat-attendance">0</p>
                    <p class="mono-label text-[10px] text-edu-500 mt-1">Presensi Terdata</p>
                </div>
            </div>

        </div>
    </section>

    <!-- ============================================================
         ROLES SECTION
         ============================================================ -->
    <section class="relative py-24 px-4 sm:px-6 lg:px-12 bg-white">
        <div class="max-w-7xl mx-auto">

            <!-- Section Header -->
            <div class="flex items-center gap-4 mb-16 reveal">
                <span class="mono-label text-xs text-edu-500">01</span>
                <div class="divider-h flex-1"></div>
                <h2 class="heading-lg text-3xl sm:text-4xl text-edu-950">Pilih Peran Anda</h2>
            </div>

            <!-- Staggered Cards -->
            <div class="space-y-16">

                <!-- Card 1: Siswa -->
                <div class="stagger grid md:grid-cols-12 gap-6 items-center">
                    <div class="md:col-span-5 md:col-start-1 order-2 md:order-1">
                        <p class="mono-label text-[10px] text-edu-500 mb-3">Role 01</p>
                        <h3 class="heading-md text-2xl text-edu-900 mb-3">Siswa</h3>
                        <p class="text-edu-600 text-sm leading-relaxed mb-4 max-w-sm">
                            Presensi harian dengan scan QR Code. Riwayat kehadiran dan statistik personal tersedia real-time.
                        </p>
                        <a href="#" class="link-underline text-sm text-edu-700 font-medium">Lihat Detail →</a>
                    </div>
                    <div class="md:col-span-7 md:col-start-6 order-1 md:order-2">
                        <div class="swiper murid-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide"><img src="{{ asset('murid/murid1.png') }}" alt="Siswa 1" loading="lazy"></div>
                                <div class="swiper-slide"><img src="{{ asset('murid/murid2.png') }}" alt="Siswa 2" loading="lazy"></div>
                                <div class="swiper-slide"><img src="{{ asset('murid/murid3.png') }}" alt="Siswa 3" loading="lazy"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Guru -->
                <div class="stagger grid md:grid-cols-12 gap-6 items-center">
                    <div class="md:col-span-7 md:col-start-1 order-1">
                        <div class="swiper guru-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide"><img src="{{ asset('guru/guru1.png') }}" alt="Guru 1" loading="lazy"></div>
                                <div class="swiper-slide"><img src="{{ asset('guru/guru2.png') }}" alt="Guru 2" loading="lazy"></div>
                                <div class="swiper-slide"><img src="{{ asset('guru/guru3.png') }}" alt="Guru 3" loading="lazy"></div>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-5 md:col-start-8 order-2">
                        <p class="mono-label text-[10px] text-edu-500 mb-3">Role 02</p>
                        <h3 class="heading-md text-2xl text-edu-900 mb-3">Guru</h3>
                        <p class="text-edu-600 text-sm leading-relaxed mb-4 max-w-sm">
                            Kelola presensi kelas, buat laporan kehadiran, dan pantau siswa dengan dashboard intuitif.
                        </p>
                        <a href="#" class="link-underline text-sm text-edu-700 font-medium">Lihat Detail →</a>
                    </div>
                </div>

                <!-- Card 3: Alumni -->
                <div class="stagger grid md:grid-cols-12 gap-6 items-center">
                    <div class="md:col-span-5 md:col-start-1 order-2 md:order-1">
                        <p class="mono-label text-[10px] text-edu-500 mb-3">Role 03</p>
                        <h3 class="heading-md text-2xl text-edu-900 mb-3">Alumni</h3>
                        <p class="text-edu-600 text-sm leading-relaxed mb-4 max-w-sm">
                            Akses data alumni, jaringan karir, dan tracking pasca kelulusan dalam satu platform.
                        </p>
                        <a href="#" class="link-underline text-sm text-edu-700 font-medium">Lihat Detail →</a>
                    </div>
                    <div class="md:col-span-7 md:col-start-6 order-1 md:order-2">
                        <div class="swiper alumni-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide"><img src="{{ asset('alumni/alumni1.png') }}" alt="Alumni 1" loading="lazy"></div>
                                <div class="swiper-slide"><img src="{{ asset('alumni/alumni2.png') }}" alt="Alumni 2" loading="lazy"></div>
                                <div class="swiper-slide"><img src="{{ asset('alumni/alumni3.png') }}" alt="Alumni 3" loading="lazy"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Orang Tua -->
                <div class="stagger grid md:grid-cols-12 gap-6 items-center">
                    <div class="md:col-span-7 md:col-start-1 order-1">
                        <div class="swiper ortu-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide"><img src="{{ asset('ortu/ortu1.png') }}" alt="Orang Tua 1" loading="lazy"></div>
                                <div class="swiper-slide"><img src="{{ asset('ortu/ortu2.png') }}" alt="Orang Tua 2" loading="lazy"></div>
                                <div class="swiper-slide"><img src="{{ asset('ortu/ortu3.png') }}" alt="Orang Tua 3" loading="lazy"></div>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-5 md:col-start-8 order-2">
                        <p class="mono-label text-[10px] text-edu-500 mb-3">Role 04</p>
                        <h3 class="heading-md text-2xl text-edu-900 mb-3">Orang Tua</h3>
                        <p class="text-edu-600 text-sm leading-relaxed mb-4 max-w-sm">
                            Pantau kehadiran anak, terima notifikasi instan, dan akses laporan berkala.
                        </p>
                        <a href="#" class="link-underline text-sm text-edu-700 font-medium">Lihat Detail →</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================================
         FEATURES SECTION
         ============================================================ -->
    <section class="relative py-24 px-4 sm:px-6 lg:px-12 bg-edu-50">
        <div class="max-w-7xl mx-auto">

            <!-- Section Header -->
            <div class="flex items-center gap-4 mb-16 reveal">
                <span class="mono-label text-xs text-edu-500">02</span>
                <div class="divider-h flex-1"></div>
                <h2 class="heading-lg text-3xl sm:text-4xl text-edu-950">Kenapa SIMPAD?</h2>
            </div>

            <!-- Feature Grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 border border-edu-200">
                <!-- Feature 1 -->
                <div class="p-8 bg-white hover:bg-edu-50 transition-colors duration-200">
                    <span class="text-3xl block mb-5">⚡</span>
                    <h4 class="heading-md text-lg text-edu-900 mb-2">Real-time Sync</h4>
                    <p class="text-edu-500 text-sm leading-relaxed">Data presensi tersinkronisasi instan ke semua perangkat tanpa delay.</p>
                </div>

                <!-- Feature 2 -->
                <div class="p-8 bg-white hover:bg-edu-50 transition-colors duration-200 border-l border-edu-200">
                    <span class="text-3xl block mb-5">🔐</span>
                    <h4 class="heading-md text-lg text-edu-900 mb-2">QR Authentication</h4>
                    <p class="text-edu-500 text-sm leading-relaxed">Sistem autentikasi aman dengan QR Code unik untuk setiap pengguna.</p>
                </div>

                <!-- Feature 3 -->
                <div class="p-8 bg-white hover:bg-edu-50 transition-colors duration-200 border-t sm:border-t-0 lg:border-l border-edu-200">
                    <span class="text-3xl block mb-5">📊</span>
                    <h4 class="heading-md text-lg text-edu-900 mb-2">Analytics</h4>
                    <p class="text-edu-500 text-sm leading-relaxed">Visualisasi data kehadiran dengan grafik interaktif dan laporan detail.</p>
                </div>

                <!-- Feature 4 -->
                <div class="p-8 bg-white hover:bg-edu-50 transition-colors duration-200 border-l border-t sm:border-t-0 border-edu-200">
                    <span class="text-3xl block mb-5">🔔</span>
                    <h4 class="heading-md text-lg text-edu-900 mb-2">Notifikasi</h4>
                    <p class="text-edu-500 text-sm leading-relaxed">Notifikasi otomatis ke orang tua saat siswa tidak hadir di sekolah.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- ============================================================
         CTA SECTION
         ============================================================ -->
    <section class="relative py-24 px-4 sm:px-6 lg:px-12 bg-edu-700">
        <div class="max-w-4xl mx-auto text-center">

            <p class="mono-label text-xs text-edu-300 mb-4 reveal">✦ Siap Bergabung?</p>

            <h2 class="heading-xl text-4xl sm:text-5xl lg:text-6xl text-white mb-6 reveal">
                Mulai Digitalisasi<br>
                Presensi Anda
            </h2>

            <p class="text-edu-200 text-lg mb-10 max-w-xl mx-auto reveal">
                Bergabung dengan ratusan sekolah yang telah menggunakan sistem presensi digital SIMPAD.
            </p>

            <a href="/admin/login" class="btn-outline border-white text-white hover:bg-white hover:text-edu-800 reveal">
                <span>Akses Dashboard</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>

        </div>
    </section>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="bg-edu-950 py-10 px-4 sm:px-6 lg:px-12">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 bg-edu-400 flex items-center justify-center">
                    <span class="text-edu-950 font-lexend font-bold text-xs">S</span>
                </div>
                <span class="text-edu-300 text-sm font-lexend font-semibold">SIMPAD</span>
            </div>
            <p class="text-edu-500 text-xs">© 2024 Sistem Presensi Alumni Digital</p>
            <div class="flex items-center gap-6">
                <a href="#" class="text-edu-500 text-xs hover:text-edu-300 transition-colors">Twitter</a>
                <a href="#" class="text-edu-500 text-xs hover:text-edu-300 transition-colors">GitHub</a>
                <a href="#" class="text-edu-500 text-xs hover:text-edu-300 transition-colors">Dokumentasi</a>
            </div>
        </div>
    </footer>

    <!-- ============================================================
         SCRIPTS
         ============================================================ -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        (function() {
            'use strict';

            // ============================================================
            // INTERSECTION OBSERVER — REVEAL & STAGGER
            // ============================================================
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -40px 0px',
            });

            document.querySelectorAll('.reveal, .stagger').forEach(el => {
                observer.observe(el);
            });

            // ============================================================
            // SWIPERS — SIMPLE FADE TRANSITION
            // ============================================================
            const swiperConfigs = [
                { selector: '.murid-slider', delay: 4000 },
                { selector: '.guru-slider', delay: 4500 },
                { selector: '.alumni-slider', delay: 5000 },
                { selector: '.ortu-slider', delay: 5500 },
            ];

            const swipers = [];

            swiperConfigs.forEach(config => {
                const el = document.querySelector(config.selector);
                if (!el) return;

                const swiper = new Swiper(el, {
                    loop: true,
                    speed: 600,
                    effect: 'fade',
                    fadeEffect: { crossFade: true },
                    autoplay: {
                        delay: config.delay,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    allowTouchMove: true,
                    grabCursor: true,
                });

                swipers.push(swiper);
            });

            // Pause autoplay when page is hidden
            document.addEventListener('visibilitychange', () => {
                swipers.forEach(swiper => {
                    if (document.hidden) {
                        swiper.autoplay.stop();
                    } else {
                        swiper.autoplay.start();
                    }
                });
            });

            // ============================================================
            // REAL-TIME STATS FETCH & ANIMATION
            // ============================================================
            async function fetchStats() {
                try {
                    const response = await fetch('/public-stats');
                    const data = await response.json();
                    
                    animateCount('stat-schools', data.schools);
                    animateCount('stat-alumni', data.alumni);
                    animateCount('stat-attendance', data.attendance);
                } catch (error) {
                    console.error('Failed to fetch stats:', error);
                    // Fallback to static numbers if request fails
                    animateCount('stat-schools', 500);
                    animateCount('stat-alumni', 100000);
                    animateCount('stat-attendance', 25000);
                }
            }

            function animateCount(id, target) {
                const el = document.getElementById(id);
                if (!el) return;

                let start = 0;
                const duration = 1500; // 1.5s
                const startTime = performance.now();

                function update(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Ease out expo formula for premium smooth slowing down
                    const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                    
                    const currentValue = start + (target - start) * easeProgress;
                    
                    if (target % 1 === 0) {
                        // Integer formatting
                        el.textContent = Math.floor(currentValue).toLocaleString('id-ID') + (target >= 1000 ? '+' : '');
                    } else {
                        // Float formatting
                        el.textContent = currentValue.toFixed(1);
                    }

                    if (progress < 1) {
                        requestAnimationFrame(update);
                    }
                }

                requestAnimationFrame(update);
            }

            fetchStats();

            console.log('%cSIMPAD%c — Sistem Presensi Alumni Digital',
                'font-weight: 800; color: #1d4ed8;',
                'color: #475569;');
        })();
    </script>

</body>
</html>