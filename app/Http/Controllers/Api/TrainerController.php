<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Modules\Pet\Models\Pet;
use Illuminate\Http\Request;
use App\Models\PermissionRequest;
use Modules\Booking\Models\Booking;
use App\Http\Controllers\Controller;

class TrainerController extends Controller
{
    public function listTrainers($petId)
    {
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

    public function listTrainersVeterinaries($petId)
    {
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
            'user_id' => 'required'
        ]);
        try {
            $userId = $request['user_id'];
            // Obtener todas las mascotas que tienen reservas asociadas
            $pets = Pet::with(['bookings.user' => function ($query) {
                $query->where('user_type', 'trainer');
            }])
                ->whereHas('bookings', function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                        ->where('status', 'pending');
                })->join('breeds', 'pets.breed_id', '=', 'breeds.id')
                ->select('pets.name', 'breeds.name as breed', 'pets.age', 'pets.status')
                ->distinct()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pets,
                'messages' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'messages' => $e->getMessage()
            ]);
        }
    }

    public function trainerPets(Request $request)
    {
        $request->validate([
            'employee_id' => 'required'
        ]);
        $employeeId = $request['employee_id'];
        // Obtener todas las mascotas que tienen reservas asociadas
        $pets = Pet::with(['bookings.employee' => function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        }])
            ->distinct()
            ->get();

        // Retornar la respuesta en formato JSON
        return response()->json([
            'data' => $pets,
            'success' => true
        ]);
    }

    public function getUserSocialNetwork(Request $request)
    {
        try {
            // Validación de datos
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'trainer_id' => 'required|exists:users,id',
            ]);

            // Verificación de permisos
            $permissionRequest = PermissionRequest::where('requester_id', $data['trainer_id'])
                ->where('target_id', $data['user_id'])
                ->where('accepted', 1)
                ->first();

            if (!$permissionRequest) {
                return response()->json([
                    'success' => false,
                    'message' => __('You don\'t have permission')
                ], 403); // Código de estado 403 Forbidden
            }

            // Carga del usuario con su perfil
            $user = User::with('profile')->findOrFail($data['user_id']);

            // Preparación de la respuesta
            $response = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ];

            // Agregar redes sociales si el perfil existe
            if ($user->profile) {
                $response['social_links'] = [
                    'facebook' => $user->profile->facebook_link,
                    'twitter' => $user->profile->twitter_link,
                    'instagram' => $user->profile->instagram_link,
                    'dribbble' => $user->profile->dribbble_link,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $response
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('Validation failed'),
                'errors' => $e->validator->errors()
            ], 422); // Código de estado 422 Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An unexpected error occurred')
            ], 500); // Código de estado 500 Internal Server Error
        }
    }
}
