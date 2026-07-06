<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QrTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'token'      => $this->token,
            'expired_at' => $this->expired_at?->toDateTimeString(),
            'expires_in_seconds' => max(0, (int) now()->diffInSeconds($this->expired_at, false)),
            'session_id' => $this->presensi_session_id,
        ];
    }
}
