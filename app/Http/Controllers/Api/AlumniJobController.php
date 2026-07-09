<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\JobVacancyResource;
use App\Services\AlumniJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AlumniJobController extends BaseController
{
    public function __construct(
        private readonly AlumniJobService $jobService
    ) {}

    /**
     * Get a list of active job vacancies for alumni.
     */
    public function index(Request $request): JsonResponse
    {
        // Otorisasi via Policy (opsional untuk viewAny)
        if (Gate::allows('viewAny', \App\Models\JobVacancy::class)) {
            // pass
        }

        try {
            $perPage = (int) $request->query('per_page', 10);
            $filters = $request->only(['search', 'type', 'category']);

            $jobs = $this->jobService->listJobs($filters, $perPage);

            return $this->success(
                JobVacancyResource::collection($jobs),
                'Daftar lowongan kerja berhasil diambil'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
