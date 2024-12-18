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
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'tipo' => 'required|in:medico,entrenamiento,evento',
            'status' => 'required|boolean',
            'pet_id' => 'required|integer',
            'owner_id' => 'required|array',
            'owner_id.*' => 'required|integer|exists:users,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
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
}
