<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChipsController extends Controller
{
     /**
     * Obtener la lista de todos los chips.
     * 
     * Método HTTP: GET
     * Ruta: /api/chips
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $chips = Chip::with('pet', 'fabricante')->get();
        return response()->json([
            'data' => $chips,
            'message' => 'Lista de todos los chips',
            'success' => true
        ]);
    }

    /**
     * Obtener los detalles de un chip específico.
     * 
     * Método HTTP: GET
     * Ruta: /api/chips/{id}
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $chip = Chip::with('pet', 'fabricante')->findOrFail($id);
        return response()->json([
            'data' => $chip,
            'message' => 'Detalles del chip',
            'success' => true
        ]);
    }

    /**
     * Crear un nuevo chip.
     * 
     * Método HTTP: POST
     * Ruta: /api/chips
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'num_identificacion' => 'required|bigInteger|unique:chips,num_identificacion',
            'pet_id' => 'required|exists:pets,id',
            'fecha_implantacion' => 'required|date',
            'fabricante_id' => 'required|exists:fabricantes,id',
            'num_contacto' => 'required|string|max:15',
        ]);

        $chip = Chip::create($request->all());

        return response()->json([
            'data' => $chip,
            'message' => 'Chip creado exitosamente',
            'success' => true
        ], 201);
    }

    /**
     * Actualizar un chip existente.
     * 
     * Método HTTP: PUT
     * Ruta: /api/chips/{id}
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $chip = Chip::findOrFail($id);

        $request->validate([
            'num_identificacion' => 'required|bigInteger|unique:chips,num_identificacion,' . $chip->id,
            'fecha_implantacion' => 'required|date',
            'fabricante_id' => 'required|exists:fabricantes,id',
            'num_contacto' => 'required|string|max:15',
        ]);

        $chip->update($request->all());

        return response()->json([
            'data' => $chip,
            'message' => 'Chip actualizado exitosamente',
            'success' => true
        ]);
    }

    /**
     * Eliminar un chip.
     * 
     * Método HTTP: DELETE
     * Ruta: /api/chips/{id}
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $chip = Chip::findOrFail($id);
        $chip->delete();

        return response()->json([
            'message' => 'Chip eliminado exitosamente',
            'success' => true
        ], 204);
    }
    
    public function getChipByPet($pet_id)
    {
        // Intentar obtener el chip asociado a la mascota
        $chip = Chip::with('pet', 'fabricante')->where('pet_id', $pet_id)->first();

        // Verificar si se encontró un chip
        if (!$chip) {
            return response()->json([
                'message' => 'Chip no encontrado para la mascota especificada',
                'success' => false,
            ], 404);
        }

        // Retornar el chip si se encontró
        return response()->json([
            'data' => $chip,
            'message' => 'Detalles del chip asociado a la mascota',
            'success' => true,
        ]);
    }
}
