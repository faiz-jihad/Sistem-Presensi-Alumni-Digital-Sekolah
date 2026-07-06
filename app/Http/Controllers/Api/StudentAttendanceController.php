<?php

namespace App\Http\Controllers\Api;

use App\Services\AttendanceService;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentAttendance;
use App\Http\Resources\StudentAttendanceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StudentAttendanceController extends BaseController
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {}

    /**
     * Tampilkan data presensi dengan filter
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = StudentAttendance::with(['student.class', 'teacher', 'presensiSession']);

        // Filter berdasarkan role
        if ($user->role === 'student') {
            $student = Student::where('parent_user_id', $user->id)
                ->orWhere('name', $user->name)
                ->orWhere('nis', $user->email)
                ->first();
            if (!$student) {
                return $this->error("Data siswa tidak ditemukan untuk akun ini.", 404);
            }
            $query->where('student_id', $student->id);
        } elseif ($user->role === 'parent') {
            $studentIds = Student::where('parent_user_id', $user->id)->pluck('id');
            $query->whereIn('student_id', $studentIds);
        } elseif ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher) {
                return $this->error("Data guru tidak ditemukan untuk akun ini.", 404);
            }
            if ($user->school_id) {
                $query->where('school_id', $user->school_id);
            }
        } elseif (in_array($user->role, ['admin', 'super_admin'])) {
            if ($user->school_id) {
                $query->where('school_id', $user->school_id);
            }
        } else {
            return $this->forbidden();
        }

        // Apply filters
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->has('date')) {
            $query->where('date', $request->date);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        return $this->success(
            StudentAttendanceResource::collection($attendances),
            "Data kehadiran berhasil dimuat"
        );
    }

    /**
     * Guru melakukan input presensi manual kelas
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher && !in_array($user->role, ['admin', 'super_admin'])) {
            return $this->forbidden("Hanya guru atau admin yang dapat menginput presensi.");
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'presensi_session_id' => 'nullable|exists:presensi_sessions,id',
            'attendances' => 'required|array|min:1',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,late,permission,sick,absent',
            'attendances.*.note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $teacherId = $teacher ? $teacher->id : null;

        try {
            $result = $this->attendanceService->recordClassAttendance(
                $teacherId,
                $request->class_id,
                $request->date,
                $request->attendances,
                $request->presensi_session_id
            );

            return $this->success($result, "Presensi kelas berhasil disimpan.");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Guru melakukan input presensi massal
     */
    public function bulkStore(Request $request): JsonResponse
    {
        return $this->store($request);
    }

    /**
     * Siswa melakukan presensi mandiri via scan QR Code
     */
    public function presensiMandiri(Request $request): JsonResponse
    {
        $user = $request->user();

        // Cari siswa yang terhubung dengan akun ini
        $student = Student::where('parent_user_id', $user->id)
            ->orWhere('name', $user->name)
            ->first();

        if (!$student) {
            $student = Student::where('parent_user_id', $user->id)->first();
        }

        if (!$student) {
            $student = Student::where('nis', $user->email)->first();
        }

        if (!$student) {
            return $this->error("Akun Anda tidak terhubung dengan data siswa mana pun.", 404);
        }

        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $attendance = $this->attendanceService->recordSelfPresence($student->id, $request->qr_code);
            return $this->success(
                new StudentAttendanceResource($attendance),
                "Presensi mandiri berhasil direkam."
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Siswa mengajukan izin/sakit
     */
    public function storeIzin(Request $request): JsonResponse
    {
        $user = $request->user();

        $student = Student::where('name', $user->name)
            ->orWhere('parent_user_id', $user->id)
            ->first();

        if (!$student) {
            return $this->error("Akun Anda tidak terhubung dengan data siswa mana pun.", 404);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'status' => 'required|in:permission,sick',
            'note' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $attendance = $this->attendanceService->applyLeave($student->id, $request->all());

            $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
            if ($admins->isNotEmpty()) {
                $statusLabel = $request->status === 'sick' ? 'Sakit' : 'Izin';
                \Filament\Notifications\Notification::make()
                    ->title("Pengajuan {$statusLabel} Siswa")
                    ->body("Siswa **{$student->name}** mengajukan **{$statusLabel}** untuk tanggal **" . \Carbon\Carbon::parse($request->date)->translatedFormat('d F Y') . "**. Catatan: {$request->note}")
                    ->warning()
                    ->sendToDatabase($admins);
            }

            return $this->success(
                new StudentAttendanceResource($attendance),
                "Pengajuan izin/sakit berhasil dikirim dan menunggu verifikasi."
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Admin/Wali Kelas memverifikasi pengajuan izin/sakit siswa
     */
    public function verifyIzin(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin', 'teacher'])) {
            return $this->forbidden("Anda tidak diizinkan melakukan verifikasi.");
        }

        $validator = Validator::make($request->all(), [
            'verification_status' => 'required|in:approved,rejected',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $attendance = $this->attendanceService->verifyLeave($id, $user->id, $request->verification_status);
            return $this->success(
                new StudentAttendanceResource($attendance),
                "Status pengajuan izin/sakit berhasil diperbarui."
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
