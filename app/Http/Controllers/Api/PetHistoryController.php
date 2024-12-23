<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\PetHistory;
use App\Trait\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PetHistoryController extends Controller
{
    use Notification;

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
            // Filtrando y estructurando la respuesta
            $formattedHistories = $histories->map(function ($history) {
                $reportData = $this->getReportData($history);
                return [
                    'id' => $history->id,
                    'report_name' => $history->name,
                    'report_type' =>  $reportData['report_type'],
                    'application_date' => $history->application_date ? \Carbon\Carbon::parse($history->application_date)->format('d-m-Y') : null,
                    'medical_conditions' => $history->medical_conditions,
                    'test_results' => $history->test_results,
                    'vet_visits' => $history->vet_visits,
                    'file' => !is_null($history->file) ? asset($history->file) : null,
                    'image' => !is_null($history->image) ? asset($history->image) : null,
                    // Filtrando pet
                    'pet_id' => $history->pet->id,
                    'pet_name' => $history->pet->name,
                    // Filtrando veterinarian
                    'veterinarian_id' => $history->veterinarian->id,
                    'veterinarian_name' => $history->veterinarian->full_name,
                    //filtrato tipo de reporte
                    'category' => isset($history->category_rel) ? $history->category_rel->id : null,
                    'category_name' => isset($history->category_rel) && !is_null($history->category_rel) ? $history->category_rel->name : null,
                    'detail_history_id' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['id']) ? $reportData['detail_type']['id'] : null,
                    'detail_history_name' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['name']) ? $reportData['detail_type']['name'] : null,
                    'fecha_aplicacion' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['fecha_aplicacion'])
                        ? \Carbon\Carbon::parse($reportData['detail_type']['fecha_aplicacion'])->format('d-m-Y')
                        : null,
                    'fecha_refuerzo' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['fecha_refuerzo'])
                        ? \Carbon\Carbon::parse($reportData['detail_type']['fecha_refuerzo'])->format('d-m-Y')
                        : null,
                    'weight' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['weight']) ? $reportData['detail_type']['weight'] : null,
                    'notes' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['notes'])  ? $reportData['detail_type']['notes'] : null,
                   'created_at' => Carbon::parse($history->created_at)->format('d-m-Y'),
                    'updated_at' => Carbon::parse($history->updated_at)->format('d-m-Y'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedHistories,
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
                'report_type' => 'required|integer|between:1,3',
                'veterinarian_id' => 'required|exists:users,id',
                'application_date' => 'nullable|date',
                'medical_conditions' => 'nullable|string',
                'test_results' => 'nullable|string',
                'vet_visits' => 'nullable|integer',
                'category' => 'nullable|integer',
                'date' => 'nullable|string',
                'report_name' => 'required|string',
                'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'image' => 'nullable|image|mimes:jpg,jpeg,png',
                'name' => 'required|string|max:255',
                'fecha_aplicacion' => 'required|date',
                'fecha_refuerzo' => 'required|date|after_or_equal:fecha_aplicacion',
                'weight' => 'nullable|string',
                'notes' => 'nullable|string'
            ]);
            $request->merge(['method' => 'store']);
            $data = $request->all();
            try {
                $data['date'] = Carbon::createFromFormat('Y-m-d', $data['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $data['date'] = Carbon::now()->format('Y-m-d');
            }
            // Asignar IDs según el tipo de reporte
            if (isset($data['report_type'])) {
                if ($data['report_type'] === 1) {
                    $data['vacuna_id'] = $this->createReportType($data);
                } elseif ($data['report_type'] === 2) {
                    $data['antidesparasitante_id'] = $this->createReportType($data);
                } elseif ($data['report_type'] === 3) {
                    $data['antigarrapata_id'] = $this->createReportType($data);
                }
            }
            if (!file_exists(public_path('images/pet_histories'))) {
                mkdir(public_path('images/pet_histories'), 0755, true);
            }
            if (!file_exists(public_path('files/pet_histories'))) {
                mkdir(public_path('files/pet_histories'), 0755, true);
            }
            // Manejar el archivo si se proporciona
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '.' . $file->getClientOriginalName();
                $file->move(public_path('files/pet_histories'), $fileName);
                $filePath = 'files/pet_histories/' . $fileName;
                $data['file'] = 'pet_histories/' . $filePath;
            }


            // Manejar la imagen si se proporciona
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalName();
                $image->move(public_path('images/pet_histories'), $imageName);
                $imagePath = 'images/pet_histories/' . $imageName;
                $data['image'] = $imagePath;
            }
            $data['name'] = $data['report_name'];
            // Crear el historial con los datos procesados
            $history = PetHistory::create($data);
            //notify
            //$this->sendNotification('pet_histories', $history, [$request->input('veterinarian_id')], __('pet.pet_history_create'));
            // $this->sendNotification('history',$history,'history');
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
        $history = PetHistory::find($id);
        if (!$history) {
            return response()->json(['message' => 'History not found'], 404);
        }
        $reportData = $this->getReportData($history);
        $response = [
            'report_id' => $history->id,
            'report_name' => $history->name,
            'report_type' =>  $reportData['report_type'],
            'application_date' => $history->application_date ? \Carbon\Carbon::parse($history->application_date)->format('d-m-Y') : null,
            'pet_id' => $history->pet->id,
            'pet_name' => $history->pet->name,
            'category_id' => $history->category,
            'owner_id' => $history->pet->owner->id,
            'owner_name' => $history->pet->owner->full_name,
            'owner_avatar' => !is_null($history->pet->owner->avatar) && !empty($history->pet->owner->avatar) ? asset($history->pet->owner->avatar) : null,
            'category' => isset($history->category_rel) ? $history->category_rel->id : null,
            'category_name' => isset($history->category_rel) && !is_null($history->category_rel) ? $history->category_rel->name : null,
            'detail_history_id' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['id']) ? $reportData['detail_type']['id'] : null,
            'detail_history_name' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['name']) ? $reportData['detail_type']['name'] : null,
            'fecha_aplicacion' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['fecha_aplicacion'])
                ? \Carbon\Carbon::parse($reportData['detail_type']['fecha_aplicacion'])->format('d-m-Y')
                : null,
            'fecha_refuerzo' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['fecha_refuerzo'])
                ? \Carbon\Carbon::parse($reportData['detail_type']['fecha_refuerzo'])->format('d-m-Y')
                : null,
            'weight' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['weight']) ? $reportData['detail_type']['weight'] : null,
            'notes' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['notes'])  ? $reportData['detail_type']['notes'] : null,
            'veterinarian_id' => $history->veterinarian->id,
            'veterinarian_name' => $history->veterinarian->full_name,
            'medical_conditions' => $history->medical_conditions,
            'test_results' => $history->test_results,
            'vet_visits' => $history->vet_visits,
            'file' => !is_null($history->file) ? asset($history->file) : null,
            'image' => !is_null($history->image) ? asset($history->image) : null,
            'created_at' => Carbon::parse($history->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($history->updated_at)->format('d-m-Y'),
        ];
        return response()->json($response);
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
                'pet_id' => 'sometimes|exists:pets,id',
                'report_type' => 'sometimes|integer|between:1,3',
                'veterinarian_id' => 'sometimes|exists:users,id',
                'detail_history_id' => 'sometimes|integer',
                'application_date' => 'sometimes|date',
                // 'medical_conditions' => 'sometimes|string',
                'test_results' => 'sometimes|string',
                'vet_visits' => 'sometimes|integer',
                'category' => 'sometimes|integer',
                'date' => 'sometimes|date',
                'report_name' => 'sometimes|string',
                'file' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
                'name' => 'sometimes|string|max:255',
                'fecha_aplicacion' => 'sometimes|date',
                'fecha_refuerzo' => 'sometimes|date|after_or_equal:fecha_aplicacion',
                'weight' => 'sometimes|string',
                'notes' => 'sometimes|string'
            ]);

            if (!file_exists(public_path('images/pet_histories'))) {
                mkdir(public_path('images/pet_histories'), 0755, true);
            }
            if (!file_exists(public_path('files/pet_histories'))) {
                mkdir(public_path('files/pet_histories'), 0755, true);
            }

            // Obtener todos los datos del request
            $request->merge(['method' => 'update']);
            $data = $request->all();
            if (isset($data['date'])) {
                try {
                    $data['date'] = Carbon::createFromFormat('Y-m-d', $data['date'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $data['date'] = Carbon::now()->format('Y-m-d');
                }
            }


            if (isset($data['report_type']) && isset($data['detail_history_id'])) {
                if ($data['report_type'] === 1) {
                    $data['vacuna_id'] = $this->createReportType($data);
                } elseif ($data['report_type'] === 2) {
                    $data['antidesparasitante_id'] = $this->createReportType($data);
                } elseif ($data['report_type'] === 3) {
                    $data['antigarrapata_id'] = $this->createReportType($data);
                }
            }
            if ($request->hasFile('file')) {
                // Eliminar el archivo anterior si existe
                if ($history->file && file_exists(public_path($history->file))) {
                    unlink(public_path($history->file)); // Elimina el archivo anterior
                }
                $file = $request->file('file');
                $fileName = time() . '.' . $file->getClientOriginalName();
                $file->move(public_path('files/pet_histories'), $fileName);
                $filePath = 'files/pet_histories/' . $fileName;
                $data['file'] = 'pet_histories/' . $filePath;
            } else {
                $data['file'] = $history->file;
            }

            // Manejar la imagen si se proporciona
            if ($request->hasFile('image')) {
                // Eliminar la imagen anterior si existe
                if ($history->image && file_exists(public_path($history->image))) {
                    unlink(public_path($history->image)); // Elimina la imagen anterior
                }
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalName();
                $image->move(public_path('images/pet_histories'), $imageName);
                $imagePath = 'images/pet_histories/' . $imageName;
                $data['image'] = $imagePath;
            } else {
                $data['image'] = $history->image;
            }


            $history->update(
                [
                    'pet_id' => $request->input('pet_id', $history->pet_id),
                    'report_type' => $request->input('report_type', $history->report_type),
                    'veterinarian_id' => $request->input('veterinarian_id', $history->veterinarian_id),
                    'antigarrapata_id' => isset($data['antigarrapata_id']) ? $data['antigarrapata_id'] : $history->antigarrapata_id,
                    'vacuna_id' => isset($data['vacuna_id']) ? $data['vacuna_id'] : $history->vacuna_id,
                    'antidesparasitante_id' => isset($data['antidesparasitante_id']) ? $data['antidesparasitante_id'] : $history->antidesparasitante_id,
                    'application_date' => $request->input('application_date', $history->application_date),
                    'medical_conditions' => $request->input('medical_conditions', $history->medical_conditions),
                    'test_results' => $request->input('test_results', $history->test_results),
                    'vet_visits' => $request->input('vet_visits', $history->vet_visits),
                    'category' => $request->input('category', $history->category),
                    'name' => $request->input('report_name', $history->name),
                    'file' => $data['file'],
                    'image' => $data['image'],
                ]
            );
            //$this->sendNotification('pet_histories', $history, [$request->input('veterinarian_id')], __('pet.pet_history_update'));
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
                'pet_id' => 'required|exists:pets,id',
            ]);
            $histories = PetHistory::where('pet_id', $data['pet_id'])
                ->get();
            // Usar map para transformar los datos
            $responseHistories = $histories->map(function ($history) {
                $reportData = $this->getReportData($history); // Asumiendo que esta función existe

                return [
                    'report_id' => $history->id,
                    'report_name' => $history->name,
                    'report_type' =>  $reportData['report_type'],
                    'application_date' => $history->application_date ? \Carbon\Carbon::parse($history->application_date)->format('d-m-Y') : null,
                    'pet_id' => $history->pet->id,
                    'pet_name' => $history->pet->name,
                    'category_id' => $history->category,
                    'owner_id' => $history->pet->owner->id,
                    'owner_name' => $history->pet->owner->full_name,
                    'owner_avatar' => !is_null($history->pet->owner->avatar) && !empty($history->pet->owner->avatar) ? asset($history->pet->owner->avatar) : null,
                    'category_name' => isset($history->category_rel) && !is_null($history->category_rel) ? $history->category_rel->name : null,
                    'detail_history_id' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['id']) ? $reportData['detail_type']['id'] : null,
                    'detail_history_name' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['name']) ? $reportData['detail_type']['name'] : null,
                    'fecha_aplicacion' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['fecha_aplicacion'])
                        ? \Carbon\Carbon::parse($reportData['detail_type']['fecha_aplicacion'])->format('d-m-Y')
                        : null,
                    'fecha_refuerzo' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['fecha_refuerzo'])
                        ? \Carbon\Carbon::parse($reportData['detail_type']['fecha_refuerzo'])->format('d-m-Y')
                        : null,
                    'weight' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['weight']) ? $reportData['detail_type']['weight'] : null,
                    'notes' => !is_null($reportData['detail_type']) && !is_null($reportData['detail_type']['notes'])  ? $reportData['detail_type']['notes'] : null,
                    'veterinarian_id' => $history->veterinarian->id,
                    'veterinarian_name' => $history->veterinarian->full_name,
                    'medical_conditions' => $history->medical_conditions,
                    'test_results' => $history->test_results,
                    'vet_visits' => $history->vet_visits,
                    'file' => !is_null($history->file) ? asset($history->file) : null,
                    'image' => !is_null($history->image) ? asset($history->image) : null,
                    'created_at' => Carbon::parse($history->created_at)->format('d-m-Y'),
                    'updated_at' => Carbon::parse($history->updated_at)->format('d-m-Y'),
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $responseHistories
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

    public function createReportType($data)
    {
        // Mapeo de tipos de reportes a controladores
        $controllers = [
            1 => VacunaController::class,
            2 => AntiWormersController::class,
            3 => AntiTickController::class,
        ];

        // Verificar si el tipo de reporte existe en el mapeo
        if (!array_key_exists($data['report_type'], $controllers)) {
            throw new \Exception('Tipo de reporte no válido.');
        }

        // Crear un nuevo Request
        $request = new Request([
            'name' => $data['name'],
            'fecha_aplicacion' => $data['fecha_aplicacion'],
            'pet_id' => $data['pet_id'],
            'fecha_refuerzo' => $data['fecha_refuerzo'],
            'weight' => isset($data['weight']) && !is_null($data['weight']) ? $data['weight'] : null,
            'notes' => isset($data['medical_conditions']) && !is_null($data['medical_conditions']) ? $data['medical_conditions'] : null,
        ]);

        // Instanciar el controlador correspondiente
        $controller = app($controllers[$data['report_type']]);

        // Llamar al método store del controlador
        if ($data['method'] == 'store') {
            $response = $controller->store($request);
        } else {
            $id = $data['detail_history_id'];
            $response = $controller->update($request, $id);
        }


        // Si la respuesta es exitosa, asigna el ID al request original
        if ($response->getStatusCode() === 201 || $response->getStatusCode() === 200) {
            if ($data['method'] == 'store') {
                $data = json_decode($response->getContent(), true);
                return $data['data']['id']; // Retorna el ID del registro creado
            } else {
                return $data['detail_history_id'];
            }
        }

        // Manejar el caso en que la creación falla
        throw new \Exception('Error al crear el registro: ' . $response->getContent());
    }

    private function getReportData($history)
    {
        if (!is_null($history->vacuna_id)) {
            return [
                'report_type' => 1,
                'detail_type' => [
                    'id' => $history->vacuna->id,
                    'name' => $history->vacuna->vacuna_name,
                    'fecha_aplicacion' => $history->vacuna->fecha_aplicacion,
                    'fecha_refuerzo' => $history->vacuna->fecha_refuerzo_vacuna,
                    'weight' => $history->vacuna->weight,
                    'notes' => $history->vacuna->additional_notes
                ]
            ];
        }

        if (!is_null($history->antidesparasitante_id)) {
            return [
                'report_type' => 2,
                'detail_type' => [
                    'id' => $history->antiparasitante->id,
                    'name' =>  $history->antiparasitante->antidesparasitante_name,
                    'fecha_aplicacion' =>  $history->antiparasitante->fecha_aplicacion,
                    'fecha_refuerzo' =>  $history->antiparasitante->fecha_refuerzo_antidesparasitante,
                    'weight' =>  $history->antiparasitante->weight,
                    'notes' =>  $history->antiparasitante->additional_notes
                ]
            ];
        }

        if (!is_null($history->antigarrapata_id)) {
            return [
                'report_type' => 3,
                'detail_type' => [
                    'id' => $history->antigarrapata->id,
                    'name' =>  $history->antigarrapata->antigarrapata_name,
                    'fecha_aplicacion' =>  $history->antigarrapata->fecha_aplicacion,
                    'fecha_refuerzo' =>  $history->antigarrapata->fecha_refuerzo_antigarrapata,
                    'weight' =>  $history->antigarrapata->weight,
                    'notes' =>  $history->antigarrapata->additional_notes
                ]
            ];
        }

        return [
            'report_type' => null,
            'detail_type' => null
        ];
    }
}
