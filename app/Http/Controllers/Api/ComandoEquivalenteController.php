<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comandos\ComandoStoreRequest;
use App\Http\Requests\Comandos\ComandoUpdateRequest;
use App\Http\Resources\Comandos\ComandoEquivalenteResource;
use App\Models\ComandoEquivalente;
use Illuminate\Http\Request;

class ComandoEquivalenteController extends Controller
{
    /**
     * Mostrar todos los registros de comando equivalente con el nombre del usuario.
     * Método HTTP: GET
     * Ruta: /api/comando-equivalente
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index () {
        // Obtener todos los registros con la relación 'user'
        $comandosEquivalentes = ComandoEquivalente::with('user')->get();

        // Utilizar el recurso para la transformación
        return ComandoEquivalenteResource::collection($comandosEquivalentes);
    }

    public function store(ComandoStoreRequest $request)
    {
        $comandoEquivalente = ComandoEquivalente::create($request->only('comando_id', 'name', 'user_id'));

        return response()->json([
            'success' => true,
            'message' => __('comando_entrenamiento.Comando equivalente creado con éxito'),
            'data' => $comandoEquivalente
        ], 201);
    }

    public function show($id)
    {
        $comandoEquivalente = ComandoEquivalente::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => __('comando_entrenamiento.commands_equivalent_by_id'),
            'data' => $comandoEquivalente
        ], 200);
    }

    public function getByUserId($user_id)
    {
        $comandoEquivalente = ComandoEquivalente::where('user_id', $user_id)->get();

        return response()->json([
            'success' => true,
            'message' => __('comando_entrenamiento.commands_equivalent_list_by_user'),
            'data' => $comandoEquivalente
        ], 200);
    }

    public function update(ComandoUpdateRequest $request, $id)
    {
        // Buscar el registro por ID, lanzar 404 si no existe
        $comandoEquivalente = ComandoEquivalente::findOrFail($id);

        // Obtener solo los campos que están presentes en la solicitud
        $data = $request->only('comando_id', 'name', 'user_id');

        // Filtrar los campos no nulos
        $filteredData = array_filter($data, function($value) {
            return $value !== null;
        });

        // Actualizar el registro con los datos filtrados
        $comandoEquivalente->update($filteredData);

        return response()->json([
            'success' => true,
            'message' => __('comando_entrenamiento.Comando equivalente actualizado con éxito'),
            'data' => $comandoEquivalente
        ], 200);
    }

    public function destroy($id)
    {
        $comandoEquivalente = ComandoEquivalente::findOrFail($id);
        $comandoEquivalente->delete();

        return response()->json([
            'success' => true,
            'message' => __('comando_entrenamiento.Comando equivalente eliminado con éxito')
        ], 200);
    }
}
