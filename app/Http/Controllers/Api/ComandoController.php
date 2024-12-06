<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comando;
use Illuminate\Http\Request;

class ComandoController extends Controller
{
    public function index()
    {
        $comandos = Comando::whereNull('pet_id')->get();
        return response()->json([
            'success' => true,
            'data' => $comandos,
            'message' => __('comando_entrenamiento.commands_list')
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:especializado,basico',
            'is_favorite' => 'required|boolean',
            'category_id' => 'required|exists:category_comandos,id',
            'voz_comando' => 'required|string|max:255',
            'instructions' => 'required|string',
        ]);

        $comando = Comando::create($request->all());

        return response()->json(['success' => true, 'data' => $comando], 201);
    }

    public function show($id)
    {
        $comando = Comando::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $comando,
            'message' => __('comando_entrenamiento.get_command')
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $comando = Comando::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:especializado,basico',
            'is_favorite' => 'sometimes|boolean',
            'category_id' => 'sometimes|exists:category_comandos,id',
            'voz_comando' => 'sometimes|string|max:255',
            'instructions' => 'sometimes|string',
        ]);

        $comando->update($request->only('name', 'description', 'type', 'is_favorite', 'category_id', 'voz_comando', 'instructions'));

        return response()->json(['success' => true, 'data' => $comando], 200);
    }

    public function destroy($id)
    {
        $comando = Comando::findOrFail($id);
        $comando->delete();

        return response()->json([
            'success' => true,
            'message' => __('comando_entrenamiento.Comando deleted successfully'),
            'data' => $comando
        ], 200);
    }

    public function storeCommandUser(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|in:especializado,basico',
                'is_favorite' => 'required|boolean',
                'category_id' => 'required|exists:category_comandos,id',
                'voz_comando' => 'required|string|max:255',
                'instructions' => 'required|string',
                'pet_id' => 'required|exists:pets,id'
            ]);

            $comando = Comando::create($data);
            return response()->json(['success' => true, 'data' => $comando], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->validator->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCommandUser(Request $request, $id)
    {
        try {
            $comando = Comando::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|in:especializado,basico',
                'is_favorite' => 'required|boolean',
                'category_id' => 'required|exists:category_comandos,id',
                'voz_comando' => 'required|string|max:255',
                'instructions' => 'required|string',
                'pet_id' => 'required|exists:pets,id'
            ]);
            $comando->update($request->only('name', 'description', 'type', 'is_favorite', 'category_id', 'voz_comando', 'instructions'));

            return response()->json(['success' => true, 'data' => $comando], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->validator->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCommandByUser(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|exists:pets,id'
            ]);
            $comandos = Comando::where('pet_id', $data['pet_id'])->get();
            if ($comandos->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No se encontraron comandos para este comando.'], 404);
            }
            return response()->json(['success' => true, 'data' => $comandos], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->validator->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
