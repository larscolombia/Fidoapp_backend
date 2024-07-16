<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clase;
use App\Models\Ejercicio;
use Illuminate\Http\Request;

class EjercicioController extends Controller
{
    public function index($claseId)
    {
        $clase = Clase::findOrFail($claseId);
        $ejercicios = $clase->ejercicios;
        return response()->json([
            'success' => true,
            'message' => 'Ejercicios recuperados exitosamente',
            'data' => $ejercicios,
        ]);
    }

    public function store(Request $request, $claseId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'required|url',
        ]);

        $ejercicio = Ejercicio::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'clase_id' => $claseId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ejercicio creado exitosamente',
            'data' => $ejercicio,
        ]);
    }

    public function show($claseId, $id)
    {
        $ejercicio = Ejercicio::where('clase_id', $claseId)->findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Ejercicio recuperado exitosamente',
            'data' => $ejercicio,
        ]);
    }

    public function update(Request $request, $claseId, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'required|url',
        ]);

        $ejercicio = Ejercicio::where('clase_id', $claseId)->findOrFail($id);
        $ejercicio->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ejercicio actualizado exitosamente',
            'data' => $ejercicio,
        ]);
    }

    public function destroy($claseId, $id)
    {
        $ejercicio = Ejercicio::where('clase_id', $claseId)->findOrFail($id);
        $ejercicio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ejercicio eliminado exitosamente',
        ]);
    }
}
