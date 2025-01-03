<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Modules\Pet\Models\Pet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Trait\Notification;

class SharedOwnerController extends Controller
{
    use Notification;
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
            $this->sendNotification('shared-owner',__('shared_owner.shared_owner'),$pet,[$request->input('user_id')],__('shared_owner.shared_owner_added_successfully'));
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
        $this->sendNotification('shared-owner',__('shared_owner.shared_owner'),$pet,[$userId], __('shared_owner.shared_owner_removed_successfully'));
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
        try{
            $pet = Pet::with('owner','owner.profile','owner.rating' ,'sharedOwners')->findOrFail($petId);

            return response()->json([
                'success' => true,
                'primary_owner' => $pet->owner,
                'shared_owners' => $pet->sharedOwners
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    public function addSharedOwnerWithEmail(Request $request, $petId)
    {
        // Validar los datos entrantes
        $request->validate([
            'email' => 'required|exists:users,email',
        ]);

        $pet = Pet::findOrFail($petId);
        $email = $request->input('email');
        $user = User::where('email',$email)->select('id')->first();
        $userId = $user->id;
        // Verifica si el usuario ya es un dueño compartido de esta mascota
        if (!$pet->sharedOwners->contains($userId)) {
            // Agrega el dueño compartido con la fecha de creación
            $pet->sharedOwners()->attach($userId, ['created_at' => now(), 'updated_at' => now()]);
            $this->sendNotification('shared-owner',__('shared_owner.shared_owner'),$pet,[$userId],__('shared_owner.shared_owner_added_successfully'));
        }

        return response()->json(['message' => __('shared_owner.shared_owner_added_successfully')]);
    }
}
