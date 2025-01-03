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
            $userNotification = UserNotification::where('user_id', $data['user_id'])
            ->orderByDesc('id')->get();

            return response()->json([
                'success' => true,
                'data' => $userNotification,
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
