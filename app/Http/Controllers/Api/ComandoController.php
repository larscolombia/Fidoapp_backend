<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comando;
use Illuminate\Http\Request;

class ComandoController extends Controller
{
    public function index()
    {
        $comandos = Comando::all();
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
}
