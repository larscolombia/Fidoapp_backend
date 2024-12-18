<?php

namespace App\Http\Requests\Api\Event;

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
        $eventId = $this->route('event');

        return [
            'name' => 'sometimes|string|max:255',
            'date' => 'sometimes|string',
            'end_date' => 'sometimes|string',
            'slug' => "sometimes|string|max:255|unique:events,slug,{$eventId}",
            'user_id' => 'sometimes|exists:users,id',
            'description' => 'sometimes|string',
            'location' => 'sometimes|string',
            'tipo' => 'sometimes|in:medico,entrenamiento,evento',
            'status' => 'sometimes|boolean',
            'owner_id' => 'sometimes|array',
            'owner_id.*' => 'sometimes|integer|exists:users,id',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png',
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
