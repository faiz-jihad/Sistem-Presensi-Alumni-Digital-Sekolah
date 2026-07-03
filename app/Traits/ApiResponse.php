<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Response sukses
     */
    protected function success($data = null, string $message = 'Berhasil', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Response error
     */
    protected function error(string $message = 'Terjadi kesalahan', int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Response validasi gagal
     */
    protected function validationError($errors, string $message = 'Validasi gagal', int $code = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Response tidak diizinkan
     */
    protected function unauthorized(string $message = 'Tidak diizinkan', int $code = 401): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    /**
     * Response akses ditolak
     */
    protected function forbidden(string $message = 'Akses ditolak', int $code = 403): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    /**
     * Response data tidak ditemukan
     */
    protected function notFound(string $message = 'Data tidak ditemukan', int $code = 404): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}