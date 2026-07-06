<?php

namespace App\Http\Controllers\Api;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Dashboard utama — data disesuaikan berdasarkan role user yang login.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return match ($user->role) {
            'super_admin'             => $this->superAdminDashboard($user),
            'admin'                   => $this->adminDashboard($user),
            'teacher'                 => $this->teacherDashboard($user),
            'student'                 => $this->studentDashboard($user),
            'alumni'                  => $this->alumniDashboard($user),
            'parent'                  => $this->parentDashboard($user),
            default                   => $this->success([
                'role' => $user->role,
                'message' => 'Dashboard belum tersedia untuk role ini.',
            ], 'Dashboard dimuat'),
        };
    }

    /**
     * Statistik admin — hanya admin & super_admin yang bisa akses.
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $query = fn($model) => $schoolId ? $model::where('school_id', $schoolId) : new $model;

        return $this->success([
            'total_students_by_status' => Student::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'total_classes_by_grade'   => StudentClass::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->selectRaw('grade, count(*) as total')
                ->groupBy('grade')
                ->orderBy('grade')
                ->pluck('total', 'grade'),
            'teachers_by_status'       => Teacher::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'recent_students'          => Student::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->with('class:id,name,grade')
                ->latest()
                ->take(5)
                ->get(['id', 'name', 'nis', 'class_id', 'status', 'created_at']),
        ], 'Statistik berhasil dimuat');
    }

    /* ─────────────────────────── private helpers ─────────────────────────── */

    private function superAdminDashboard(User $user): JsonResponse
    {
        return $this->success([
            'role'    => 'super_admin',
            'summary' => [
                'total_schools'        => School::count(),
                'total_users'          => User::count(),
                'total_students'       => Student::count(),
                'total_teachers'       => Teacher::count(),
                'total_classes'        => StudentClass::count(),
                'total_academic_years' => AcademicYear::count(),
            ],
            'schools_overview' => School::withCount([
                'users as total_teachers' => fn($q) => $q->where('role', 'teacher'),
            ])->latest()->take(5)->get(['id', 'name', 'npsn', 'level', 'status']),
            'recent_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'role', 'status', 'created_at']),
        ], 'Dashboard Super Admin berhasil dimuat');
    }

    private function adminDashboard(User $user): JsonResponse
    {
        $schoolId = $user->school_id;

        return $this->success([
            'role'    => 'admin',
            'school'  => School::find($schoolId, ['id', 'name', 'npsn', 'level', 'principal_name', 'status']),
            'summary' => [
                'total_students'        => Student::where('school_id', $schoolId)->count(),
                'total_active_students' => Student::where('school_id', $schoolId)->where('status', 'active')->count(),
                'total_teachers'        => Teacher::where('school_id', $schoolId)->count(),
                'total_classes'         => StudentClass::where('school_id', $schoolId)->count(),
                'total_active_classes'  => StudentClass::where('school_id', $schoolId)->where('status', 'active')->count(),
                'active_academic_year'  => AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first(['id', 'name', 'start_date', 'end_date']),
            ],
            'recent_students' => Student::where('school_id', $schoolId)
                ->with('class:id,name,grade')
                ->latest()
                ->take(5)
                ->get(['id', 'name', 'nis', 'class_id', 'status', 'created_at']),
            'recent_teachers' => Teacher::where('school_id', $schoolId)
                ->latest()
                ->take(5)
                ->get(['id', 'name', 'nip', 'status', 'created_at']),
        ], 'Dashboard Admin berhasil dimuat');
    }

    private function teacherDashboard(User $user): JsonResponse
    {
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (! $teacher) {
            return $this->success([
                'role'    => 'teacher',
                'message' => 'Data guru belum terhubung ke akun ini. Hubungi admin.',
            ], 'Dashboard Guru');
        }

        $classes = StudentClass::where('homeroom_teacher_id', $teacher->id)
            ->where('status', 'active')
            ->withCount('students')
            ->get(['id', 'name', 'grade', 'major', 'status']);

        // Ambil jadwal hari ini untuk guru
        $todayDay = strtolower(\Carbon\Carbon::now()->format('l'));
        $todayDate = \Carbon\Carbon::today()->toDateString();
        $now = \Carbon\Carbon::now();

        $schedules = \App\Models\Schedule::with(['class', 'subject', 'classHour'])
            ->where('teacher_id', $teacher->id)
            ->where('day', $todayDay)
            ->where('is_active', true)
            ->get();

        $todaySchedules = [];
        $activeSchedule = null;
        $activeSession = null;
        $buttonState = 'none'; // 'check_in', 'check_out', 'none'

        // Cari session aktif (open) hari ini
        $openSession = \App\Models\PresensiSession::where('teacher_id', $teacher->id)
            ->where('date', $todayDate)
            ->where('status', 'open')
            ->first();

        if ($openSession) {
            $activeSession = $openSession;
            $buttonState = 'check_out';
            $activeSchedule = \App\Models\Schedule::with(['class', 'subject', 'classHour'])->find($openSession->schedule_id);
        }

        foreach ($schedules as $schedule) {
            $session = \App\Models\PresensiSession::where('schedule_id', $schedule->id)
                ->where('date', $todayDate)
                ->first();

            $startTime = \Carbon\Carbon::parse($todayDate . ' ' . ($schedule->classHour?->start_time ?? '00:00:00'));
            $endTime = \Carbon\Carbon::parse($todayDate . ' ' . ($schedule->classHour?->end_time ?? '00:00:00'));
            $allowedStartTime = $startTime->copy()->subMinutes(15);
            $isWithinTeachingWindow = $now->greaterThanOrEqualTo($allowedStartTime) && $now->lessThanOrEqualTo($endTime);

            $status = 'upcoming';
            if ($session) {
                if ($session->status === 'open') {
                    $status = 'teaching';
                } elseif ($session->status === 'closed') {
                    $status = 'completed';
                } elseif ($session->status === 'cancelled') {
                    $status = 'cancelled';
                }
            } else {
                if ($now->greaterThan($endTime)) {
                    $status = 'missed';
                } elseif ($isWithinTeachingWindow) {
                    $status = 'eligible';
                    if (!$openSession) {
                        $activeSchedule = $schedule;
                        $buttonState = 'check_in';
                    }
                }
            }

            $todaySchedules[] = [
                'schedule_id' => $schedule->id,
                'class' => $schedule->class?->name,
                'subject' => $schedule->subject?->name,
                'start_time' => $schedule->classHour?->start_time,
                'end_time' => $schedule->classHour?->end_time,
                'room' => $schedule->room,
                'status' => $status,
                'session' => $session ? [
                    'id' => $session->id,
                    'status' => $session->status,
                    'check_in_time' => $session->start_time,
                    'check_out_time' => $session->end_time,
                ] : null,
            ];
        }

        return $this->success([
            'role'    => 'teacher',
            'profile' => [
                'id'                => $teacher->id,
                'name'              => $teacher->name,
                'nip'               => $teacher->nip,
                'employment_status' => $teacher->employment_status,
                'status'            => $teacher->status,
            ],
            'homeroom_classes' => $classes,
            'summary' => [
                'total_homeroom_classes' => $classes->count(),
                'total_students'         => $classes->sum('students_count'),
            ],
            'today_attendance' => [
                'date' => \Carbon\Carbon::now()->translatedFormat('l, d F Y'),
                'schedules' => $todaySchedules,
                'active_schedule' => $activeSchedule ? [
                    'id' => $activeSchedule->id,
                    'class' => $activeSchedule->class?->name,
                    'subject' => $activeSchedule->subject?->name,
                    'start_time' => $activeSchedule->classHour?->start_time,
                    'end_time' => $activeSchedule->classHour?->end_time,
                    'room' => $activeSchedule->room,
                ] : null,
                'active_session' => $activeSession ? [
                    'id' => $activeSession->id,
                    'status' => $activeSession->status,
                    'check_in_time' => $activeSession->start_time,
                    'is_late' => (bool) $activeSession->is_late,
                ] : null,
                'button_state' => $buttonState,
            ]
        ], 'Dashboard Guru berhasil dimuat');
    }

    private function studentDashboard(User $user): JsonResponse
    {
        $student = Student::where('parent_user_id', $user->id)
            ->orWhere(function ($q) use ($user) {
                // Fallback: cari student yang berelasi via email/name
                $q->where('name', $user->name);
            })
            ->with(['class:id,name,grade,major', 'school:id,name,npsn,level'])
            ->first();

        return $this->success([
            'role'    => 'student',
            'profile' => $student ? [
                'id'              => $student->id,
                'name'            => $student->name,
                'nis'             => $student->nis,
                'nisn'            => $student->nisn,
                'gender'          => $student->gender,
                'enrollment_year' => $student->enrollment_year,
                'status'          => $student->status,
                'school'          => $student->school,
                'class'           => $student->class,
            ] : null,
            'message' => $student ? null : 'Data siswa belum terhubung ke akun ini.',
        ], 'Dashboard Siswa berhasil dimuat');
    }

    private function alumniDashboard(User $user): JsonResponse
    {
        $school = School::find($user->school_id, ['id', 'name', 'npsn', 'level']);

        return $this->success([
            'role'    => 'alumni',
            'profile' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'phone'  => $user->phone,
                'school' => $school,
                'status' => $user->status,
            ],
            'message' => 'Selamat datang, Alumni!',
        ], 'Dashboard Alumni berhasil dimuat');
    }

    private function parentDashboard(User $user): JsonResponse
    {
        $children = Student::where('parent_user_id', $user->id)
            ->with(['class:id,name,grade,major', 'school:id,name'])
            ->get(['id', 'name', 'nis', 'class_id', 'school_id', 'status', 'gender']);

        return $this->success([
            'role'     => 'parent',
            'profile'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'children' => $children,
            'summary'  => [
                'total_children' => $children->count(),
            ],
        ], 'Dashboard Orang Tua berhasil dimuat');
    }
}
