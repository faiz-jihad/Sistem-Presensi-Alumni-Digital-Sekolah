<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Hero Header Section --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-blue-700 p-6 sm:p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 border border-primary-500/10">
            {{-- Decorative Subtle SVG Shapes --}}
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute -bottom-16 right-16 w-56 h-56 rounded-full bg-white/5 pointer-events-none"></div>

            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm border border-white/20">
                    <x-heroicon-o-chat-bubble-left-right class="text-white" style="width: 24px; height: 24px;" />
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-white tracking-tight">Kirim Notifikasi WhatsApp</h2>
                    <p class="text-xs text-blue-100/90 mt-0.5">Kirim rekapitulasi kehadiran massal ke nomor WhatsApp orang tua siswa terdaftar secara otomatis.</p>
                </div>
            </div>
            
            <div class="relative z-10 flex flex-wrap items-center gap-2.5 self-start md:self-auto">
                <span class="inline-flex items-center gap-1.5 bg-white/10 dark:bg-white/5 border border-white/25 dark:border-white/10 rounded-full px-3 py-1 text-xs font-semibold text-white backdrop-blur-md">
                    <span class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.5)]"></span>
                    Antrean Aktif
                </span>
                <span class="inline-flex items-center bg-white/10 dark:bg-white/5 border border-white/25 dark:border-white/10 rounded-full px-3 py-1 text-xs font-semibold text-white backdrop-blur-md">
                    Integrasi Fonnte
                </span>
            </div>
        </div>

        {{-- Stats Cards Grid (Precise border indicators & alignment) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Siswa --}}
            <div class="relative rounded-2xl bg-white p-6 shadow-sm border border-gray-200 border-l-4 border-l-primary-600 dark:bg-zinc-900 dark:border-zinc-800 dark:border-l-primary-500 transition duration-300 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-950/20 text-primary-600 dark:text-primary-400 rounded-xl">
                        <x-heroicon-o-user-group style="width: 24px; height: 24px;" />
                    </div>
                    <div class="space-y-1">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider leading-none">Total Siswa Aktif</div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white leading-none mt-1">{{ $this->getStats()['total_students'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Terdaftar WA --}}
            <div class="relative rounded-2xl bg-white p-6 shadow-sm border border-gray-200 border-l-4 border-l-emerald-600 dark:bg-zinc-900 dark:border-zinc-800 dark:border-l-emerald-500 transition duration-300 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <x-heroicon-o-device-phone-mobile style="width: 24px; height: 24px;" />
                    </div>
                    <div class="space-y-1">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider leading-none">Orang Tua Terdaftar WA</div>
                        <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 leading-none mt-1">{{ $this->getStats()['has_phone'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Belum ada nomor --}}
            <div class="relative rounded-2xl bg-white p-6 shadow-sm border border-gray-200 border-l-4 border-l-rose-600 dark:bg-zinc-900 dark:border-zinc-800 dark:border-l-rose-500 transition duration-300 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 rounded-xl">
                        <x-heroicon-o-exclamation-triangle style="width: 24px; height: 24px;" />
                    </div>
                    <div class="space-y-1">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider leading-none">Belum Ada No. WA</div>
                        <div class="text-3xl font-black text-rose-600 dark:text-rose-500 leading-none mt-1">{{ $this->getStats()['no_phone'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form & Actions Section --}}
        <x-filament::section class="shadow-sm">
            <x-slot name="heading">Filter &amp; Konfigurasi Notifikasi</x-slot>
            <x-slot name="description">Tentukan tipe laporan serta periode rekapitulasi kehadiran.</x-slot>

            <div class="space-y-6 mt-4">
                {{ $this->form }}

                <div class="pt-5 border-t border-gray-100 dark:border-zinc-850 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                    {{-- Recipient badge --}}
                    <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-400 text-xs font-semibold self-start">
                        <x-heroicon-s-users style="width: 16px; height: 16px;" />
                        <span>{{ $this->getRecipientCount() }} Orang tua terdaftar sebagai penerima</span>
                    </div>

                    {{-- Action Button --}}
                    <div class="flex justify-end">
                        {{ $this->sendNotifAction }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Info & Warning Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Info Box --}}
            <x-filament::section class="shadow-sm">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-information-circle class="text-primary-600 dark:text-primary-400" style="width: 20px; height: 20px;" />
                        <span class="text-sm font-bold text-gray-900 dark:text-white">Alur &amp; Cara Kerja</span>
                    </div>
                </x-slot>
                
                <ol class="space-y-4 mt-4 text-xs text-gray-600 dark:text-gray-400">
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-primary-50 dark:bg-primary-950/30 text-primary-600 dark:text-primary-400 font-bold flex items-center justify-center flex-shrink-0">1</span>
                        <div class="space-y-0.5">
                            <p class="font-bold text-gray-800 dark:text-gray-200">Pilih Periode Rekap</p>
                            <p class="text-gray-500">Tentukan format rekapitulasi harian (tanggal) atau bulanan (bulan &amp; tahun).</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-primary-50 dark:bg-primary-950/30 text-primary-600 dark:text-primary-400 font-bold flex items-center justify-center flex-shrink-0">2</span>
                        <div class="space-y-0.5">
                            <p class="font-bold text-gray-800 dark:text-gray-200">Kirim Notifikasi</p>
                            <p class="text-gray-500">Klik tombol kirim di atas. Konfirmasi jumlah penerima akan muncul sebelum pengiriman.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-primary-50 dark:bg-primary-950/30 text-primary-600 dark:text-primary-400 font-bold flex items-center justify-center flex-shrink-0">3</span>
                        <div class="space-y-0.5">
                            <p class="font-bold text-gray-800 dark:text-gray-200">Sistem Antrean (Queue)</p>
                            <p class="text-gray-500">Pesan diproses satu persatu secara asinkron agar aman dari rate limiting WhatsApp.</p>
                        </div>
                    </li>
                </ol>
            </x-filament::section>

            {{-- Warning Box --}}
            <x-filament::section class="shadow-sm">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="text-amber-600 dark:text-amber-400" style="width: 20px; height: 20px;" />
                        <span class="text-sm font-bold text-gray-900 dark:text-white">Persyaratan &amp; Status</span>
                    </div>
                </x-slot>

                <div class="space-y-4 mt-4 text-xs text-gray-600 dark:text-gray-400">
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2.5">
                            <x-heroicon-s-check-circle style="width: 18px; height: 18px; color: currentColor;" class="text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                            <span>Token Fonnte terkonfigurasi di env <code class="bg-gray-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded font-mono text-rose-500 text-[10px]">WHATSAPP_API_TOKEN</code>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <x-heroicon-s-check-circle style="width: 18px; height: 18px; color: currentColor;" class="text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                            <span>Jalankan Queue worker: <code class="bg-gray-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded font-mono text-blue-500 text-[10px]">php artisan queue:work</code>.</span>
                        </li>
                    </ul>

                    <div class="pt-4 border-t border-gray-100 dark:border-zinc-800 text-[10px] text-gray-500 leading-relaxed">
                        Sistem juga mengirim notifikasi otomatis real-time ke orang tua setiap kali siswa melakukan scan QR mandiri atau guru menginput kehadiran di kelas.
                    </div>
                </div>
            </x-filament::section>
        </div>

    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>