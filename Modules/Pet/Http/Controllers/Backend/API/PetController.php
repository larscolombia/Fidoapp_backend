<?php

namespace Modules\Pet\Http\Controllers\Backend\API;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Jobs\LostPet;
use App\Trait\Notification;
use Illuminate\Support\Str;
use Modules\Pet\Models\Pet;
use Illuminate\Http\Request;
use Modules\Pet\Models\Breed;
use Modules\Pet\Models\PetNote;
use Modules\Pet\Models\PetType;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Modules\Pet\Transformers\PetResource;
use Modules\Pet\Transformers\BreedResource;
use App\Http\Requests\Api\Pets\StoreRequest;
use App\Http\Requests\Api\Pets\UpdateRequest;
use Modules\Pet\Transformers\PetNoteResource;
use Modules\Pet\Transformers\PetTypeResource;
use Modules\Pet\Transformers\OwnerPetResource;
use Modules\Pet\Transformers\PetDetailsResource;

class PetController extends Controller
{
    use Notification;
    // Retorna una lista paginada de tipos de mascotas, filtrada por estado activo y una búsqueda opcional.
    public function petTypeList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $pettype = PetType::where('status', 1);

        if ($request->has('search')) {
            $pettype->where('name', 'like', "%{$request->search}%");
        }

        $pettype = $pettype->paginate($perPage);
        $items = PetTypeResource::collection($pettype);


        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('pet.pettype_list'),
        ], 200);
    }

    //  Retorna una lista paginada de mascotas de un usuario específico, filtrada por estado, tipo de mascota y búsqueda opcional.
    public function petList(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 10);
        $user_id = !empty($request->user_id) ? $request->user_id : auth()->user()->id;
        $pet = Pet::with(['pettype', 'breed'])->where('status', 1)->where('user_id', $user_id);

        if ($request->has('search')) {
            $pet->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('pettype_id') && $request->pettype_id != '') {
            // $pet = $pet->whereIn('pettype_id', $parent_id)->orWhere('pettype_id', $request->pettype_id);
            $pet = $pet->Where('pettype_id', $request->pettype_id);
        }
        $pet = $pet->paginate($perPage);
        $items = PetResource::collection($pet);


        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('pet.pet_list'),
        ], 200);
    }

    // Retorna una lista paginada de razas, filtrada por estado, tipo de mascota y búsqueda opcional en el nombre o descripción.
    public function breedList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $breed = Breed::where('status', 1)->orderBy('name','asc');

        if ($request->has('search')) {
            $breed->where('name', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%");
        }
        if ($request->has('pettype_id')) {
            $breed->where('pettype_id', $request->pettype_id);
        }
        //$breed = $breed->paginate($perPage);
        $breed = $breed->get();
        $items = BreedResource::collection($breed);


        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('pet.breed_list'),
        ], 200);
    }

    // Retorna una lista paginada de notas de mascotas, filtrada por estado, privacidad y tipo de usuario (usuario o administrador).
    public function petNoteList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $user = Auth::user();
        $user_type = $user->user_type;


        $pet_note = PetNote::where('status', 1)->with('createdBy');

        if ($user_type == 'user') {

            $pet_note->where(function ($query) {

                $query->where('created_by', auth()->id())
                    ->Orwhere('is_private', 0)
                    ->OrwhereHas('createdBy', function ($subQuery) {
                        $subQuery->where('user_type', 'admin');
                    })
                    ->OrwhereHas('createdBy', function ($subQuery) {
                        $subQuery->where('user_type', 'demo_admin');
                    });
            });
        } else {

            $pet_note->where(function ($query) {

                $query->where('created_by', auth()->id())

                    ->Orwhere('is_private', 0);
            });
        }

        if (!empty($request->pet_id)) {
            $pet_note->where('pet_id', $request->pet_id);
        }
        if ($request->has('search')) {
            $pet_note->where('name', 'like', "%{$request->search}%");
        }
        $pet_note = $pet_note->orderBy('created_at', 'desc');

        $pet_note = $pet_note->paginate($perPage);
        $items = PetNoteResource::collection($pet_note);

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => 'Pet Note List',
        ], 200);
    }

    // Retorna una lista paginada de dueños y sus mascotas, basándose en un employee_id y los datos de reserva.
    public function OwnerPetList(Request $request)
    {

        $employee_id = !empty($request->emaployee_id) ? $request->emaployee_id : auth()->user()->id;

        $perPage = $request->input('per_page', 10);

        $bookingData = Booking::where('employee_id', $employee_id)->select('user_id', 'pet_id')->get();

        $userIds = $bookingData->pluck('user_id')->toArray();

        $petIds = $bookingData->pluck('pet_id')->toArray();

        $users = User::with(['pets' => function ($query) use ($petIds) {
            $query->whereIn('id', $petIds);
        }])->whereIn('id', $userIds);

        $user = $users->paginate($perPage);
        $items = OwnerPetResource::collection($user);

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => 'Owners and Pets Note List',
        ], 200);
    }

    // Retorna los detalles completos de una mascota específica, incluyendo su tipo, raza y notas asociadas.
    public function PetDetails(Request $request)
    {

        try {
            $pet_id = $request->has('pet_id') ? $request->input('pet_id') : null;

            $pet_details = Pet::with(['pettype', 'breed', 'petnote', 'sharedOwners', 'owner'])->where('id', $pet_id)->first();

            $items = new PetDetailsResource($pet_details);

            return response()->json([
                'status' => true,
                'data' => $items,
                'message' => 'Pet Details',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Retorna las edades de una mascota y su dueño, basado en el id de la mascota.
    public function getPetAndOwnerAge($id)
    {
        $pet = Pet::with('user')->find($id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Pet not found'
            ], 404);
        }

        $owner = $pet->user;

        return response()->json([
            'success' => true,
            'message' => 'Pet and owner ages retrieved successfully',
            'data' => [
                'pet_age' => $pet->age,
                'owner_age' => $owner->age,
            ]
        ]);
    }

    // Retorna una lista de todas las mascotas de tipo 'dog' con información detallada sobre su raza, incluyendo nombre, descripción, género, peso y altura.
    public function getAllPetsWithBreedInfo()
    {
        $pets = Pet::where('slug', 'dog')->with('breed')->get();

        $result = $pets->map(function ($pet) {
            return [
                'name' => $pet->name,
                'age' => $pet->age,
                'qr_code' => asset($pet->qr_code),
                'breed' => [
                    'name' => $pet->breed->name,
                    'description' => $pet->breed->description,
                    'gender' => $pet->gender,
                    'weight' => $pet->weight ?? 0,
                    'weight_unit' => $pet->weight_unit ?? '',
                    'height' => $pet->height ?? 0,
                    'height_unit' => $pet->height_unit ?? '',
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Pets retrieved successfully',
            'data' => $result
        ]);
    }

    /**
     * Crear una nueva mascota.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Define las reglas de validación
            $rules = [
                'name' => 'sometimes|string|max:255',
                'breed_id' => 'sometimes|exists:breeds,id',
                'breed_name' => 'sometimes|string',
                'size' => 'sometimes|string|max:50',
                'date_of_birth' => 'sometimes|string',
                'gender' => 'sometimes|in:male,female',
                'weight' => 'sometimes|numeric',
                'height' => 'sometimes|numeric',
                'weight_unit' => 'sometimes|string|max:10',
                'height_unit' => 'sometimes|string|max:10',
                'user_id' => 'required|exists:users,id',
                'additional_info' => 'sometimes|string',
                'status' => 'sometimes|boolean',
                'pet_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,tiff,tif,bmp,webp',
                'qr_code' => 'sometimes',
                'passport' => 'nullable|string',
                'pet_fur' => 'nullable|string|max:255'
            ];

            // Obtener los datos validados
            $validatedData = $request->validate($rules);

            // Manejo de breed_id y breed_name
            $breed = null;

            if (!empty($validatedData['breed_id'])) {
                $breed = Breed::find($validatedData['breed_id']);
            }

            if (!$breed && !empty($validatedData['breed_name'])) {
                $breed = Breed::where('name', $validatedData['breed_name'])->first();
            }

            if (!isset($validatedData['qr_code'])) {
                $validatedData['qr_code'] = null;
            }

            if (!$breed) {
                return response()->json([
                    'message' => __('pet.invalid_breed'),
                ], 422);
            }

            try {
                $validatedData['date_of_birth'] = Carbon::createFromFormat('Y-m-d', $validatedData['date_of_birth'])->format('Y-m-d');
            } catch (\Exception $e) {
                $validatedData['date_of_birth'] = null; // Asignar null si la conversión falla
            }

            $validatedData['breed_id'] = $breed->id;
            // Calcular la edad de la mascota
            if (isset($validatedData['date_of_birth']) && !is_null($validatedData['date_of_birth'])) {
                $validatedData['age'] = $this->calculateAge($validatedData['date_of_birth']);
            }
            // Generar el slug automáticamente si no está presente
            $slug = Str::slug($validatedData['name']);
            $slugCount = Pet::where('slug', 'LIKE', "{$slug}%")->count();
            if ($slugCount > 0) {
                $slug .= '-' . ($slugCount + 1);
            }

            $validatedData['pettype_id'] = PetType::where('slug', 'dog')->first()->id;
            $validatedData['slug'] = $slug;

            if (!file_exists(public_path('images/pets'))) {
                mkdir(public_path('images/pets'), 0755, true);
            }


            // Crear la nueva mascota
            $pet = Pet::create($validatedData);
            // Manejo de la imagen de la mascota usando Media Library
            if ($request->hasFile('pet_image')) {
                $pet->addMedia($request->file('pet_image'))
                    ->toMediaCollection('pet_image');
            }

            //creando el qr
            $validatedData['qr_code'] = $this->generateQrCode($pet);
            $pet->update([
                'qr_code' => $validatedData['qr_code']
            ]);
            //notification
            $title = __('Mascota') . ' ' . $pet->name;
            $this->sendNotification($pet->user_id, 'pets', $title, $pet, [$pet->user_id], __('pet.pet_created_successfully'));
            return response()->json([
                'message' => __('pet.pet_created_successfully'),
                'data' => $pet
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->validator->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            // Manejo de excepciones generales
            \Log::error('Error al crear la mascota: ' . $e->getMessage());

            return response()->json([
                'message' => __('pet.creation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pet = Pet::findOrFail($id);

            $rules = [
                'name' => 'sometimes|string|max:255',
                'breed_id' => 'sometimes|exists:breeds,id',
                'breed_name' => 'sometimes|string',
                'size' => 'sometimes|string|max:50',
                'date_of_birth' => 'sometimes|string',
                'gender' => 'sometimes|in:male,female',
                'weight' => 'sometimes|numeric',
                'height' => 'sometimes|numeric',
                'weight_unit' => 'sometimes|max:10',
                'height_unit' => 'sometimes|max:10',
                'user_id' => 'sometimes|exists:users,id',
                'additional_info' => 'sometimes|string',
                'status' => 'sometimes|boolean',
                'pet_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,tiff,tif,bmp,webp',
                'qr_code' => 'sometimes',
                'passport' => 'nullable|string',
                'pet_fur' => 'nullable|string|max:255'
            ];

            $validatedData = $request->validate($rules);
            if (!file_exists(public_path('images/pets'))) {
                mkdir(public_path('images/pets'), 0755, true);
            }
            if (isset($validatedData['breed_id'])) {
                $breed = Breed::find($validatedData['breed_id']);
            } elseif (isset($validatedData['breed_name'])) {
                $breed = Breed::where('name', $validatedData['breed_name'])->first();
                if ($breed) {
                    $validatedData['breed_id'] = $breed->id;
                } else {
                    return response()->json([
                        'message' => __('validation.invalid_breed'),
                    ], 422);
                }
            }

            try {
                if (!is_null($validatedData['date_of_birth'])) {
                    $validatedData['date_of_birth'] = str_replace('/', '-', $validatedData['date_of_birth']);
                    $validatedData['age'] = $this->calculateAge($validatedData['date_of_birth']);
                }

                $validatedData['date_of_birth'] = Carbon::createFromFormat('Y-m-d', $validatedData['date_of_birth'])->format('Y-m-d');
            } catch (\Exception $e) {
                $validatedData['date_of_birth'] = null; // Asignar null si la conversión falla
            }

            //buscamos el qr
            $qrCodeFilename = basename($pet->qr_code); // Extraer solo el nombre del archivo

            // Construir la ruta completa en el sistema de archivos
            $qrCodePath = public_path('images/qr_codes/' . $qrCodeFilename);
            if (file_exists($qrCodePath)) {
                unlink($qrCodePath);
            }
            //actualizamos
            $pet->update($validatedData);
            //creando el qr
            $validatedData['qr_code'] = $this->generateQrCode($pet);
            //actualizamos el qr
            $pet->update([
                'qr_code' => $validatedData['qr_code']
            ]);
            // Manejo de la imagen de la mascota
            if ($request->hasFile('pet_image')) {
                // Eliminar la imagen anterior si existe
                if ($pet->hasMedia('pet_image')) {
                    $pet->getFirstMedia('pet_image')->delete();
                }

                // Agregar la nueva imagen a la colección de medios
                $pet->addMedia($request->file('pet_image'))
                    ->toMediaCollection('pet_image');
                // Recargar el modelo para obtener los datos actualizados
                $pet->load('media');
            }
            $title = __('Mascota') . ' ' . $pet->name;
            $userId = !is_null($request->input('user_id')) ? $request->input('user_id') : $pet->user_id;
            //notification
            $this->sendNotification($userId, 'pets', $title, $pet, [$pet->user_id], __('pet.pet_updated_successfully'));
            return response()->json([
                'success' => true,
                'message' => __('pet.pet_updated_successfully'),
                'data' => $pet
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->validator->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ]);
        }
    }

    public function calculateAge($birthdate)
    {
        $birthdate = Carbon::parse($birthdate);
        $now = Carbon::now();

        $years = $now->diffInYears($birthdate);
        $months = $now->diffInMonths($birthdate->copy()->addYears($years));
        $days = $now->diffInDays($birthdate->copy()->addYears($years)->addMonths($months));

        $ageString = '';

        if ($years > 0) {
            $ageString .= trans_choice('pet.years', $years, ['count' => $years]) . ', ';
        }

        if ($months > 0) {
            $ageString .= trans_choice('pet.months', $months, ['count' => $months]) . ', ';
        }

        $ageString .= trans_choice('pet.days', $days, ['count' => $days]);

        return $ageString;
    }

    public function show($id)
    {
        $pet = Pet::with(['pettype', 'breed'])->findOrFail($id);
        $result =  [
            'id' => $pet->id,
            'name' => $pet->name,
            'slug' => $pet->slug,
            'pettype_id' => $pet->pettype_id,
            'breed_id' => $pet->breed_id,
            'size' => $pet->size,
            'date_of_birth' => !is_null($pet->date_of_birth) ? Carbon::parse($pet->date_of_birth)->format('d-m-Y') : null,
            'age' => $pet->age,
            'gender' => $pet->gender,
            'weight' => $pet->weight ?? 0,
            'height' => $pet->height ?? 0,
            'weight_unit' => $pet->weight_unit ?? '',
            'height_unit' => $pet->height_unit ?? '',
            'user_id' => $pet->user_id,
            'additional_info' => $pet->additional_info,
            'status' => $pet->status,
            'passport' => $pet->passport,
            'permission_expiration' => $pet->permission_pet_profile ? Carbon::parse($pet->permission_pet_profile->expiration)->format('d-m-Y') : null,
            'created_by' => $pet->created_by,
            'updated_by' => $pet->updated_by,
            'deleted_by' => $pet->deleted_by,
            'created_at' => !is_null($pet->created_at) ? Carbon::parse($pet->created_at)->format('d-m-Y') : null,
            'updated_at' => !is_null($pet->updated_at) ? Carbon::parse($pet->updated_at)->format('d-m-Y') : null,
            'deleted_at' => $pet->deleted_at,
            'qr_code' => $pet->qr_code,
            'pet_image' => $pet->pet_image,
            'pet_fur' => $pet->pet_fur,
            'public_profile' =>  route('pet_detail.profile_public', ['slug' => $pet->slug]),
            // Agregar descripción de la raza
            'description' => optional($pet->breed)->description, // Usamos optional para evitar errores si breed es null
            // Agregar nombre del tipo de mascota
            'pettype' => optional($pet->pettype)->name, // Usamos optional para evitar errores si pettype es null
        ];
        return response()->json([
            'data' => $result,
            'message' => __('pet.pet_retrieved_successfully'),
        ]);
    }

    public function updateLost(Request $request)
    {
        $data = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
        ]);

        $pet = Pet::findOrFail($data['pet_id']);
        $pet->lost = true;
        $pet->lost_date = Carbon::now();
        $pet->save();

        $userIds = User::where('status', 1)->pluck('id')->toArray();
        $message = __('messages.pet_lost_notification', [
            'pet_name' => $pet->name,
            'owner_name' => $pet->owner->full_name,
        ]);
        //notificacion
        $this->sendNotification($pet->user_id, 'pets', __('pet.lost'), $pet, $userIds, $message);
        //notificacion push
        // dispatch(new LostPet($userIds,__('pet.lost'),$message));
        return response()->json([
            'data' => $pet,
            'message' => __('messages.pet_lost'),
        ], 200);
    }

    public function destroy($id)
    {
        $petSelected = Pet::find($id);
        $userId = $petSelected->user_id;
        $petSelected->delete();
        $this->sendNotification($userId, 'pets', __('pet.title'), $petSelected, [$petSelected->user_id], __('pet.pet_deleted_successfully'));
        return response()->json([
            'success' => true,
            'message' => __('pet.pet_deleted_successfully'),
            'data' => $petSelected
        ]);
    }

    public function generateQrCode($pet)
    {
        // Convierte el array $pet a una cadena JSON
        //$data = json_encode($pet);
        // Obtiene el slug de la mascota
        $slug = $pet['slug']; // Asumiendo que $pet es un array y contiene el slug

        // Construye la URL para la API de qrserver.com usando el slug
        $url = route('pet_detail.profile_public', ['slug' => $slug]);
        // Construye la URL para la API de qrserver.com
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/';
        $size = '150x150'; // Tamaño del código QR
        $url = $qrCodeUrl . '?size=' . $size . '&data=' . urlencode($url);
        // Realiza la solicitud para obtener el código QR
        $response = Http::get($url);
        // Verifica si la solicitud fue exitosa
        if ($response->successful()) {
            // Obtén el contenido de la imagen del QR Code
            $qrCodeContent = $response->body();

            // Genera un nombre de archivo basado en el timestamp actual
            $timestamp = time(); // Obtiene el timestamp actual
            $filename = 'qr_code_' . $timestamp . '.png'; // Nombre del archivo
            $path = 'images/qr_codes/' . $filename;

            // Guarda el archivo en el disco público
            $saved = File::put(public_path($path), $qrCodeContent);

            // Verifica si el archivo se guardó correctamente
            if ($saved) {
                return $path;
            } else {
                throw new \Exception("No se pudo guardar el archivo QR code.");
            }
        } else {
            throw new \Exception("Error al generar el código QR: " . $response->status());
        }
    }
}
