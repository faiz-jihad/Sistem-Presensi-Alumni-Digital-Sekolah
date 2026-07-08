<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 py-2">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Uji Coba Notifikasi Desktop
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Klik tombol di sebelah kanan untuk meminta izin dan menguji notifikasi native sistem operasi (desktop/laptop) Anda.
                </p>
            </div>
            <div>
                <x-filament::button
                    type="button"
                    color="info"
                    icon="heroicon-o-bell"
                    onclick="triggerTestDesktopNotification()"
                >
                    Uji Coba Notifikasi
                </x-filament::button>
            </div>
        </div>

        <script>
            function triggerTestDesktopNotification() {
                if (typeof Notification === 'undefined') {
                    alert('Browser Anda tidak mendukung notifikasi desktop.');
                    return;
                }

                if (Notification.permission === 'default') {
                    Notification.requestPermission().then((permission) => {
                        if (permission === 'granted') {
                            showNativeNotification();
                        } else {
                            alert('Izin notifikasi desktop ditolak. Silakan aktifkan izin notifikasi pada pengaturan browser Anda.');
                        }
                    });
                } else if (Notification.permission === 'granted') {
                    showNativeNotification();
                } else {
                    alert('Izin notifikasi desktop diblokir oleh browser. Silakan klik ikon gembok di sebelah URL untuk mengaktifkannya kembali.');
                }
            }

            function showNativeNotification() {
                try {
                    new Notification('Uji Coba Notifikasi SIMPAD', {
                        body: 'Hebat! Notifikasi desktop native berhasil muncul di layar laptop Anda.',
                        icon: '/favicon.ico'
                    });
                } catch (e) {
                    console.error('Error triggering notification:', e);
                }
            }
        </script>
    </x-filament::section>
</x-filament-widgets::widget>
