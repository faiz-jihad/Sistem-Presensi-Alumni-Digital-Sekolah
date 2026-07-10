<x-filament-widgets::widget>
    <div class="mb-6">
        <div class="flex items-center gap-2">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">Aksi Cepat Admin</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Pintasan tindakan utama untuk mengelola aktivitas sekolah dengan mudah.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <!-- Buat Jadwal -->
            <a href="/admin/schedules/create" class="flex items-center gap-4 p-5 bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:border-amber-500 dark:hover:border-amber-500 hover:shadow-md transition-all duration-200 group">
                <div class="p-3 bg-amber-50 dark:bg-amber-950/20 rounded-xl text-amber-600 dark:text-amber-400 group-hover:scale-105 transition-transform duration-200">
                    <x-filament::icon icon="heroicon-o-calendar-days" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">Buat Jadwal</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Atur jadwal belajar kelas</p>
                </div>
            </a>

            <!-- Kelola Guru -->
            <a href="/admin/teachers" class="flex items-center gap-4 p-5 bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:border-emerald-500 dark:hover:border-emerald-500 hover:shadow-md transition-all duration-200 group">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/20 rounded-xl text-emerald-600 dark:text-emerald-400 group-hover:scale-105 transition-transform duration-200">
                    <x-filament::icon icon="heroicon-o-user-group" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">Kelola Guru</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar & hak mengajar guru</p>
                </div>
            </a>

            <!-- Kelola Siswa -->
            <a href="/admin/students" class="flex items-center gap-4 p-5 bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-md transition-all duration-200 group">
                <div class="p-3 bg-primary-50 dark:bg-primary-950/20 rounded-xl text-primary-600 dark:text-primary-400 group-hover:scale-105 transition-transform duration-200">
                    <x-filament::icon icon="heroicon-o-academic-cap" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">Kelola Siswa</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Data & kelas siswa</p>
                </div>
            </a>

            <!-- Laporan Presensi -->
            <a href="/admin/attendance-report" class="flex items-center gap-4 p-5 bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:border-rose-500 dark:hover:border-rose-500 hover:shadow-md transition-all duration-200 group">
                <div class="p-3 bg-rose-50 dark:bg-rose-950/20 rounded-xl text-rose-600 dark:text-rose-400 group-hover:scale-105 transition-transform duration-200">
                    <x-filament::icon icon="heroicon-o-document-chart-bar" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">Laporan Presensi</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rekap harian & bulanan</p>
                </div>
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
