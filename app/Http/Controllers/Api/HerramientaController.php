<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HerramientaType;
use Illuminate\Http\Request;
use App\Models\Herramienta;

class HerramientaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'nullable|string|in:Silbato,Diarios,Clicker',
            'type_id' => 'nullable|exists:herramientas_entrenamiento_type,id',
            'audio' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Verificar que se haya proporcionado 'type' o 'type_id'
        if (!$request->filled('type') && !$request->filled('type_id')) {
            return response()->json(['error' => 'Debe proporcionar un type o type_id.'], 400);
        }

        if ($request->filled('type')) {
            $type = HerramientaType::firstOrCreate(
                ['type' => $request->input('type')],
                ['icon' => $request->input('icon', null)]
            );
            $type_id = $type->id;
        } else {
            $type_id = $request->input('type_id');
        }

        $herramienta = Herramienta::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type_id' => $type_id,
            'audio' => $request->input('audio'),
            'status' => $request->input('status'),
        ]);

        return response()->json(['data' => $herramienta], 201);
    }

    public function update(Request $request, $id)
    {
        $herramienta = Herramienta::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|nullable|string|in:Silbato,Diarios,Clicker',
            'type_id' => 'sometimes|nullable|exists:herramientas_entrenamiento_type,id',
            'audio' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        // Verificar que se haya proporcionado 'type' o 'type_id'
        if (!$request->filled('type') && !$request->filled('type_id')) {
            return response()->json(['error' => 'Debe proporcionar un type o type_id.'], 400);
        }

        if ($request->filled('type')) {
            $type = HerramientaType::firstOrCreate(
                ['type' => $request->input('type')],
                ['icon' => $request->input('icon', null)]
            );
            $herramienta->type_id = $type->id;
        } elseif ($request->filled('type_id')) {
            $herramienta->type_id = $request->input('type_id');
        }

        $herramienta->update($request->only(['name', 'description', 'audio', 'status']));

        return response()->json(['data' => $herramienta]);
    }

    public function show($id)
    {
        $herramienta = Herramienta::with('type')->findOrFail($id);
        return response()->json(['data' => $herramienta]);
    }

    public function index()
    {
        $herramientas = Herramienta::with('type')->get();
        return response()->json(['data' => $herramientas]);
    }

    public function destroy($id)
    {
        $herramienta = Herramienta::findOrFail($id);
        $herramienta->delete();

        return response()->json(['message' => 'Herramienta de entrenamiento eliminada correctamente.']);
    }
}
