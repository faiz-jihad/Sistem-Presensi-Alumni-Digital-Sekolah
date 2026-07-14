<?php

namespace App\Http\Controllers\Api;

use App\Models\AlumniEvent;
use App\Http\Requests\AlumniEvent\StoreAlumniEventRequest;
use App\Http\Requests\AlumniEvent\UpdateAlumniEventRequest;
use App\Http\Resources\AlumniEventResource;
use App\Services\AlumniEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AlumniEventController extends BaseController
{
    public function __construct(
        private readonly AlumniEventService $eventService
    ) {}

    /**
     * Tampilkan daftar event alumni berdasarkan role
     */
    public function index(Request $request): JsonResponse
    {
        try {
            if (!$request->user()) {
                return $this->error('Unauthenticated.', 401);
            }
            $perPage = max(1, min((int) $request->query('per_page', 10), 100));
            $events = $this->eventService->listEvents($request->user(), $perPage);
            return $this->success([
                'data' => AlumniEventResource::collection($events->getCollection())->resolve($request),
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ], 'Daftar event alumni berhasil diambil');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Tampilkan detail event alumni
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $event = AlumniEvent::with(['school', 'postedBy'])->find($id);

            if (!$event) {
                return $this->error('Event tidak ditemukan', 404);
            }

            // Otorisasi via Policy
            if (!Gate::forUser($request->user())->allows('view', $event)) {
                return $this->forbidden();
            }

            return $this->success(
                new AlumniEventResource($event),
                'Detail event alumni berhasil diambil'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Buat/ajukan event alumni baru
     */
    public function store(StoreAlumniEventRequest $request): JsonResponse
    {
        try {
            $event = $this->eventService->createEvent(
                $request->validated(),
                $request->user(),
                $request->file('banner_image')
            );

            $message = $request->user()?->role === 'alumni' 
                ? 'Event alumni berhasil diajukan, menunggu persetujuan admin.' 
                : 'Event alumni berhasil ditambahkan.';

            return $this->success(
                new AlumniEventResource($event->load(['school', 'postedBy'])),
                $message,
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Update event alumni
     */
    public function update(UpdateAlumniEventRequest $request, $id): JsonResponse
    {
        try {
            $event = AlumniEvent::find($id);

            if (!$event) {
                return $this->error('Event tidak ditemukan', 404);
            }

            // Otorisasi via Policy
            if (!Gate::forUser($request->user())->allows('update', $event)) {
                return $this->forbidden('Hanya pengaju yang dapat mengedit event berstatus pending');
            }

            $updatedEvent = $this->eventService->updateEvent(
                $event,
                $request->validated(),
                $request->file('banner_image')
            );

            return $this->success(
                new AlumniEventResource($updatedEvent->load(['school', 'postedBy'])),
                'Event alumni berhasil diperbarui'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Hapus event alumni
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $event = AlumniEvent::find($id);

            if (!$event) {
                return $this->error('Event tidak ditemukan', 404);
            }

            // Otorisasi via Policy
            if (!Gate::forUser($request->user())->allows('delete', $event)) {
                return $this->forbidden('Hanya pengaju yang dapat menghapus event berstatus pending');
            }

            $this->eventService->deleteEvent($event);

            return $this->success(null, 'Event alumni berhasil dihapus');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Setujui pengajuan event (Admin & Super Admin)
     */
    public function approve(Request $request, $id): JsonResponse
    {
        try {
            $event = AlumniEvent::find($id);

            if (!$event) {
                return $this->error('Event tidak ditemukan', 404);
            }

            // Otorisasi via Policy
            if (!Gate::forUser($request->user())->allows('approve', $event)) {
                return $this->forbidden();
            }

            $approvedEvent = $this->eventService->approveEvent($event);

            return $this->success(
                new AlumniEventResource($approvedEvent->load(['school', 'postedBy'])),
                'Event alumni berhasil disetujui'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Tolak pengajuan event (Admin & Super Admin)
     */
    public function reject(Request $request, $id): JsonResponse
    {
        try {
            $event = AlumniEvent::find($id);

            if (!$event) {
                return $this->error('Event tidak ditemukan', 404);
            }

            // Otorisasi via Policy
            if (!Gate::forUser($request->user())->allows('reject', $event)) {
                return $this->forbidden();
            }

            $rejectedEvent = $this->eventService->rejectEvent($event);

            return $this->success(
                new AlumniEventResource($rejectedEvent->load(['school', 'postedBy'])),
                'Event alumni ditolak'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
