<?php

namespace App\Http\Controllers\Api;

use App\Models\AlumniEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AlumniEventController extends BaseController
{
    /**
     * Tampilkan daftar event alumni berdasarkan role
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = AlumniEvent::with(['school', 'postedBy']);

        if ($user->role === 'alumni') {
            // Alumni can only see approved events or their own submissions
            $query->where(function ($q) use ($user) {
                $q->where('approval_status', 'approved')
                  ->orWhere('posted_by', $user->id);
            });
            // Also filter by their school
            if ($user->school_id) {
                $query->where('school_id', $user->school_id);
            }
        } elseif ($user->role !== 'super_admin') {
            // Admin and Teacher can only see events from their school
            $query->where('school_id', $user->school_id);
        }

        $events = $query->orderBy('event_date', 'asc')->get();

        return $this->success($events, 'Daftar event alumni berhasil diambil');
    }

    /**
     * Tampilkan detail event alumni
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $event = AlumniEvent::with(['school', 'postedBy'])->find($id);

        if (!$event) {
            return $this->error('Event tidak ditemukan', 404);
        }

        // Authorize view
        if ($user->role === 'alumni') {
            if ($event->approval_status !== 'approved' && $event->posted_by !== $user->id) {
                return $this->forbidden();
            }
        } elseif ($user->role !== 'super_admin') {
            if ($event->school_id !== $user->school_id) {
                return $this->forbidden();
            }
        }

        return $this->success($event, 'Detail event alumni berhasil diambil');
    }

    /**
     * Buat/ajukan event alumni baru
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'banner_image' => 'nullable|image|max:2048', // max 2MB
        ];

        // Jika super_admin, school_id opsional (fallback ke sekolah pertama jika tidak ada).
        if ($user->role === 'super_admin') {
            $rules['school_id'] = 'nullable|exists:schools,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $data = $request->all();
        $data['posted_by'] = $user->id;

        // Tentukan school_id
        if ($user->role === 'super_admin') {
            $data['school_id'] = $request->input('school_id') ?: ($user->school_id ?: \App\Models\School::first()?->id);
        } else {
            $data['school_id'] = $user->school_id;
        }

        // Tentukan status persetujuan
        if ($user->role === 'alumni') {
            $data['approval_status'] = 'pending';
        } else {
            $data['approval_status'] = 'approved';
        }

        // Handle upload banner
        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('event-banners', 'public');
            $data['banner_image'] = $path;
        }

        $event = AlumniEvent::create($data);

        $message = $user->role === 'alumni' 
            ? 'Event alumni berhasil diajukan, menunggu persetujuan admin.' 
            : 'Event alumni berhasil ditambahkan.';

        return $this->success($event, $message, 201);
    }

    /**
     * Update event alumni
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $event = AlumniEvent::find($id);

        if (!$event) {
            return $this->error('Event tidak ditemukan', 404);
        }

        // Authorize update
        if ($user->role === 'alumni') {
            // Alumni only edit their own pending events
            if ($event->posted_by !== $user->id || $event->approval_status !== 'pending') {
                return $this->forbidden('Hanya pengaju yang dapat mengedit event berstatus pending');
            }
        } elseif ($user->role !== 'super_admin') {
            // Admin / Teacher edit school events
            if ($event->school_id !== $user->school_id) {
                return $this->forbidden();
            }
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'banner_image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];

        // Admin/SuperAdmin can also update approval_status in this API (opsional)
        if (in_array($user->role, ['super_admin', 'admin'])) {
            $rules['approval_status'] = 'nullable|in:pending,approved,rejected';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $data = $request->all();

        // Handle upload banner baru
        if ($request->hasFile('banner_image')) {
            // Hapus banner lama jika ada
            if ($event->banner_image) {
                Storage::disk('public')->delete($event->banner_image);
            }
            $path = $request->file('banner_image')->store('event-banners', 'public');
            $data['banner_image'] = $path;
        }

        $event->update($data);

        return $this->success($event, 'Event alumni berhasil diperbarui');
    }

    /**
     * Hapus event alumni
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $event = AlumniEvent::find($id);

        if (!$event) {
            return $this->error('Event tidak ditemukan', 404);
        }

        // Authorize delete
        if ($user->role === 'alumni') {
            if ($event->posted_by !== $user->id || $event->approval_status !== 'pending') {
                return $this->forbidden('Hanya pengaju yang dapat menghapus event berstatus pending');
            }
        } elseif ($user->role !== 'super_admin') {
            if ($event->school_id !== $user->school_id) {
                return $this->forbidden();
            }
        }

        // Hapus file banner jika ada
        if ($event->banner_image) {
            Storage::disk('public')->delete($event->banner_image);
        }

        $event->delete();

        return $this->success(null, 'Event alumni berhasil dihapus');
    }

    /**
     * Setujui pengajuan event (Admin & Super Admin)
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $event = AlumniEvent::find($id);

        if (!$event) {
            return $this->error('Event tidak ditemukan', 404);
        }

        // Check school if admin
        if ($user->role !== 'super_admin' && $event->school_id !== $user->school_id) {
            return $this->forbidden();
        }

        if ($event->approval_status === 'approved') {
            return $this->success($event, 'Event alumni sudah disetujui');
        }

        $event->update(['approval_status' => 'approved']);

        return $this->success($event, 'Event alumni berhasil disetujui');
    }

    /**
     * Tolak pengajuan event (Admin & Super Admin)
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $event = AlumniEvent::find($id);

        if (!$event) {
            return $this->error('Event tidak ditemukan', 404);
        }

        // Check school if admin
        if ($user->role !== 'super_admin' && $event->school_id !== $user->school_id) {
            return $this->forbidden();
        }

        if ($event->approval_status === 'rejected') {
            return $this->success($event, 'Event alumni ditolak');
        }

        $event->update(['approval_status' => 'rejected']);

        return $this->success($event, 'Event alumni ditolak');
    }
}
