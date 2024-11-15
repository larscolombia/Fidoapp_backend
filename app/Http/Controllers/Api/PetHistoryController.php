<?php

namespace App\Http\Controllers\Api;

use App\Models\PetHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PetHistoryController extends Controller
{

    public function medicalHistoryPerPet(Request $request)
    {
        try {

            $data = $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'search' => 'nullable|string',
            ]);
            $query = PetHistory::with(['pet', 'pet.vacunas', 'pet.antidesparasitantes', 'pet.antigarrapatas', 'veterinarian'])
                ->where('pet_id', $data['pet_id']);

            // Si hay un término de búsqueda, aplica filtros
            if (!empty($data['search'])) {
                $query->where(function ($q) use ($data) {
                    $q->where('medical_conditions', 'like', '%' . $data['search'] . '%')
                        ->orWhere('test_results', 'like', '%' . $data['search'] . '%')
                        ->orWhereHas('pet.vacunas', function ($q) use ($data) {
                            $q->where('vacuna_name', 'like', '%' . $data['search'] . '%');
                        })
                        ->orWhereHas('pet.antidesparasitantes', function ($q) use ($data) {
                            $q->where('antidesparasitante_name', 'like', '%' . $data['search'] . '%');
                        })
                        ->orWhereHas('pet.antigarrapatas', function ($q) use ($data) {
                            $q->where('antigarrapata_name', 'like', '%' . $data['search'] . '%');
                        });
                });
            }

            // Obtención de historiales paginados
            $histories = $query->get();

            return response()->json([
                'success' => true,
                'data' => $histories,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pet not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'veterinarian_id' => 'required|exists:users,id',
                'vacuna_id' => 'nullable|integer',
                'antidesparasitante_id' => 'nullable|integer',
                'antigarrapata_id' => 'nullable|integer',
                'medical_conditions' => 'nullable|string',
                'test_results' => 'nullable|string',
                'vet_visits' => 'nullable|integer',
                'category' => 'nullable|integer',
                'date' => 'nullable|date',
                'name' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);
            $data = $request->all();
              // Manejar el archivo si se proporciona
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('pet_histories'), $fileName);
            $data['file'] = 'pet_histories/' . $fileName;
        }

        // Manejar la imagen si se proporciona
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('pet_histories'), $imageName);
            $data['image'] = 'pet_histories/' . $imageName;
        }

        // Crear el historial con los datos procesados
        $history = PetHistory::create($data);
            return response()->json([
                'success' => true,
                'data' => $history
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $history = PetHistory::with(['pet', 'pet.vacunas', 'pet.antidesparasitantes', 'pet.antigarrapatas', 'veterinarian'])->find($id);
        if (!$history) {
            return response()->json(['message' => 'History not found'], 404);
        }
        return response()->json($history);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // Buscar el historial por ID
            $history = PetHistory::find($id);
            if (!$history) {
                return response()->json(['message' => 'Historial no encontrado'], 404);
            }

            // Validar los datos entrantes
            $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'veterinarian_id' => 'required|exists:users,id',
                'vacuna_id' => 'nullable|integer',
                'antidesparasitante_id' => 'nullable|integer',
                'antigarrapata_id' => 'nullable|integer',
                'medical_conditions' => 'nullable|string',
                'test_results' => 'nullable|string',
                'vet_visits' => 'nullable|integer',
                'category' => 'nullable|integer',
                'date' => 'nullable|date',
                'name' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Obtener todos los datos del request
            $data = $request->all();

            if ($request->hasFile('file')) {
                // Eliminar el archivo anterior si existe
                if ($history->file && file_exists(public_path($history->file))) {
                    unlink(public_path($history->file)); // Elimina el archivo anterior
                }
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('pet_histories'), $fileName);
                $data['file'] = 'pet_histories/' . $fileName; // Guarda la nueva ruta
            }

            // Manejar la imagen si se proporciona
            if ($request->hasFile('image')) {
                // Eliminar la imagen anterior si existe
                if ($history->image && file_exists(public_path($history->image))) {
                    unlink(public_path($history->image)); // Elimina la imagen anterior
                }
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('pet_histories'), $imageName);
                $data['image'] = 'pet_histories/' . $imageName; // Guarda la nueva ruta
            }

            // Actualizar el historial con los nuevos datos
            $history->update($data);

            return response()->json([
                'success' => true,
                'data' => $history
            ], 200); // Cambiar a 200 para indicar una actualización exitosa
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el historial: ' . $e->getMessage()
            ], 500);
        }
    }

    public function petClinicalHistoryForOwner(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'pet_id' => 'required|exists:pets,id',
            ]);
            $history = PetHistory::with(['pet', 'pet.user'])
                ->whereHas('pet', function ($q) use ($data) {
                    return $q->where('user_id', $data['user_id'])
                        ->where('id', $data['pet_id']);
                })
                ->get();
            return response()->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $history = PetHistory::find($id);
        if (!$history) {
            return response()->json(['message' => 'History not found'], 404);
        }

        $history->delete();
        return response()->json(['message' => 'Successfully deleted history']);
    }
}
