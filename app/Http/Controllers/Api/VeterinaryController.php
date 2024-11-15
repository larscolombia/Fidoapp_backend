<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\PetHistory;
use Modules\Pet\Models\Pet;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use App\Http\Controllers\Controller;

class VeterinaryController extends Controller
{
    public function listVeterinaries($petId)
    {
        $list_veterinaries = Booking::where('pet_id', $petId)
            ->where('status', 'completed')
            ->where('booking_type', 'veterinary')
            ->get();

        return response()->json([
            'data' => $list_veterinaries,
            'message' => __('booking.List of veterinaries.'),
            'success' => true
        ]);
    }

    public function listPets(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric'
        ]);
        $userId = $request['user_id'];
        $pets = Pet::with('bookings')
            ->whereHas('bookings', function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->get();

        return response()->json(
            [
                'data' => $pets,
                'message' => __('Exitoso'),
                'success' => true
            ]
        );
    }

    public function petListByVeterinarian(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|numeric',
                'most_recent' => 'nullable',
                'sort_asc_alphabetically' => 'nullable',
                'sort_desc_alphabetically' => 'nullable',
                'category' => 'nullable|array',
                'category.*' => 'numeric',
                'date' => 'nullable|date'

            ]);
            $userId = $request['user_id'];
            $query = Pet::with('bookings')
                ->whereHas('bookings', function ($query) use ($userId, $request) {
                    $query->where('employee_id', $userId);
                    // Filtrar por fecha si se proporciona
                    if ($request->filled('date')) {
                        $query->whereDate('created_at', $request->input('date'));
                    }
                    return $query;
                });

            if ($request->filled('category')) {
                $query->whereHas('bookings.veterinary.service.category', function ($query) use ($request) {
                    $query->whereIn('id', $request->input('category'));
                });
            }
            // Ordenar alfabéticamente si se especifica
            if ($request->filled('sort_asc_alphabetically') && $request->input('sort_asc_alphabetically')) {
                $query->orderBy('name', 'asc');
            } elseif ($request->filled('sort_desc_alphabetically') && $request->input('sort_desc_alphabetically')) {
                $query->orderBy('name', 'desc');
            }

            // Obtener los resultados
            $pets = $query->get();

            return response()->json(
                [
                    'data' => $pets,
                    'message' => __('Exitoso'),
                    'success' => true
                ]
            );
        } catch (\Exception $e) {

            return response()->json(
                [
                    'message' => $e->getMessage(),
                    'success' => false
                ]
            );
        }
    }

    public function petHistoryListByVeterinarian($id)
    {
        try {
            $history = PetHistory::with(['pet', 'pet.vacunas', 'pet.antidesparasitantes', 'pet.antigarrapatas', 'veterinarian'])
                ->where('veterinarian_id', $id)->get();
            if (count($history) == 0) {
                return response()->json(['success' => false, 'message' => 'History not found'], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function petOwnerInformation(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'veterinarian_id' => 'required|exists:users,id',
            ]);
            $user = User::with(['pets', 'pets.histories'])
                ->whereHas('pets.histories', function ($q) use ($data) {
                    return $q->where('veterinarian_id', $data['veterinarian_id'])
                        ->where('pet_id', $data['pet_id']);
                })
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.mobile')
                ->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'user_info' => $user,
                    'profile_info' => $user->profile
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
