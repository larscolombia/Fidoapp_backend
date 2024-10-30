<?php

namespace App\Http\Controllers\Api;

use Modules\Pet\Models\Pet;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use App\Http\Controllers\Controller;

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

    public function getPetsAssignedToTheTrainer(Request $request)
    {
        $request->validate([
            'user_id' =>'required'
        ]);
        try{
            $userId = $request['user_id'];
            // Obtener todas las mascotas que tienen reservas asociadas
            $pets = Pet::with(['bookings.user' => function ($query) {
                $query->where('user_type', 'trainer');
            }])
            ->whereHas('bookings', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status','pending');
            })->join('breeds', 'pets.breed_id','=','breeds.id')
            ->select('pets.name','breeds.name as breed','pets.age','pets.status')
            ->distinct()
            ->get();

            return response()->json([
                'success' =>true,
                'data' => $pets,
                'messages' => 'success'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success' =>false,
                'data' => null,
                'messages' => $e->getMessage()
            ]);
        }

    }
}
