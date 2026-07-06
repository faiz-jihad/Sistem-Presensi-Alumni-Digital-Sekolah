<?php

namespace App\Http\Controllers\Api;

use App\Models\PresensiSession;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TeacherAttendanceController extends BaseController
{
    /**
     * Get teacher's schedules and attendance sessions for today
     */
    public function today(Request $request): JsonResponse
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return $this->error('Data guru tidak ditemukan untuk akun ini.', 404);
        }

        $todayDay = strtolower(Carbon::now()->format('l'));
        $todayDate = Carbon::today()->toDateString();

        $schedules = Schedule::with(['class', 'subject', 'classHour', 'school'])
            ->where('teacher_id', $teacher->id)
            ->where('day', $todayDay)
            ->where('is_active', true)
            ->get();

        $scheduleData = $schedules->map(function ($schedule) use ($todayDate) {
            $session = PresensiSession::where('schedule_id', $schedule->id)
                ->where('date', $todayDate)
                ->first();

            $now = Carbon::now();
            $startTime = Carbon::parse($todayDate . ' ' . ($schedule->classHour?->start_time ?? '00:00:00'));
            $endTime = Carbon::parse($todayDate . ' ' . ($schedule->classHour?->end_time ?? '00:00:00'));
            
            $allowedStartTime = $startTime->copy()->subMinutes(15);
            $isWithinTeachingWindow = $now->greaterThanOrEqualTo($allowedStartTime) && $now->lessThanOrEqualTo($endTime);

            $status = 'upcoming';
            $sessionStatus = $session?->status;

            if ($session) {
                if ($sessionStatus === 'open') {
                    $status = 'teaching';
                } elseif ($sessionStatus === 'closed') {
                    $status = 'completed';
                } elseif ($sessionStatus === 'cancelled') {
                    $status = 'cancelled';
                } elseif ($sessionStatus === 'scheduled') {
                    if ($now->greaterThan($endTime)) {
                        $status = 'missed';
                    } elseif ($isWithinTeachingWindow) {
                        $status = 'eligible';
                    }
                }
            } else {
                if ($now->greaterThan($endTime)) {
                    $status = 'missed';
                } elseif ($isWithinTeachingWindow) {
                    $status = 'eligible';
                }
            }

            return [
                'schedule_id' => $schedule->id,
                'class' => [
                    'id' => $schedule->class?->id,
                    'name' => $schedule->class?->name,
                ],
                'subject' => [
                    'id' => $schedule->subject?->id,
                    'name' => $schedule->subject?->name,
                    'code' => $schedule->subject?->code,
                ],
                'class_hour' => [
                    'id' => $schedule->classHour?->id,
                    'code' => $schedule->classHour?->code,
                    'start_time' => $schedule->classHour?->start_time,
                    'end_time' => $schedule->classHour?->end_time,
                ],
                'room' => $schedule->room,
                'status' => $status,
                'is_within_window' => $isWithinTeachingWindow,
                'session' => $session ? [
                    'id' => $session->id,
                    'status' => $session->status,
                    'check_in_time' => $session->start_time,
                    'check_out_time' => $session->end_time,
                    'is_late' => (bool) $session->is_late,
                ] : null,
            ];
        });

        return $this->success([
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->name,
            ],
            'date' => Carbon::now()->translatedFormat('l, d F Y'),
            'schedules' => $scheduleData,
        ], 'Jadwal hari ini berhasil dimuat');
    }

    /**
     * Check-in / Presensi Masuk (One-click)
     */
    public function checkIn(Request $request): JsonResponse
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return $this->error('Data guru tidak ditemukan untuk akun ini.', 404);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $todayDay = strtolower(Carbon::now()->format('l'));
        $todayDate = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 1. Find the currently active/eligible schedule
        $schedules = Schedule::with(['classHour', 'school'])
            ->where('teacher_id', $teacher->id)
            ->where('day', $todayDay)
            ->where('is_active', true)
            ->get();

        $activeSchedule = null;
        $existingSession = null;
        foreach ($schedules as $schedule) {
            $startTime = Carbon::parse($todayDate . ' ' . ($schedule->classHour?->start_time ?? '00:00:00'));
            $endTime = Carbon::parse($todayDate . ' ' . ($schedule->classHour?->end_time ?? '00:00:00'));
            $allowedStartTime = $startTime->copy()->subMinutes(15);

            if ($now->greaterThanOrEqualTo($allowedStartTime) && $now->lessThanOrEqualTo($endTime)) {
                $session = PresensiSession::where('schedule_id', $schedule->id)
                    ->where('date', $todayDate)
                    ->first();

                if (!$session) {
                    $activeSchedule = $schedule;
                    break;
                } elseif ($session->status === 'scheduled') {
                    $activeSchedule = $schedule;
                    $existingSession = $session;
                    break;
                }
            }
        }

        if (!$activeSchedule) {
            return $this->error('Tidak ada jadwal mengajar aktif atau Anda sudah melakukan presensi masuk untuk jadwal saat ini.', 400);
        }

        // 2. Geofencing Validation
        $school = School::find($activeSchedule->school_id);
        if ($school && $school->latitude && $school->longitude) {
            $distance = $this->calculateDistance(
                (float) $request->latitude,
                (float) $request->longitude,
                (float) $school->latitude,
                (float) $school->longitude
            );

            if ($distance > $school->radius_meters) {
                return $this->error(sprintf('Anda berada di luar area sekolah. Jarak Anda: %.1f meter (Maksimum radius: %d meter).', $distance, $school->radius_meters), 400);
            }
        }

        // 3. Handle Selfie Photo (if uploaded)
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('teacher_attendance', 'public');
        }

        // 4. If a scheduled session exists, activate it
        if ($existingSession) {
            $scheduledStartTime = Carbon::parse($todayDate . ' ' . $activeSchedule->classHour->start_time);
            $isLate = $now->greaterThan($scheduledStartTime->copy()->addMinutes(15));

            $existingSession->update([
                'status' => 'open',
                'start_time' => $now->toTimeString(),
                'end_time' => $activeSchedule->classHour->end_time,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'photo' => $photoPath,
                'is_late' => $isLate,
            ]);

            return $this->success($existingSession, 'Presensi Masuk berhasil disimpan. Status mengajar: Sedang Mengajar.');
        }

        // 5. Determine Lateness (if checked in > 15 minutes after start_time)
        $scheduledStartTime = Carbon::parse($todayDate . ' ' . $activeSchedule->classHour->start_time);
        $isLate = $now->greaterThan($scheduledStartTime->copy()->addMinutes(15));

        // 6. Create PresensiSession
        $session = PresensiSession::create([
            'school_id' => $activeSchedule->school_id,
            'schedule_id' => $activeSchedule->id,
            'teacher_id' => $teacher->id,
            'date' => $todayDate,
            'start_time' => $now->toTimeString(),
            'end_time' => $activeSchedule->classHour->end_time,
            'status' => 'open',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $photoPath,
            'is_late' => $isLate,
        ]);

        return $this->success($session, 'Presensi Masuk berhasil disimpan. Status mengajar: Sedang Mengajar.');
    }

    /**
     * Check-out / Presensi Keluar (One-click)
     */
    public function checkOut(Request $request): JsonResponse
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return $this->error('Data guru tidak ditemukan untuk akun ini.', 404);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $todayDate = Carbon::today()->toDateString();

        // 1. Find the active open session for today
        $session = PresensiSession::where('teacher_id', $teacher->id)
            ->where('date', $todayDate)
            ->where('status', 'open')
            ->first();

        if (!$session) {
            return $this->error('Tidak ada sesi mengajar aktif yang dapat di-check out.', 400);
        }

        // 2. Geofencing Validation
        $school = School::find($session->school_id);
        if ($school && $school->latitude && $school->longitude) {
            $distance = $this->calculateDistance(
                (float) $request->latitude,
                (float) $request->longitude,
                (float) $school->latitude,
                (float) $school->longitude
            );

            if ($distance > $school->radius_meters) {
                return $this->error(sprintf('Anda berada di luar area sekolah. Jarak Anda: %.1f meter (Maksimum radius: %d meter).', $distance, $school->radius_meters), 400);
            }
        }

        // 3. Update PresensiSession
        $now = Carbon::now();
        $session->update([
            'status' => 'closed',
            'end_time' => $now->toTimeString(),
            'closed_by' => $user->id,
            'closed_at' => $now,
            'closed_latitude' => $request->latitude,
            'closed_longitude' => $request->longitude,
        ]);

        return $this->success($session, 'Presensi Keluar berhasil disimpan. Sesi mengajar telah selesai.');
    }

    /**
     * Calculate distance between two coordinates in meters (Haversine formula)
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
