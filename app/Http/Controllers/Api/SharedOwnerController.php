<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pet\Models\Pet;

class SharedOwnerController extends Controller
{
     /**
     * Agregar un dueño compartido a una mascota.
     * 
     * @param Request $request
     * @param int $petId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSharedOwner(Request $request, $petId)
    {
        // Validar los datos entrantes
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $pet = Pet::findOrFail($petId);
        $userId = $request->input('user_id');

        // Verifica si el usuario ya es un dueño compartido de esta mascota
        if (!$pet->sharedOwners->contains($userId)) {
            // Agrega el dueño compartido con la fecha de creación
            $pet->sharedOwners()->attach($userId, ['created_at' => now(), 'updated_at' => now()]);
        }

        return response()->json(['message' => __('shared_owner.shared_owner_added_successfully')]);
    }

     /**
     * Eliminar un dueño compartido de una mascota.
     * 
     * @param Request $request
     * @param int $petId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeSharedOwner(Request $request, $petId)
    {
        // Validar los datos entrantes
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $pet = Pet::findOrFail($petId);
        $userId = $request->input('user_id');

        // Eliminar el dueño compartido
        $pet->sharedOwners()->detach($userId);

        return response()->json(['message' => __('shared_owner.shared_owner_removed_successfully')]);
    }

    /**
     * Obtener los dueños de una mascota.
     * 
     * @param int $petId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOwners($petId)
    {
        $pet = Pet::with('owner', 'sharedOwners')->findOrFail($petId);

        return response()->json([
            'primary_owner' => $pet->owner,
            'shared_owners' => $pet->sharedOwners
        ]);
    }
}
