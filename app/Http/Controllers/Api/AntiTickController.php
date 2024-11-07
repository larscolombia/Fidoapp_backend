<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Antigarrapata;
use Illuminate\Http\Request;

class AntiTickController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validación de los datos recibidos
            $request->validate([
                'antigarrapata_name' => 'required|string|max:255',
                'fecha_aplicacion' => 'required|date',
                'pet_id' => 'required|exists:pets,id',
                'fecha_refuerzo_antigarrapata' => 'required|date|after_or_equal:fecha_aplicacion',
            ]);

            // Crear la nueva Antigarrapata asociada a la mascota
            $antiTick = new Antigarrapata();
            $antiTick->pet_id = $request->pet_id;
            $antiTick->antigarrapata_name = $request->antigarrapata_name;
            $antiTick->fecha_aplicacion = $request->fecha_aplicacion;
            $antiTick->fecha_refuerzo_antigarrapata = $request->fecha_refuerzo_antigarrapata;
            $antiTick->save();
            return response()->json([
                'success' => true,
                'message' => __('Antigarrapata creada exitosamente.')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $antiTick = Antigarrapata::find($id);
            // Verificar si se encontró la Antigarrapata
            if (!$antiTick) {
                return response()->json([
                    'success' => false,
                    'message' => 'Antigarrapata no encontrada.'
                ], 404);
            }

            // Retornar la respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $antiTick
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request,$id)
    {
        try {
            // Validar los datos actualizados
            $request->validate([
                'antigarrapata_name' => 'required|string|max:255',
                'fecha_aplicacion' => 'required|date',
                'fecha_refuerzo_antigarrapata' => 'required|date|after_or_equal:fecha_aplicacion',
            ]);

            $antiTick = Antigarrapata::find($id);
            // Verificar si se encontró la Antigarrapata
            if (!$antiTick) {
                return response()->json([
                    'success' => false,
                    'message' => 'Antigarrapata no encontrada.'
                ], 404);
            }
            // Actualizar los datos de la Antigarrapata
            $antiTick->antigarrapata_name = $request->antigarrapata_name;
            $antiTick->fecha_aplicacion = $request->fecha_aplicacion;
            $antiTick->fecha_refuerzo_antigarrapata = $request->fecha_refuerzo_antigarrapata;
            $antiTick->save();

            // Retornar la respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $antiTick
            ]);
        } catch (\Exception $e) {
            // Retornar la respuesta exitosa
            return response()->json([
                'success' => false,
                'data' => $e->getMessage()
            ]);
        }
    }

    public function antiTickGivenToPet(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|integer|exists:pets,id'
            ]);
            $antiTick = Antigarrapata::with('pet')->where('pet_id', $data['pet_id'])->get();
            return response()->json([
                'success' => true,
                'data' => $antiTick
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $antiTick = Antigarrapata::find($id);
            // Verificar si se encontró la Antigarrapata
            if (!$antiTick) {
                return response()->json([
                    'success' => false,
                    'message' => 'Antigarrapata no encontrada.'
                ], 404);
            }
            $antiTick->delete();
            return response()->json([
                'success' => true,
                'message' =>  __('Antigarrapata eliminada exitosamente.')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>  $e->getMessage()
            ], 200);
        }
    }
}
