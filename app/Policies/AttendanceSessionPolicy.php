<?php

namespace App\Policies;

use App\Enums\SessionStatus;
use App\Models\PresensiSession;
use App\Models\Teacher;
use App\Models\User;

class AttendanceSessionPolicy
{
    /**
     * Admin/super_admin dapat melakukan apa saja.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolActive()) {
            return false;
        }

        if (in_array($user->role, ['admin', 'super_admin'], true)) {
            return true;
        }
        return null;
    }

    /**
     * Guru hanya bisa melihat sesi miliknya.
     */
    public function view(User $user, PresensiSession $session): bool
    {
        $teacher = Teacher::where('user_id', $user->id)->first();
        return $teacher && (int) $session->teacher_id === (int) $teacher->id;
    }

    /**
     * Guru dapat membuka kelas jika:
     * - Jam sekarang >= jam mulai, ATAU
     * - allow_early_open aktif pada schedule
     */
    public function open(User $user, PresensiSession $session): bool
    {
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return false;
        }

        // Pastikan ini jadwal guru tersebut
        if ((int) $session->schedule?->teacher_id !== (int) $teacher->id) {
            return false;
        }

        // Hanya bisa buka jika status scheduled
        if ($session->status !== SessionStatus::Scheduled) {
            return false;
        }

        $now       = now();
        $startTime = $session->schedule?->classHour?->start_time;

        if ($startTime === null) {
            return true; // Tidak ada jam, izinkan
        }

        $startCarbon = \Carbon\Carbon::parse(
            $session->date . ' ' . $startTime
        );

        // Izin jika sudah masuk jam mulai
        if ($now->greaterThanOrEqualTo($startCarbon)) {
            return true;
        }

        // Atau jika allow_early_open aktif
        return (bool) $session->schedule?->allow_early_open;
    }

    /**
     * Guru hanya bisa menutup sesi miliknya sendiri yang sedang OPEN.
     */
    public function close(User $user, PresensiSession $session): bool
    {
        $teacher = Teacher::where('user_id', $user->id)->first();

        return $teacher
            && (int) $session->teacher_id === (int) $teacher->id
            && $session->status === SessionStatus::Open;
    }

    /**
     * Guru dapat melakukan presensi manual jika sesi OPEN dan miliknya.
     */
    public function manualAttendance(User $user, PresensiSession $session): bool
    {
        $teacher = Teacher::where('user_id', $user->id)->first();

        return $teacher
            && (int) $session->teacher_id === (int) $teacher->id
            && $session->status === SessionStatus::Open;
    }

    /**
     * Guru dapat generate QR jika sesi OPEN dan miliknya.
     */
    public function generateQr(User $user, PresensiSession $session): bool
    {
        return $this->manualAttendance($user, $session);
    }
}
