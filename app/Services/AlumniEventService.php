<?php

namespace App\Services;

use App\Models\AlumniEvent;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class AlumniEventService
{
    /**
     * Mengambil daftar kegiatan alumni dengan eager loading
     */
    public function listEvents(User $user, int $perPage = 10): LengthAwarePaginator
    {
        $query = AlumniEvent::with(['school', 'postedBy']);

        if ($user->role === 'alumni') {
            $query->where(function ($q) use ($user) {
                $q->where('approval_status', 'approved')
                  ->orWhere('posted_by', $user->id);
            });
            if ($user->school_id) {
                $query->where('school_id', $user->school_id);
            }
        } elseif ($user->role !== 'super_admin') {
            $query->where('school_id', $user->school_id);
        }

        return $query->orderBy('event_date', 'asc')->paginate($perPage);
    }

    /**
     * Membuat pengajuan kegiatan alumni baru
     */
    public function createEvent(array $data, User $user, $bannerFile = null): AlumniEvent
    {
        $data['posted_by'] = $user->id;

        // Tentukan school_id
        if ($user->role === 'super_admin') {
            $data['school_id'] = $data['school_id'] ?? ($user->school_id ?? \App\Models\School::first()?->id);
        } else {
            $data['school_id'] = $user->school_id;
        }

        // Tentukan status persetujuan
        if ($user->role === 'alumni') {
            $data['approval_status'] = 'pending';
        } else {
            $data['approval_status'] = 'approved';
        }

        // Upload banner
        if ($bannerFile) {
            $path = $bannerFile->store('event-banners', 'public');
            $data['banner_image'] = $path;
        }

        return AlumniEvent::create($data);
    }

    /**
     * Memperbarui data kegiatan alumni
     */
    public function updateEvent(AlumniEvent $event, array $data, $bannerFile = null): AlumniEvent
    {
        // Upload banner baru
        if ($bannerFile) {
            // Hapus banner lama jika ada
            if ($event->banner_image) {
                Storage::disk('public')->delete($event->banner_image);
            }
            $path = $bannerFile->store('event-banners', 'public');
            $data['banner_image'] = $path;
        }

        $event->update($data);

        return $event;
    }

    /**
     * Menghapus kegiatan alumni beserta berkas banner
     */
    public function deleteEvent(AlumniEvent $event): bool
    {
        if ($event->banner_image) {
            Storage::disk('public')->delete($event->banner_image);
        }

        return $event->delete();
    }

    /**
     * Menyetujui pengajuan kegiatan alumni
     */
    public function approveEvent(AlumniEvent $event): AlumniEvent
    {
        if ($event->approval_status !== 'approved') {
            $event->update(['approval_status' => 'approved']);
        }
        return $event;
    }

    /**
     * Menolak pengajuan kegiatan alumni
     */
    public function rejectEvent(AlumniEvent $event): AlumniEvent
    {
        if ($event->approval_status !== 'rejected') {
            $event->update(['approval_status' => 'rejected']);
        }
        return $event;
    }
}
