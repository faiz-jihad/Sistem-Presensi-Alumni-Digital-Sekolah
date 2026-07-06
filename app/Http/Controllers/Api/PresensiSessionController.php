<?php

namespace App\Http\Controllers\Api;

use App\Models\PresensiSession;
use App\Models\Teacher;
use App\Services\PresensiSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PresensiSessionController extends BaseController
{
    public function __construct(
        private readonly PresensiSessionService $presensiSessionService
    ) {}

    /**
     * Tampilkan list sesi presensi
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $sessions = $this->presensiSessionService->listForUser($request->user(), $request->only([
                'date',
                'status',
                'schedule_id',
            ]));

            return $this->success($sessions, 'List sesi presensi berhasil dimuat');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /**
     * Detail sesi presensi
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $session = PresensiSession::with([
            'schedule.class',
            'schedule.subject',
            'schedule.classHour',
            'teacher',
            'studentAttendances.student',
            'studentAttendances.class',
        ])->findOrFail($id);

        try {
            $this->presensiSessionService->ensureUserCanManageSession($request->user(), $session);

            return $this->success($session, 'Detail sesi presensi berhasil dimuat');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /**
     * Buat sesi presensi berdasarkan jadwal
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher && !in_array($user->role, ['admin', 'super_admin'], true)) {
            return $this->forbidden('Hanya guru atau admin yang dapat membuat sesi presensi.');
        }

        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $session = $this->presensiSessionService->create($validator->validated(), $teacher?->id);

            return $this->success($session, 'Sesi presensi berhasil dibuat', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /**
     * Update sesi presensi
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $session = PresensiSession::findOrFail($id);

        try {
            $this->presensiSessionService->ensureUserCanManageSession($request->user(), $session);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }

        $validator = Validator::make($request->all(), $this->rules(requiredSchedule: false));

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $session = $this->presensiSessionService->update($session, $validator->validated());

            return $this->success($session, 'Sesi presensi berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /**
     * Buka sesi presensi
     */
    public function open(Request $request, int $id): JsonResponse
    {
        $session = PresensiSession::findOrFail($id);

        try {
            $this->presensiSessionService->ensureUserCanManageSession($request->user(), $session);
            $session = $this->presensiSessionService->open($session);

            return $this->success($session, 'Sesi presensi berhasil dibuka');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /**
     * Tutup sesi presensi
     */
    public function close(Request $request, int $id): JsonResponse
    {
        $session = PresensiSession::findOrFail($id);

        try {
            $this->presensiSessionService->ensureUserCanManageSession($request->user(), $session);
            $session = $this->presensiSessionService->close($session, $request->user()->id);

            return $this->success($session, 'Sesi presensi berhasil ditutup');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /**
     * Hapus sesi presensi
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $session = PresensiSession::findOrFail($id);

        try {
            $this->presensiSessionService->ensureUserCanManageSession($request->user(), $session);
            $this->presensiSessionService->delete($session);

            return $this->success(null, 'Sesi presensi berhasil dihapus');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    /**
     * Ambil QR Code string/token untuk sesi
     */
    public function showQr(Request $request, int $id): JsonResponse
    {
        $session = PresensiSession::with(['schedule.classHour'])->findOrFail($id);

        try {
            $this->presensiSessionService->ensureUserCanManageSession($request->user(), $session);

            if (!$this->presensiSessionService->canShowQr($session)) {
                return $this->error('QR Code hanya tersedia untuk sesi hari ini yang sedang dibuka dan belum berakhir.', 400);
            }

            $qrCodeString = 'session_' . $session->id;

            return $this->success([
                'session_id' => $session->id,
                'qr_code' => $qrCodeString,
                'status' => $session->status,
                'date' => $session->date,
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
            ], 'Token QR Code berhasil digenerate');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $this->exceptionCode($e));
        }
    }

    private function rules(bool $requiredSchedule = true): array
    {
        return [
            'schedule_id' => [$requiredSchedule ? 'required' : 'sometimes', 'exists:schedules,id'],
            'teacher_id' => ['sometimes', 'nullable', 'exists:teachers,id'],
            'date' => ['sometimes', 'nullable', 'date'],
            'start_time' => ['sometimes', 'nullable', 'regex:/^\\d{2}:\\d{2}(:\\d{2})?$/'],
            'end_time' => ['sometimes', 'nullable', 'regex:/^\\d{2}:\\d{2}(:\\d{2})?$/'],
            'status' => ['sometimes', 'in:scheduled,open,closed,cancelled'],
            'material_topic' => ['sometimes', 'nullable', 'string'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }

    private function exceptionCode(\Exception $e): int
    {
        return in_array($e->getCode(), [400, 403, 404, 422], true) ? $e->getCode() : 400;
    }
}