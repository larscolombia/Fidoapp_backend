<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrainingDiaryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required',
            'actividad' => 'required|string',
            'notas' => 'string',
            'pet_id' => 'required|exists:pets,id',
            'image' => 'sometimes',
        ]);


        if (!empty($request['image'])) {
            // $media = $pet->addMediaFromUrl($request['pet_image'])->toMediaCollection('pet_image');
            // storeMediaFile($pet, $request->file('pet_image'), 'pet_image');
        }
    }
}
