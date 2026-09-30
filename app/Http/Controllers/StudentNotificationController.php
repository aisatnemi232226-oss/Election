<?php

namespace App\Http\Controllers;

use App\Notifications\StudentVoterVerified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->where('type', StudentVoterVerified::class)
            ->latest()
            ->limit(10)
            ->get();

        $unreadCount = $request->user()
            ->unreadNotifications()
            ->where('type', StudentVoterVerified::class)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications->map(static function ($notification): array {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'],
                    'message' => $notification->data['message'],
                    'url' => $notification->data['url'],
                    'created_at' => $notification->created_at?->toIso8601String(),
                    'read_at' => $notification->read_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function markRead(Request $request): JsonResponse
    {
        $updated = $request->user()
            ->unreadNotifications()
            ->where('type', StudentVoterVerified::class)
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'updated_count' => $updated,
        ]);
    }
}
