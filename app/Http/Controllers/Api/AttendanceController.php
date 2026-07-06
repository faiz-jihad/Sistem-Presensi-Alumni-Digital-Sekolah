<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Attendance\CloseSessionRequest;
use App\Http\Requests\Attendance\GenerateQrRequest;
use App\Http\Requests\Attendance\ManualAttendanceRequest;
use App\Http\Requests\Attendance\OpenSessionRequest;
use App\Http\Requests\Attendance\ScanQrRequest;
use App\Http\Resources\AttendanceRecordResource;
use App\Http\Resources\AttendanceSessionResource;
use App\Http\Resources\QrTokenResource;
use App\Models\PresensiSession;
use App\Models\Student;
use App\Models\Teacher;
use App\Policies\AttendanceSessionPolicy;
use App\Services\PresensiSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttendanceController extends BaseController
{
    public function __construct(
        private readonly PresensiSessionService $sessionService
    ) {}

    /* ───────────────────────────────────────────────
     |  GET /api/v1/teacher/today
     |  Jadwal hari ini untuk guru yang login
     * ─────────────────────────────────────────────── */
    public function today(Request $request): JsonResponse
    {
        $teacher = Teacher::where('user_id', $request->user()->id)->first();

        if (!$teacher) {
            return $this->error('Data guru tidak ditemukan untuk akun ini.', 404);
        }

        try {
            $dayParam = $request->query('day');
            $schedules = $this->sessionService->getTodaySchedulesForTeacher($teacher, $dayParam);

            // Tentukan hari yang berhasil di-load untuk info client
            $today = now();
            $resolvedDay = \App\Enums\DayOfWeek::fromCarbon($today);
            if ($dayParam) {
                $resolvedDay = \App\Enums\DayOfWeek::tryFrom($dayParam) ?? $resolvedDay;
            } elseif (in_array($resolvedDay, [\App\Enums\DayOfWeek::Saturday, \App\Enums\DayOfWeek::Sunday], true)) {
                $resolvedDay = \App\Enums\DayOfWeek::Monday;
            }

            return $this->success([
                'date'         => now()->translatedFormat('l, d F Y'),
                'resolved_day' => $resolvedDay->value,
                'resolved_day_label' => $resolvedDay->label(),
                'teacher'      => [
                    'id'   => $teacher->id,
                    'name' => $teacher->name,
                ],
                'schedules'    => $schedules,
                'total'        => $schedules->count(),
            ], 'Jadwal hari ini berhasil dimuat');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /* ───────────────────────────────────────────────
     |  POST /api/v1/attendance/open
     |  Guru membuka kelas
     * ─────────────────────────────────────────────── */
    public function open(OpenSessionRequest $request): JsonResponse
    {
        try {
            $session = $this->sessionService->openBySchedule(
                $request->validated('schedule_id'),
                $request->user()->id
            );

            $session->load(['schedule.class', 'schedule.subject', 'schedule.classHour', 'teacher', 'openedBy']);

            return $this->success(
                new AttendanceSessionResource($session),
                'Kelas berhasil dibuka',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /* ───────────────────────────────────────────────
     |  POST /api/v1/attendance/manual
     |  Input presensi manual oleh guru
     * ─────────────────────────────────────────────── */
    public function manual(ManualAttendanceRequest $request): JsonResponse
    {
        $session = PresensiSession::with('schedule')->findOrFail(
            $request->validated('session_id')
        );

        // Policy check
        if (!Gate::forUser($request->user())->allows('manualAttendance', $session)) {
            return $this->forbidden('Anda tidak berhak melakukan presensi pada sesi ini.');
        }

        try {
            $result = $this->sessionService->saveManualAttendance(
                $session,
                $request->validated('attendances')
            );

            return $this->success($result, 'Presensi manual berhasil disimpan');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /* ───────────────────────────────────────────────
     |  POST /api/v1/attendance/generate-qr
     |  Generate QR token (5 menit)
     * ─────────────────────────────────────────────── */
    public function generateQr(GenerateQrRequest $request): JsonResponse
    {
        $session = PresensiSession::with('schedule')->findOrFail(
            $request->validated('session_id')
        );

        // Policy check
        if (!Gate::forUser($request->user())->allows('generateQr', $session)) {
            return $this->forbidden('Anda tidak berhak generate QR untuk sesi ini.');
        }

        try {
            $qrToken = $this->sessionService->generateQrToken($session);

            return $this->success(
                new QrTokenResource($qrToken),
                'QR Token berhasil dibuat (berlaku 5 menit)',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /* ───────────────────────────────────────────────
     |  POST /api/v1/attendance/scan
     |  Siswa scan QR → presensi otomatis
     * ─────────────────────────────────────────────── */
    public function scan(ScanQrRequest $request): JsonResponse
    {
        $user = $request->user();

        // Cari data siswa yang terhubung ke akun ini
        $student = Student::where('parent_user_id', $user->id)
            ->orWhere('nis', $user->email)
            ->orWhere('name', $user->name)
            ->first();

        if (!$student) {
            return $this->error('Akun Anda tidak terhubung dengan data siswa.', 404);
        }

        try {
            $attendance = $this->sessionService->scanQrToken(
                $request->validated('token'),
                $student
            );

            $attendance->load('student', 'presensiSession');

            return $this->success(
                new AttendanceRecordResource($attendance),
                'Presensi QR berhasil dicatat'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /* ───────────────────────────────────────────────
     |  POST /api/v1/attendance/close
     |  Guru menutup sesi presensi
     * ─────────────────────────────────────────────── */
    public function close(CloseSessionRequest $request): JsonResponse
    {
        $session = PresensiSession::with('schedule')->findOrFail(
            $request->validated('session_id')
        );

        // Policy check
        if (!Gate::forUser($request->user())->allows('close', $session)) {
            return $this->forbidden('Anda tidak berhak menutup sesi presensi ini.');
        }

        try {
            $session = $this->sessionService->closeSession($session, $request->user()->id);
            $session->load(['schedule.class', 'schedule.subject', 'teacher', 'closedBy', 'studentAttendances.student']);

            return $this->success(
                new AttendanceSessionResource($session),
                'Sesi presensi berhasil ditutup'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /* ───────────────────────────────────────────────
     |  GET /api/v1/attendance/session/{id}
     |  Detail sesi presensi
     * ─────────────────────────────────────────────── */
    public function session(Request $request, int $id): JsonResponse
    {
        $session = PresensiSession::with([
            'schedule.class',
            'schedule.subject',
            'schedule.classHour',
            'teacher',
            'openedBy',
            'closedBy',
            'studentAttendances.student',
        ])->findOrFail($id);

        // Policy check
        if (!Gate::forUser($request->user())->allows('view', $session)) {
            return $this->forbidden('Anda tidak berhak mengakses sesi ini.');
        }

        return $this->success(
            new AttendanceSessionResource($session),
            'Detail sesi berhasil dimuat'
        );
    }

    /* ───────────────────────────────────────────────
     |  GET /api/v1/attendance/history
     |  Riwayat presensi guru
     * ─────────────────────────────────────────────── */
    public function history(Request $request): JsonResponse
    {
        $teacher = Teacher::where('user_id', $request->user()->id)->first();

        if (!$teacher) {
            return $this->error('Data guru tidak ditemukan.', 404);
        }

        try {
            $sessions = $this->sessionService->getHistory($teacher, $request->only(['date', 'status']));

            return $this->success([
                'total'    => $sessions->count(),
                'sessions' => AttendanceSessionResource::collection($sessions),
            ], 'Riwayat presensi berhasil dimuat');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /* ─── Helper ─────────────────────────────── */

    private function exceptionCode(\Exception $e): int
    {
        return in_array($e->getCode(), [400, 403, 404, 422], true)
            ? $e->getCode()
            : 400;
    }
}
