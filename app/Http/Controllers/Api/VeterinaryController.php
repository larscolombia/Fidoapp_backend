<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;

class VeterinaryController extends Controller
{
    public function listVeterinaries ($petId) {
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
}
