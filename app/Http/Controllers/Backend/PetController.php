<?php

namespace App\Http\Controllers\Backend;

use Modules\Pet\Models\Pet;
use Illuminate\Http\Request;
use Modules\Pet\Models\Breed;
use App\Http\Controllers\Controller;

class PetController extends Controller
{
    public function profilePublic($slug)
    {
        // Intentar encontrar la mascota por el slug
        $pet = Pet::where('slug', $slug)->firstOrFail();

        // Determinar el color basado en las relaciones
        $color = $this->determineColor($pet);

        return view('backend.pet.index', compact('pet', 'color'));
    }

    /**
     * Determina el color basado en las relaciones de diario e historias.
     *
     * @param Pet $pet
     * @return string
     */
    private function determineColor(Pet $pet): string
    {
        if ($pet->diario->isNotEmpty() && $pet->histories->isNotEmpty()) {
            return '#28a745'; // Verde
        }

        if ($pet->diario->isNotEmpty()) {
            return '#007bff'; // Azul
        }

        if ($pet->histories->isNotEmpty()) {
            return '#ffc107'; // Amarillo
        }

        return '#dc3545'; // Rojo
    }
}
