<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpecialCondition;
use Illuminate\Http\Request;

class SpecialConditionController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'allergies' => 'nullable|string|max:255',
                'chronic_diseases' => 'nullable|string|max:255',
                'disabilities' => 'nullable|string|max:255',
                'food_needs' => 'nullable|string|max:255',
                'medications' => 'nullable|string|max:255'
            ]);

            $SpecialCondition = SpecialCondition::create($data);
            return response()->json([
                'success' => true,
                'message' => __('record created successfully'),
                'data' => $SpecialCondition
            ],200);
        }catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => __('validation error'),
                    'errors' => $e->validator->errors()
                ], 422);
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
            $data = $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'allergies' => 'nullable|string|max:255',
                'chronic_diseases' => 'nullable|string|max:255',
                'disabilities' => 'nullable|string|max:255',
                'food_needs' => 'nullable|string|max:255',
                'medications' => 'nullable|string|max:255'
            ]);

            $SpecialCondition = SpecialCondition::find($id);
            if(!$SpecialCondition){
                return response()->json([
                    'success' => false,
                    'message' => __('Record not found')
                ]);
            }
            $SpecialCondition->update($data);
            return response()->json([
                'success' => true,
                'message' => __('record updated successfully'),
                'data' => $SpecialCondition
            ],200);
        }catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => __('validation error'),
                    'errors' => $e->validator->errors()
                ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        try {
            $SpecialCondition = SpecialCondition::find($id);
            // Verificar si se la condicion
            if (!$SpecialCondition) {
                return response()->json([
                    'success' => false,
                    'message' => __('There is no special condition')
                ], 404);
            }

            // Retornar la respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $SpecialCondition
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
            $SpecialCondition = SpecialCondition::find($id);
            // Verificar si se encontró la vacuna
            if (!$SpecialCondition) {
                return response()->json([
                    'success' => false,
                    'message' => __('There is no special condition')
                ], 404);
            }
            $SpecialCondition->delete();
            return response()->json([
                'success' => true,
                'message' =>  __('Special condition successfully removed')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>  $e->getMessage()
            ], 200);
        }
    }
}
