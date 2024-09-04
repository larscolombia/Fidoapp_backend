<?php

namespace app\Http\Requests\Api\pets;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class storeRequest extends FormRequest
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
            'breed_id' => 'nullable|exists:breeds,id', // Mantenemos esta regla para IDs
            'breed_name' => 'nullable|string', // Nueva regla para el nombre de la raza
            'size' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight_unit' => 'nullable|string|max:10',
            'height_unit' => 'nullable|string|max:10',
            'user_id' => 'required|exists:users,id',
            'additional_info' => 'nullable|string',
            'status' => 'nullable|boolean',
            'pet_image' => 'nullable', // Nueva regla para la imagen
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

    public function messages()
    {
        return [
            'user_id.exists' => __('pet.user_not_found'),
            'pet_image.image' => __('pet.invalid_image'),
            'pet_image.mimes' => __('pet.invalid_image_format'),
        ];
    }
}
