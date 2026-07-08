<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $orphans = DB::table('student_attendances')
            ->whereNull('presensi_session_id')
            ->whereNotNull('class_id')
            ->get(['id', 'class_id', 'date']);

        foreach ($orphans as $orphan) {
            $sessionId = DB::table('presensi_sessions')
                ->leftJoin('schedules', 'schedules.id', '=', 'presensi_sessions.schedule_id')
                ->where('presensi_sessions.date', $orphan->date)
                ->where(function ($query) use ($orphan) {
                    $query->where('presensi_sessions.class_id', $orphan->class_id)
                        ->orWhere('schedules.class_id', $orphan->class_id);
                })
                ->orderByDesc('presensi_sessions.id')
                ->value('presensi_sessions.id');

            if ($sessionId) {
                DB::table('student_attendances')
                    ->where('id', $orphan->id)
                    ->update(['presensi_session_id' => $sessionId]);
            }
        }

        $duplicates = DB::table('student_attendances')
            ->select('presensi_session_id', 'student_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('presensi_session_id')
            ->groupBy('presensi_session_id', 'student_id')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = DB::table('student_attendances')
                ->where('presensi_session_id', $duplicate->presensi_session_id)
                ->where('student_id', $duplicate->student_id)
                ->orderByRaw("CASE WHEN status IN ('present', 'late') THEN 0 ELSE 1 END")
                ->orderByRaw('CASE WHEN check_in_time IS NULL THEN 1 ELSE 0 END')
                ->orderBy('check_in_time')
                ->orderByRaw('CASE WHEN scanned_at IS NULL THEN 1 ELSE 0 END')
                ->orderBy('scanned_at')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $idsToDelete = array_slice($ids, 1);
            if (!empty($idsToDelete)) {
                DB::table('student_attendances')
                    ->whereIn('id', $idsToDelete)
                    ->delete();
            }
        }
    }

    public function down(): void
    {
        //
    }
};
