<?php

use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Extracting\Strategies;

use function Knuckles\Scribe\Config\configureStrategy;
use function Knuckles\Scribe\Config\removeStrategies;

// Only the most common configs are shown. See the https://scribe.knuckles.wtf/laravel/reference/config for all.
// config/scribe.php
return [
    'title' => 'API Sistem Presensi & Alumni Digital Sekolah',
    'description' => 'Dokumentasi API untuk Sistem Presensi dan Alumni Digital Sekolah',
    'base_url' => env('APP_URL'),
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains' => ['*'],
                'versions' => ['v1'],
            ],
            'include' => [
                // Tambahkan route yang mau didokumentasikan
                'api/v1/auth/*',
                'api/v1/roles',
                'api/v1/schools',
            ],
            'exclude' => [
                // Route yang tidak mau didokumentasikan
            ],
            'apply' => [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer {token}',
                ],
            ],
        ],
    ],
    'auth' => [
        'enabled' => true,
        'default' => true,
        'in' => 'bearer',
        'name' => 'Authorization',
        'use_headers' => true,
    ],
];