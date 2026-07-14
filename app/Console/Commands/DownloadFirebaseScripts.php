<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DownloadFirebaseScripts extends Command
{
    protected $signature = 'app:download-firebase-scripts';

    protected $description = 'Download Firebase browser scripts to public/js.';

    public function handle(): int
    {
        $jsDir = public_path('js');

        if (! is_dir($jsDir) && ! mkdir($jsDir, 0755, true) && ! is_dir($jsDir)) {
            $this->error("Tidak dapat membuat direktori: {$jsDir}");

            return self::FAILURE;
        }

        $firebaseScripts = [
            'core-app.js' => 'https://cdn.jsdelivr.net/npm/firebase@10.8.0/firebase-app-compat.js',
            'user-session.js' => 'https://cdn.jsdelivr.net/npm/firebase@10.8.0/firebase-auth-compat.js',
            'core-msg.js' => 'https://cdn.jsdelivr.net/npm/firebase@10.8.0/firebase-messaging-compat.js',
        ];

        foreach ($firebaseScripts as $name => $url) {
            $path = $jsDir . DIRECTORY_SEPARATOR . $name;

            if (file_exists($path)) {
                $this->line("Lewati {$name}, file sudah ada.");
                continue;
            }

            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                ],
            ]);

            $content = @file_get_contents($url, false, $context);

            if (! $content) {
                $this->warn("Gagal mengunduh {$name}.");
                continue;
            }

            file_put_contents($path, $content);
            $this->info("Berhasil mengunduh {$name}.");
        }

        return self::SUCCESS;
    }
}
