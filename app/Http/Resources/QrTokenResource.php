<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QrTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (is_array($this->resource)) {
            return [
                'session_id' => $this->resource['session_id'],
                'qr_token' => $this->resource['qr_token'] ?? $this->resource['token'],
                'token' => $this->resource['token'],
                'class_id' => $this->resource['class_id'] ?? null,
                'tanggal' => $this->resource['tanggal'] ?? $this->resource['date'] ?? null,
                'date' => $this->resource['date'] ?? $this->resource['tanggal'] ?? null,
            ];
        }

        return [
            'token'      => $this->token,
            'expired_at' => $this->expired_at?->toDateTimeString(),
            'expires_in_seconds' => max(0, (int) now()->diffInSeconds($this->expired_at, false)),
            'session_id' => $this->presensi_session_id,
        ];
    }
}
