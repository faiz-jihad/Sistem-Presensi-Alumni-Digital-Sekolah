<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PrayerAttendanceResource;
use App\Models\PrayerAttendance;
use App\Services\PrayerAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class PrayerAttendanceController extends BaseController
{
    public function __construct(private readonly PrayerAttendanceService $service) {}

    public function today(Request $request): JsonResponse
    {
        try {
            return $this->success(
                $this->service->todaySummary($request->user()),
                'Jadwal dan presensi sholat hari ini berhasil dimuat.'
            );
        } catch (RuntimeException $exception) {
            return $this->error($exception->getMessage(), $this->runtimeStatus($exception));
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prayer_type' => ['required', Rule::in(PrayerAttendanceService::PRAYER_TYPES)],
        ]);

        try {
            $attendance = $this->service->submit($request->user(), $validated['prayer_type']);

            return $this->success(
                new PrayerAttendanceResource($attendance),
                'Presensi sholat berhasil dikirim dan menunggu verifikasi guru.',
                201
            );
        } catch (RuntimeException $exception) {
            return $this->error($exception->getMessage(), $this->runtimeStatus($exception));
        }
    }

    public function pending(Request $request): JsonResponse
    {
        try {
            return $this->success(
                PrayerAttendanceResource::collection(
                    $this->service->pendingForTeacher($request->user())
                ),
                'Daftar presensi yang menunggu verifikasi berhasil dimuat.'
            );
        } catch (RuntimeException $exception) {
            return $this->error($exception->getMessage(), $this->runtimeStatus($exception));
        }
    }

    public function verify(Request $request, PrayerAttendance $prayerAttendance): JsonResponse
    {
        $validated = $request->validate([
            'approved' => ['required', 'boolean'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $attendance = $this->service->verify(
                $request->user(),
                $prayerAttendance,
                (bool) $validated['approved'],
                $validated['note'] ?? null
            );

            return $this->success(
                new PrayerAttendanceResource($attendance),
                $validated['approved']
                    ? 'Presensi sholat berhasil disetujui.'
                    : 'Presensi sholat berhasil ditolak.'
            );
        } catch (RuntimeException $exception) {
            return $this->error($exception->getMessage(), $this->runtimeStatus($exception));
        }
    }

    public function verifyAll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'attendance_ids' => ['required', 'array', 'min:1'],
            'attendance_ids.*' => ['integer', 'distinct', 'exists:prayer_attendances,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $items = $this->service->verifyAll(
                $request->user(),
                $validated['attendance_ids'],
                $validated['note'] ?? null
            );

            return $this->success([
                'count' => $items->count(),
                'items' => PrayerAttendanceResource::collection($items),
            ], 'Semua presensi sholat berhasil disetujui.');
        } catch (RuntimeException $exception) {
            return $this->error($exception->getMessage(), $this->runtimeStatus($exception));
        }
    }

    public function history(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'prayer_type' => ['nullable', Rule::in(PrayerAttendanceService::PRAYER_TYPES)],
            'status' => [
                'nullable',
                Rule::in(['pending', 'approved', 'rejected', 'late', 'missed', 'expired', 'cancelled']),
            ],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $items = $this->service->history($request->user(), $filters);

            return $this->success(
                PrayerAttendanceResource::collection($items),
                'Riwayat presensi sholat berhasil dimuat.'
            );
        } catch (RuntimeException $exception) {
            return $this->error($exception->getMessage(), $this->runtimeStatus($exception));
        }
    }

    public function show(Request $request, PrayerAttendance $prayerAttendance): JsonResponse
    {
        try {
            return $this->success(
                new PrayerAttendanceResource(
                    $this->service->detail($request->user(), $prayerAttendance)
                ),
                'Detail presensi sholat berhasil dimuat.'
            );
        } catch (RuntimeException $exception) {
            return $this->error($exception->getMessage(), $this->runtimeStatus($exception));
        }
    }

    private function runtimeStatus(RuntimeException $exception): int
    {
        $message = strtolower($exception->getMessage());
        if (str_contains($message, 'tidak memiliki akses') ||
            str_contains($message, 'tidak dapat mengakses') ||
            str_contains($message, 'bukan akun')) {
            return 403;
        }

        if (str_contains($message, 'belum terhubung')) {
            return 404;
        }

        return 422;
    }
}
