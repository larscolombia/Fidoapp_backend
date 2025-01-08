<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function getNotification(Request $request)
    {
        $data =     $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $userNotifications = UserNotification::where('user_id', $data['user_id'])
                ->orderByDesc('id')->get();
            // Transformar las notificaciones
            $formattedNotifications = $userNotifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'description' => $notification->description,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at,
                    'updated_at' => $notification->updated_at,
                    'status' => optional($notification->bookings) ? optional($notification->bookings)->status : null,
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $formattedNotifications,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las notificaciones: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateRead($id)
    {
        $userNotification = UserNotification::findOrFail($id);

        $userNotification->is_read = true;
        $userNotification->save();
        return response()->json([
            'success' => true,
            'data' => $userNotification,
            'message' => 'Registration successfully updated'
        ], 200);
    }
}
