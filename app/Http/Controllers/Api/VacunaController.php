<?php

namespace App\Http\Controllers\Api;

use App\Models\Vacuna;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VacunaController extends Controller
{

    public function store(Request $request)
    {
        try {
            // Validación de los datos recibidos
            $request->validate([
                'name' => 'required|string|max:255',
                'fecha_aplicacion' => 'required|date',
                'pet_id' => 'required|exists:pets,id',
                'fecha_refuerzo' => 'required|date|after_or_equal:fecha_aplicacion',
                'weight' => 'nullable|string',
                'notes' => 'nullable|string'
            ]);

            // Crear la nueva vacuna asociada a la mascota
            $vacuna = new Vacuna();
            $vacuna->pet_id = $request->pet_id;
            $vacuna->vacuna_name = $request->name;
            $vacuna->fecha_aplicacion = $request->fecha_aplicacion;
            $vacuna->fecha_refuerzo_vacuna = $request->fecha_refuerzo;
            $vacuna->weight = $request->weight;
            $vacuna->additional_notes = $request->notes;
            $vacuna->save();
            return response()->json([
                'success' => true,
                'data' => $vacuna,
                'message' => __('Vacuna creada exitosamente.')
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
            $vacuna = Vacuna::find($id);
            // Verificar si se encontró la vacuna
            if (!$vacuna) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vacuna no encontrada.'
                ], 404);
            }

            // Retornar la respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $vacuna
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
                'name' => 'required|string|max:255',
                'fecha_aplicacion' => 'nullable|date',
                'fecha_refuerzo' => 'nullable|date|after_or_equal:fecha_aplicacion',
                'weight' => 'nullable|string',
                'notes' => 'nullable|string'
            ]);

            $vacuna = Vacuna::find($id);
            // Verificar si se encontró la vacuna
            if (!$vacuna) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vacuna no encontrada.'
                ], 404);
            }
            // Actualizar los datos de la vacuna
            $vacuna->vacuna_name = $request->name;
            if(!is_null($request->fecha_aplicacion)){
                $vacuna->fecha_aplicacion = $request->fecha_aplicacion;
            }

            if(!is_null($request->fecha_refuerzo)){
                $vacuna->fecha_refuerzo_vacuna = $request->fecha_refuerzo;
            }

            if(!is_null($request->weight)){
                $vacuna->weight = $request->weight;
            }
            if(!is_null($request->notes)){
                $vacuna->additional_notes = $request->notes;
            }

            $vacuna->save();

            // Retornar la respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $vacuna
            ],201);
        } catch (\Exception $e) {
            // Retornar la respuesta exitosa
            return response()->json([
                'success' => false,
                'data' => $e->getMessage()
            ]);
        }
    }

    public function vaccinesGivenToPet(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|integer|exists:pets,id'
            ]);
            $vacunas = Vacuna::with('pet')->where('pet_id', $data['pet_id'])->get();
            return response()->json([
                'success' => true,
                'data' => $vacunas
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
            $vacuna = Vacuna::find($id);
            // Verificar si se encontró la vacuna
            if (!$vacuna) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vacuna no encontrada.'
                ], 404);
            }
            $vacuna->delete();
            return response()->json([
                'success' => true,
                'message' =>  __('Vacuna eliminada exitosamente.')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>  $e->getMessage()
            ], 200);
        }
    }
}
