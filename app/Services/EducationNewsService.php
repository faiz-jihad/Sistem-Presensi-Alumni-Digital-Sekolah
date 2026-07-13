<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EducationNewsService
{
    private const SOURCE_URL = 'https://www.kemendikdasmen.go.id';

    public function latest(): array
    {
        return Cache::remember('kemendikdasmen.latest-news', now()->addHours(3), function (): array {
            $response = Http::acceptJson()
                ->timeout(20)
                ->retry(2, 500)
                ->post(self::SOURCE_URL . '/berita-5', [
                    'jsonrpc' => '2.0',
                    'method' => 'call',
                    'params' => new \stdClass(),
                    'id' => 1,
                ]);

            $response->throw();

            $items = $response->json('result');
            if (! is_array($items)) {
                return [];
            }

            return collect($items)
                ->filter(fn ($item): bool => is_array($item) && isset($item['id'], $item['name']))
                ->take(5)
                ->map(function (array $item): array {
                    $id = (int) $item['id'];
                    $title = trim((string) $item['name']);

                    return [
                        'id' => $id,
                        'title' => $title,
                        'published_at' => (string) ($item['tgl_rilis'] ?? ''),
                        'category' => (string) ($item['klasifikasi'] ?? 'Berita'),
                        'image_url' => self::SOURCE_URL . '/mendikbud/image/' . $id,
                        'article_url' => self::SOURCE_URL . '/berita/' . $id . '-' . Str::slug($title),
                        'source' => 'Kemendikdasmen',
                    ];
                })
                ->values()
                ->all();
        });
    }
}
