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
        try{
            $data = $request->validate([
               'pet_id' => 'required|exists:pets,id',
               'veterinarian_id' => 'required|exists:users,id',
            ]);
            $user = User::with(['pets','pets.bookings'])
            ->whereHas('pets.bookings', function($q) use($data) {
                return $q->where('employee_id', $data['veterinarian_id'])
                            ->where('pet_id',$data['pet_id']);
            })
            ->select('users.id','users.first_name','users.last_name','users.email','users.mobile')
            ->first();

            if(!$user){
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'user_info' => $user,
                    'profile_info' => $user->profile
                ]
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
