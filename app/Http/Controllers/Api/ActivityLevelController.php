<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLevel;
use Illuminate\Http\Request;

class ActivityLevelController extends Controller
{
    /**
     * Obtener los niveles de actividad de una mascota específica.
     */
    public function index($pet_id)
    {
        $activityLevels = ActivityLevel::where('pet_id', $pet_id)->get();

        if ($activityLevels->isEmpty()) {
            return response()->json([
                'message' => 'Niveles de actividad no encontrados para la mascota especificada',
                'success' => false,
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Lista de niveles de actividad de la mascota',
            'success' => true,
            'data' => $activityLevels,
        ], 200);
    }

    /**
     * Crear un nuevo nivel de actividad para una mascota específica.
     */
    public function store(Request $request, $pet_id)
    {
        $validatedData = $request->validate([
            'daily_steps' => 'nullable|integer',
            'distance_covered' => 'nullable|numeric',
            'calories_burned' => 'nullable|integer',
            'active_minutes' => 'nullable|integer',
            'goal_steps' => 'nullable|integer',
            'goal_distance' => 'nullable|numeric',
            'goal_calories' => 'nullable|integer',
            'goal_active_minutes' => 'nullable|integer',
        ]);

        $activityLevel = ActivityLevel::create(array_merge($validatedData, ['pet_id' => $pet_id]));

        return response()->json([
            'message' => 'Nivel de actividad creado exitosamente',
            'success' => true,
            'data' => $activityLevel,
        ], 201);
    }

    /**
     * Actualizar un nivel de actividad existente para una mascota específica.
     */
    public function update(Request $request, $id)
    {
        $activityLevel = ActivityLevel::find($id);

        if (!$activityLevel) {
            return response()->json([
                'message' => 'Nivel de actividad no encontrado',
                'success' => false,
                'data' => null,
            ], 404);
        }

        $validatedData = $request->validate([
            'daily_steps' => 'nullable|integer',
            'distance_covered' => 'nullable|numeric',
            'calories_burned' => 'nullable|integer',
            'active_minutes' => 'nullable|integer',
            'goal_steps' => 'nullable|integer',
            'goal_distance' => 'nullable|numeric',
            'goal_calories' => 'nullable|integer',
            'goal_active_minutes' => 'nullable|integer',
        ]);

        $activityLevel->update($validatedData);

        return response()->json([
            'message' => 'Nivel de actividad actualizado exitosamente',
            'success' => true,
            'data' => $activityLevel,
        ], 200);
    }

    /**
     * Eliminar un nivel de actividad para una mascota específica.
     */
    public function destroy($id)
    {
        $activityLevel = ActivityLevel::find($id);

        if (!$activityLevel) {
            return response()->json([
                'message' => 'Nivel de actividad no encontrado',
                'success' => false,
                'data' => null,
            ], 404);
        }

        $activityLevel->delete();

        return response()->json([
            'message' => 'Nivel de actividad eliminado exitosamente',
            'success' => true,
            'data' => null,
        ], 200);
    }
}
