<?php

namespace App\Services;

use App\Models\School;
use Carbon\CarbonInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PrayerScheduleService
{
    /**
     * @return array{province:string, city:string, date:string, times:array<string, string>}
     */
    public function forDate(School $school, CarbonInterface $date): array
    {
        $province = $school->prayer_province ?: config('services.equran_prayer.province');
        $city = $school->prayer_city ?: config('services.equran_prayer.city');
        $monthly = $this->monthly($province, $city, $date->month, $date->year);
        $dateString = $date->toDateString();

        $schedule = collect($monthly['jadwal'] ?? [])->first(
            fn (array $item): bool => ($item['tanggal_lengkap'] ?? null) === $dateString
        );

        if (! is_array($schedule)) {
            throw new RuntimeException('Jadwal sholat untuk tanggal ini belum tersedia.');
        }

        return [
            'province' => $province,
            'city' => $city,
            'date' => $dateString,
            'times' => [
                'subuh' => $this->readTime($schedule, 'subuh'),
                'dzuhur' => $this->readTime($schedule, 'dzuhur'),
                'ashar' => $this->readTime($schedule, 'ashar'),
                'maghrib' => $this->readTime($schedule, 'maghrib'),
                'isya' => $this->readTime($schedule, 'isya'),
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function monthly(string $province, string $city, int $month, int $year): array
    {
        $cacheKey = 'equran-prayer.'.sha1("{$province}|{$city}|{$month}|{$year}");
        $backupKey = "{$cacheKey}.backup";

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(12)
                ->retry(2, 250)
                ->post(config('services.equran_prayer.base_url'), [
                    'provinsi' => $province,
                    'kabkota' => $city,
                    'bulan' => $month,
                    'tahun' => $year,
                ]);

            $response->throw();
            $payload = $response->json();
            $data = is_array($payload) ? ($payload['data'] ?? null) : null;

            if (! is_array($data) || ! is_array($data['jadwal'] ?? null)) {
                throw new RuntimeException('Format jadwal sholat dari penyedia tidak valid.');
            }

            Cache::put(
                $cacheKey,
                $data,
                now()->addHours((int) config('services.equran_prayer.cache_hours', 24))
            );
            Cache::put($backupKey, $data, now()->addDays(14));

            return $data;
        } catch (ConnectionException|RuntimeException $exception) {
            if ($backup = Cache::get($backupKey)) {
                return $backup;
            }

            throw new RuntimeException(
                'Jadwal sholat sedang tidak dapat dimuat. Silakan coba lagi.',
                previous: $exception
            );
        } catch (\Throwable $exception) {
            if ($backup = Cache::get($backupKey)) {
                return $backup;
            }

            throw new RuntimeException(
                'Jadwal sholat sedang tidak dapat dimuat. Silakan coba lagi.',
                previous: $exception
            );
        }
    }

    /** @param array<string, mixed> $schedule */
    private function readTime(array $schedule, string $key): string
    {
        $value = $schedule[$key] ?? null;
        if (! is_string($value) || ! preg_match('/^\d{2}:\d{2}$/', $value)) {
            throw new RuntimeException("Waktu {$key} pada jadwal sholat tidak valid.");
        }

        return $value;
    }
}
