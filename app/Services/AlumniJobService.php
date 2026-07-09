<?php

namespace App\Services;

use App\Models\JobVacancy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AlumniJobService
{
    /**
     * Mengambil daftar lowongan kerja aktif dengan pagination dan filter
     */
    public function listJobs(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = JobVacancy::with(['postedBy:id,name', 'school:id,name'])
            ->active()
            ->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('company_name', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['type'])) {
            $query->jobType($filters['type']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->paginate($perPage);
    }
}
