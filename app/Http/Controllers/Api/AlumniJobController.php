<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

class AlumniJobController extends BaseController
{
    /**
     * Get a list of active job vacancies for alumni.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');
        $type = $request->query('type');
        $category = $request->query('category');

        $query = JobVacancy::with(['postedBy:id,name', 'school:id,name'])
            ->active()
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('company_name', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        if ($type) {
            $query->jobType($type);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $jobs = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar lowongan kerja berhasil diambil',
            'data' => $jobs
        ]);
    }
}
