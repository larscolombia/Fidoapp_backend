<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;

class TrainerController extends Controller
{
    public function listTrainers ($petId) {
        $list_trainers = Booking::where('pet_id', $petId)
            ->where('status', 'completed')
            ->where('booking_type', 'training')
            ->get();

        return response()->json([
            'data' => $list_trainers,
            'message' => __('booking.List of trainers.'),
            'success' => true
        ]);
    }

    public function listTrainersVeterinaries($petId) {
        // Obtener la lista de reservas completadas de tipo 'training' y 'veterinary'
        $list_bookings = Booking::where('pet_id', $petId)
            ->where('status', 'completed')
            ->whereIn('booking_type', ['training', 'veterinary'])
            ->get();
    
        // Retornar la respuesta en formato JSON
        return response()->json([
            'data' => $list_bookings,
            'message' => __('booking.List of training and veterinary bookings.'),
            'success' => true
        ]);
    }
}
