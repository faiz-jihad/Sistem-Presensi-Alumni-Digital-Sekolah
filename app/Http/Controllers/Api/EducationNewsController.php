<?php

namespace App\Http\Controllers\Api;

use App\Services\EducationNewsService;
use Illuminate\Http\JsonResponse;

class EducationNewsController extends BaseController
{
    public function __construct(
        private readonly EducationNewsService $educationNewsService
    ) {}

    public function index(): JsonResponse
    {
        try {
            return $this->success(
                $this->educationNewsService->latest(),
                'Informasi pendidikan berhasil diambil.'
            );
        } catch (\Throwable $exception) {
            report($exception);

            return $this->error(
                'Informasi pendidikan sedang tidak tersedia.',
                503
            );
        }
    }
}
