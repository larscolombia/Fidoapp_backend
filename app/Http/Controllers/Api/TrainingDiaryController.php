<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Diario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TrainingDiaryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|exists:pets,id',
            ]);
            $trainingDiaries = Diario::where('pet_id', $data['pet_id'])->get();
            $formattedDiaries = $trainingDiaries->map(function ($trainingDiary) {
                $formattedDate = date("d-m-Y", strtotime($trainingDiary->date));
                return [
                    'id' => $trainingDiary->id,
                    'category_id' => $trainingDiary->category_id,
                    'category_name' => $trainingDiary->category->name,
                    'date' => $formattedDate,
                    'actividad' => $trainingDiary->actividad,
                    'notas' => $trainingDiary->notas,
                    'pet_id' => $trainingDiary->pet_id,
                    'image' => $trainingDiary->image,
                    'created_at' => $trainingDiary->created_at,
                    'updated_at' => $trainingDiary->updated_at
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $formattedDiaries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'date' => 'required|string',
                'actividad' => 'required|string|max:255',
                'notas' => 'nullable|string|max:255',
                'category_id' => 'required|integer',
                'pet_id' => 'required|exists:pets,id',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,bmp,svg',
            ]);
            try {
                $data['date'] = Carbon::createFromFormat('Y-m-d', $data['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $data['date'] = Carbon::now()->format('Y-m-d');
            }
            if (!isset($request->image)) {
                $data['image'] = null;
            }
            if (!file_exists(public_path('images/training_diary'))) {
                mkdir(public_path('images/training_diary'), 0755, true);
            }
            // Manejo de la imagen del diario
            if (!is_null($data['image']) && $request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalName();
                $imagePath = 'images/training_diary/' . $imageName;

                // Mueve la imagen a la carpeta public/images/training_diary
                $image->move(public_path('images/training_diary'), $imageName);
                $data['image'] = $imagePath;
            }

            // Crear la nueva mascota
            $trainingDiary = Diario::create($data);

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
            $formattedDate = date("d-m-Y", strtotime($trainingDiary->date));
            $data = [
                'id' => $trainingDiary->id,
                'category_id' => $trainingDiary->category_id,
                'category_name' => $trainingDiary->category->name,
                'date' =>  $formattedDate,
                'actividad' => $trainingDiary->actividad,
                'notas' => $trainingDiary->notas,
                'pet_id' => $trainingDiary->pet_id,
                'image' => $trainingDiary->image,
                'created_at' => $trainingDiary->created_at,
                'updated_at' => $trainingDiary->updated_at
            ];
            // Retornar la respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDiario(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|exists:pets,id',
            ]);
            $trainingDiaries = Diario::where('pet_id', $data['pet_id'])->get();
            $formattedDiaries = $trainingDiaries->map(function ($trainingDiary) {
                $formattedDate = date("d-m-Y", strtotime($trainingDiary->date));
                return [
                    'id' => $trainingDiary->id,
                    'category_id' => $trainingDiary->category_id,
                    'category_name' => $trainingDiary->category->name,
                    'date' => $formattedDate,
                    'actividad' => $trainingDiary->actividad,
                    'notas' => $trainingDiary->notas,
                    'pet_id' => $trainingDiary->pet_id,
                    'image' => $trainingDiary->image,
                    'created_at' => $trainingDiary->created_at,
                    'updated_at' => $trainingDiary->updated_at
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $formattedDiaries
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
                'date' => 'sometimes|string',
                'actividad' => 'sometimes|string|max:255',
                'notas' => 'sometimes|string|max:255',
                'category_id' => 'sometimes|integer',
                'pet_id' => 'sometimes|exists:pets,id',
                'image' => 'sometimes|image|mimes:jpg,jpeg,png,gif,bmp,svg',
            ]);


            $trainingDiary = Diario::find($id);
            if (!$trainingDiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }
            try {
                $data['date'] = Carbon::createFromFormat('Y-m-d', $data['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $data['date'] = $trainingDiary->date;
            }
            if (!file_exists(public_path('images/training_diary'))) {
                mkdir(public_path('images/training_diary'), 0755, true);
            }
            if ($request->hasFile('image')) {
                // Elimina la imagen anterior si existe
                if ($trainingDiary->image) {
                    $previousImagePath = public_path($trainingDiary->image);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }

                // Procesa la nueva imagen
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalName();
                $imagePath = 'images/training_diary/' . $imageName;

                // Mueve la nueva imagen a la carpeta public/images/training_diary
                $image->move(public_path('images/training_diary'), $imageName);

                // Actualiza el campo de imagen en los datos
                $data['image'] = $imagePath;
            } else {
                $data['image'] = $trainingDiary->image;
            }

            $trainingDiary->update([
                'date' => $data['date'],
                'actividad' => $request->input('actividad', $trainingDiary->actividad),
                'notas' => $request->input('notas', $trainingDiary->notas),
                'category_id' => $request->input('category_id', $trainingDiary->category_id),
                'pet_id' => $request->input('pet_id', $trainingDiary->pet_id),
                'image' => $data['image'],
            ]);

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
