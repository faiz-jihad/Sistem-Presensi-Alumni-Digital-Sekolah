<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 30), 1), 100);

        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn ($notification) => $this->formatNotification($notification));

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $request->user()->unreadNotifications()->count(),
                'notifications' => $notifications,
            ],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ],
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi sudah ditandai dibaca.',
            'data' => [
                'unread_count' => 0,
            ],
        ]);
    }

    private function formatNotification($notification): array
    {
        $data = $notification->data;
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $data = is_array($decoded) ? $decoded : [];
        }

        return [
            'id' => $notification->id,
            'title' => $data['title'] ?? 'Notifikasi',
            'body' => $data['body'] ?? ($data['message'] ?? ''),
            'type' => $data['data']['type'] ?? $data['type'] ?? $notification->type,
            'payload' => $data['data'] ?? $data,
            'read_at' => optional($notification->read_at)->toISOString(),
            'created_at' => optional($notification->created_at)->toISOString(),
        ];
    }
}
