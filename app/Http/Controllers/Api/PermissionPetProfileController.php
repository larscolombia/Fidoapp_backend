<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\ExpiryDate;
use Illuminate\Http\Request;
use App\Models\PermissionProfile;
use App\Http\Controllers\Controller;
use App\Models\PermissionPetProfile;

class PermissionPetProfileController extends Controller
{
    public function index()
    {
        $expirations = ExpiryDate::get();
        $permissionProfiles =  PermissionProfile::get();
        return response()->json([
            'success' => true,
            'permission_profiles' => $permissionProfiles,
            'expirations' => $expirations
        ],200);
    }
    public function updatePermission(Request $request)
    {
        // Validación de datos
        $validatedData = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'permission_profile_id' => ['required', 'integer'],
            'expiration' => ['required', 'integer', 'exists:expiry_dates,id'],
        ]);

        try {
            // Obtener la fecha de expiración
            $expiryDate = ExpiryDate::findOrFail($validatedData['expiration']);

            // Calcular la fecha de expiración
            $now = Carbon::now();
            $expiry = $now->addDays($expiryDate->day);

            // Actualizar o crear el perfil de permiso del mascota
            $permissionPetProfile = PermissionPetProfile::updateOrCreate(
                ['pet_id' => $validatedData['pet_id']],
                [
                    'permission_profile_id' => $validatedData['permission_profile_id'],
                    'expiration' => $expiry
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $permissionPetProfile
            ],200);
        } catch (\Exception $e) {
            \Log::error('Error:'.$e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ],500);
        }
    }
}
