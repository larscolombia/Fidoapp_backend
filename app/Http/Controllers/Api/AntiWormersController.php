<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Antidesparasitante;
use Illuminate\Http\Request;

class AntiWormersController extends Controller
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

            // Crear la nueva Antidesparasitante asociada a la mascota
            $antiWormer = new Antidesparasitante();
            $antiWormer->pet_id = $request->pet_id;
            $antiWormer->antidesparasitante_name = $request->name;
            $antiWormer->fecha_aplicacion = $request->fecha_aplicacion;
            $antiWormer->fecha_refuerzo_antidesparasitante = $request->fecha_refuerzo;
            $antiWormer->weight = $request->weight;
            $antiWormer->additional_notes = $request->notes;
            $antiWormer->save();
            return response()->json([
                'success' => true,
                'data' => $antiWormer,
                'message' => __('Antidesparasitante creada exitosamente.')
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
            $antiWormer = Antidesparasitante::find($id);
            // Verificar si se encontró la vacuna
            if (!$antiWormer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Antidesparasitante no encontrada.'
                ], 404);
            }

            // Retornar la respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $antiWormer
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

            $antiWormer = Antidesparasitante::find($id);
            // Verificar si se encontró la vacuna
            if (!$antiWormer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Antidesparasitante no encontrada.'
                ], 404);
            }
            // Actualizar los datos de la vacuna
            $antiWormer->antidesparasitante_name = $request->name;
            if(!is_null($request->fecha_aplicacion)){
                $antiWormer->fecha_aplicacion = $request->fecha_aplicacion;
            }
            if(!is_null($request->fecha_refuerzo)){
                $antiWormer->fecha_refuerzo_antidesparasitante = $request->fecha_refuerzo;
            }
            if(!is_null($request->weight)){
                $antiWormer->weight = $request->weight;
            }

            if(!is_null($request->notes)){
                $antiWormer->additional_notes = $request->notes;
            }

            $antiWormer->save();

            // Retornar la respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $antiWormer
            ],201);
        } catch (\Exception $e) {
            // Retornar la respuesta exitosa
            return response()->json([
                'success' => false,
                'data' => $e->getMessage()
            ]);
        }
    }

    public function antiWormersGivenToPet(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|integer|exists:pets,id'
            ]);
            $antiWormer = Antidesparasitante::with('pet')->where('pet_id', $data['pet_id'])->get();
            return response()->json([
                'success' => true,
                'data' => $antiWormer
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
            $antiWormer = Antidesparasitante::find($id);
            // Verificar si se encontró la vacuna
            if (!$antiWormer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Antidesparasitante no encontrada.'
                ], 404);
            }
            $antiWormer->delete();
            return response()->json([
                'success' => true,
                'message' =>  __('Antidesparasitante eliminada exitosamente.')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>  $e->getMessage()
            ], 200);
        }
    }
}
