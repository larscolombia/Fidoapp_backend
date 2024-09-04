<?php

namespace app\Http\Requests\api\pets;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'breed_id' => 'sometimes|exists:breeds,id',
            'breed_name' => 'sometimes|string',
            'size' => 'sometimes|string|max:50',
            'date_of_birth' => 'sometimes|date',
            'age' => 'sometimes|string|max:50',
            'gender' => 'sometimes|in:male,female',
            'weight' => 'sometimes|numeric',
            'height' => 'sometimes|numeric',
            'weight_unit' => 'sometimes|string|max:10',
            'height_unit' => 'sometimes|string|max:10',
            'user_id' => 'sometimes|exists:users,id',
            'additional_info' => 'sometimes|string',
            'status' => 'sometimes|boolean',
            'pet_image' => 'sometimes',
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
            'user_id.exists' => __('validation.user_not_found'),
            'pet_image.image' => __('validation.invalid_image'),
            'pet_image.mimes' => __('validation.invalid_image_format'),
            'breed_id.exists' => __('validation.invalid_breed'),
            'breed_name.string' => __('validation.invalid_breed'),
        ];
    }
}
