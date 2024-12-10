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
            'name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'slug' => "sometimes|required|string|max:255|unique:events,slug,{$eventId}",
            'user_id' => 'sometimes|required|exists:users,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'tipo' => 'sometimes|required|in:salud,entrenamiento',
            'status' => 'sometimes|required|boolean',
            'owner_id' => 'required|array',
            'owner_id.*' => 'required|integer|exists:users,id'
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
