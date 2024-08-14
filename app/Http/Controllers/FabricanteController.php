<?php

namespace App\Http\Controllers;

use App\Models\Fabricante;
use Illuminate\Http\Request;

class FabricanteController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'nombre' => 'required|string|max:255|unique:fabricantes,nombre',
        ]);

        // Crear un nuevo fabricante
        $fabricante = Fabricante::create([
            'nombre' => $request->input('nombre'),
        ]);

        // Devolver una respuesta JSON con el fabricante creado
        return response()->json([
            'success' => true,
            'fabricante' => $fabricante,
        ]);
    }
}
