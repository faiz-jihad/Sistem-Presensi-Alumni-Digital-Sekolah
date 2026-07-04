<?php

namespace App\Http\Controllers\Api;

use App\Models\PresensiSession;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PresensiSessionController extends BaseController
{
    /**
     * Tampilkan list sesi presensi
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = PresensiSession::with(['schedule.class', 'schedule.subject', 'teacher']);

        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher) {
                return $this->error("Data guru tidak ditemukan untuk akun ini.", 404);
            }
            $query->where('teacher_id', $teacher->id);
        } elseif ($user->role === 'admin' || $user->role === 'super_admin') {
            if ($user->school_id) {
                $query->where('school_id', $user->school_id);
            }
        } else {
            return $this->forbidden();
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        return $this->success($query->latest()->get(), "List sesi presensi berhasil dimuat");
    }

    /**
     * Buka sesi presensi berdasarkan Schedule ID
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        
        if (!$teacher && !in_array($user->role, ['admin', 'super_admin'])) {
            return $this->forbidden("Hanya guru atau admin yang dapat membuat sesi presensi.");
        }

        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'date' => 'nullable|date',
            'material_topic' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $schedule = Schedule::findOrFail($request->schedule_id);
        $date = $request->date ?? Carbon::today()->toDateString();
        $teacherId = $teacher ? $teacher->id : $schedule->teacher_id;

        // Cek jika sesi sudah ada untuk jadwal dan tanggal tersebut
        $session = PresensiSession::where('schedule_id', $schedule->id)
            ->where('date', $date)
            ->first();

        if ($session) {
            // Jika sudah ada, ubah status ke open
            $session->update([
                'status' => 'open',
                'material_topic' => $request->material_topic ?? $session->material_topic,
                'notes' => $request->notes ?? $session->notes,
            ]);
            return $this->success($session, "Sesi presensi berhasil dibuka kembali");
        }

        $session = PresensiSession::create([
            'school_id' => $schedule->school_id,
            'schedule_id' => $schedule->id,
            'teacher_id' => $teacherId,
            'date' => $date,
            'start_time' => Carbon::now()->toTimeString(),
            'status' => 'open',
            'material_topic' => $request->material_topic,
            'notes' => $request->notes,
        ]);

        return $this->success($session, "Sesi presensi berhasil dibuka", 201);
    }

    /**
     * Tutup sesi presensi
     */
    public function close(Request $request, int $id): JsonResponse
    {
        $session = PresensiSession::findOrFail($id);
        $user = $request->user();

        // Validasi hak akses tutup
        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher || $session->teacher_id !== $teacher->id) {
                return $this->forbidden("Anda tidak berhak menutup sesi presensi ini.");
            }
        }

        $session->update([
            'status' => 'closed',
            'closed_by' => $user->id,
            'closed_at' => Carbon::now(),
            'end_time' => Carbon::now()->toTimeString(),
        ]);

        return $this->success($session, "Sesi presensi berhasil ditutup");
    }

    /**
     * Ambil QR Code string/token untuk sesi
     */
    public function showQr(Request $request, int $id): JsonResponse
    {
        $session = PresensiSession::findOrFail($id);
        
        // QR Code string format: session_{id}
        $qrCodeString = "session_" . $session->id;

        return $this->success([
            'session_id' => $session->id,
            'qr_code' => $qrCodeString,
            'status' => $session->status,
        ], "Token QR Code berhasil digenerate");
    }
}
