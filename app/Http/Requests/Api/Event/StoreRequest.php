<?php

namespace App\Http\Requests\Api\Event;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'date' => 'required|string',
            'end_date' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:events',
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string|max:255',
            'location' => 'nullable|string',
            'tipo' => 'required|in:medico,entrenamiento,evento',
            'status' => 'required|boolean',
            'pet_id' => 'required|integer',
            'owner_id' => 'required|array',
            'owner_id.*' => 'required|integer|exists:users,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif',

            // Reglas condicionales
            'service_id' => 'nullable|integer|exists:services,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'duration_id' => 'nullable|integer|exists:service_duration,id',
            'training_id' => 'nullable|integer|exists:service_training,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();

            if ($data['tipo'] === 'medico') {
                if (empty($data['service_id']) || empty($data['category_id'])) {
                    $validator->errors()->add('service_id', 'El campo service_id es requerido cuando tipo es "medico".');
                    $validator->errors()->add('category_id', 'El campo category_id es requerido cuando tipo es "medico".');
                }
            }

            if ($data['tipo'] === 'entrenamiento') {
                if (empty($data['duration_id']) || empty($data['training_id'])) {
                    $validator->errors()->add('duration_id', 'El campo duration_id es requerido cuando tipo es "entrenamiento".');
                    $validator->errors()->add('training_id', 'El campo training_id es requerido cuando tipo es "entrenamiento".');
                }
            }
        });
    }
}
