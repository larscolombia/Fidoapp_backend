<?php

namespace App\Http\Controllers\Api;

use App\Models\Diario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrainingDiaryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'date' => 'required|date',
                'actividad' => 'required|string',
                'notas' => 'string',
                'category_id' => 'required|integer',
                'pet_id' => 'required|exists:pets,id',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if(!isset($request->image)){
                $data['image'] = null;
            }
            // Manejo de la imagen del diario
            if (!is_null($data['image']) && $request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = 'images/training_diary/' . $imageName;

                // Mueve la imagen a la carpeta public/images/training_diary
                $image->move(public_path('images/training_diary'), $imageName);
            }

            // Crear la nueva mascota
            $trainingDiary = Diario::create($data);

            if (!is_null($data['image'])) {
                storeMediaFile($trainingDiary, $data['image'], 'image');
            }
            return response()->json([
                'success' => true,
                'message' => __('Record created successfully'),
                'data' => $trainingDiary
            ], 201);
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
            $trainingDiary = Diario::find($id);
            // Verificar si se encontró el diario
            if (!$trainingDiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            // Retornar la respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $trainingDiary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'date' => 'required|date',
                'actividad' => 'required|string',
                'notas' => 'string',
                'category_id' => 'required|integer',
                'pet_id' => 'required|exists:pets,id',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if(!isset($request->image)){
                $data['image'] = null;
            }
            $trainingDiary = Diario::find($id);
            if (!$trainingDiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }
            // Manejo de la imagen del diario
            if (!is_null($data['image']) && $request->hasFile('image')) {
                $imagePath = $this->handleImageUpload($request->file('image'));
            }

            if (!is_null($data['image']) && !empty($request['image'])) {
                storeMediaFile($trainingDiary, $request->file('image'), 'image');
            }

            $trainingDiary->update($data);

            return response()->json([
                'success' => true,
                'message' => __('Updated diary'),
                'data' => $trainingDiary
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $trainingDiary = Diario::find($id);
            // Verificar si se encontró el diario
            if (!$trainingDiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diary not found.'
                ], 404);
            }
            $trainingDiary->delete();
            return response()->json([
                'success' => true,
                'message' =>  __('Diary successfully deleted')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>  $e->getMessage()
            ], 200);
        }
    }

    private function handleImageUpload($image)
    {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = 'images/training_diary/' . $imageName;

        // Mueve la imagen a la carpeta public/images/training_diary
        $image->move(public_path('images/training_diary'), $imageName);

        return $imagePath;
    }
}
