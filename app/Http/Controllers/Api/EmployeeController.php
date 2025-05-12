<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Models\EmployeeRating;

class EmployeeController extends Controller
{
    public function ratingUserStore(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'employee_id' => 'required|exists:users,id',
                'review_msg' => 'required|string|max:255',
                'rating' => 'required|numeric|min:1|max:5'
            ]);

            if (is_null($data['rating'])) {
                $data['rating'] = 1;
            }
            if ($data['rating'] >= 3) {
                $data['status'] = 1;
            }

            // Crear la calificación del empleado
            $employeeRating = EmployeeRating::create($data);

            return response()->json([
                'success' => true,
                'data' => $employeeRating,
                'message' => $employeeRating->status == 0 ? __('messages.comment_review') : null
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la calificación.'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateRaiting(Request $request, $id)
    {
        try {
            // Validar solo los campos que se van a actualizar
            $data = $request->validate([
                'review_msg' => 'nullable|string|max:255',
                'rating' => 'nullable|numeric|min:1|max:5'
            ]);

            // Buscar la calificación del empleado
            $employeeRating = EmployeeRating::find($id);

            // Verificar si se encontró el registro
            if (!$employeeRating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Calificación no encontrada.'
                ], 404);
            }

            // Actualizar los campos
            $employeeRating->update($data);

            return response()->json([
                'success' => true,
                'data' => $employeeRating
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getRating(Request $request)
    {
        // Validación más estricta
        $data = $request->validate([
            'user_id' => 'nullable|integer',
            'employee_id' => 'nullable|integer',
        ]);

        // Inicializar la consulta
        $query = EmployeeRating::query();

        // Agregar condiciones dinámicamente
        if (isset($data['user_id']) && !is_null($data['user_id'])) {
            $query->where('user_id', $data['user_id']);
        }

        if (isset($data['employee_id']) && !is_null($data['employee_id'])) {
            $query->where('employee_id', $data['employee_id']);
        }

        // Ejecutar la consulta
        $employeeRating = $query->where('status', 1)->get();

        // Manejo de errores: si no se encuentra ninguna calificación
        if ($employeeRating->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No ratings found for the specified user and employee.'
            ], 404);
        }
        $formattedRatings = $employeeRating->map(function ($rating) {
            return [
                'id' => $rating->id,
                'employee_id' => $rating->employee_id,
                'review_msg' => $rating->review_msg,
                'rating' => $rating->rating,
                'user_id' => $rating->user_id,
                'created_at' => $rating->created_at,
                'user_full_name' => optional($rating->user)->full_name ?? default_user_name(),
                'user_avatar' => !is_null(optional($rating->user)->media->pluck('original_url')->first()) ? optional($rating->user)->media->pluck('original_url')->first() : asset('images/default/default.jpg'),
            ];
        });
        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'data' => $formattedRatings
        ]);
    }

    public function destroyRaiting($id)
    {
        try {
            $employeeRating = EmployeeRating::find($id);
            // Verificar si se encontró el raiting
            if (! $employeeRating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Raiting no encontrada.'
                ], 404);
            }
            $employeeRating->delete();
            return response()->json([
                'success' => true,
                'message' =>  __('Raiting eliminado exitosamente.')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>  $e->getMessage()
            ], 200);
        }
    }
}
