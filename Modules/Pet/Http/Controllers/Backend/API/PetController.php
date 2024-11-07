<?php

namespace Modules\Pet\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pets\StoreRequest;
use App\Http\Requests\Api\Pets\UpdateRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Pet\Models\Pet;
use Modules\Pet\Models\PetType;
use Modules\Pet\Models\Breed;
use Modules\Pet\Models\PetNote;
use Modules\Pet\Transformers\PetResource;
use Modules\Pet\Transformers\PetTypeResource;
use Modules\Pet\Transformers\BreedResource;
use Modules\Pet\Transformers\PetNoteResource;
use Modules\Pet\Transformers\OwnerPetResource;
use Modules\Pet\Transformers\PetDetailsResource;
use Modules\Booking\Models\Booking;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Support\Facades\Log;

class PetController extends Controller
{
    // Retorna una lista paginada de tipos de mascotas, filtrada por estado activo y una búsqueda opcional.
    public function petTypeList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $pettype = PetType::where('status',1);

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
        $pet = Pet::with(['pettype','breed'])->where('status',1)->where('user_id',$user_id);

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

        $breed = Breed::where('status',1);

        if ($request->has('search')) {
            $breed->where('name', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%");
        }
        if ($request->has('pettype_id')) {
            $breed->where('pettype_id',$request->pettype_id);
        }
        $breed = $breed->paginate($perPage);
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

        $user=Auth::user();
        $user_type=$user->user_type;


        $pet_note = PetNote::where('status',1)->with('createdBy');

        if($user_type =='user'){

            $pet_note->where(function ($query) {

                $query->where('created_by',auth()->id())
                         ->Orwhere('is_private',0)
                         ->OrwhereHas('createdBy', function ($subQuery) {
                            $subQuery->where('user_type','admin');
                       })
                       ->OrwhereHas('createdBy', function ($subQuery) {
                        $subQuery->where('user_type','demo_admin');
                   });
               });
         }else{

            $pet_note->where(function ($query) {

                $query->where('created_by',auth()->id())

                         ->Orwhere('is_private',0);

                  });

          }

        if (!empty($request->pet_id)) {
            $pet_note->where('pet_id', $request->pet_id);
        }
        if ($request->has('search')) {
            $pet_note->where('name', 'like', "%{$request->search}%");
        }
        $pet_note= $pet_note->orderBy('created_at','desc');

        $pet_note = $pet_note->paginate($perPage);
        $items = PetNoteResource::collection($pet_note);

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => 'Pet Note List',
        ], 200);
    }

    // Retorna una lista paginada de dueños y sus mascotas, basándose en un employee_id y los datos de reserva.
    public function OwnerPetList(Request $request){

        $employee_id=!empty($request->emaployee_id) ? $request->emaployee_id : auth()->user()->id;

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
    public function PetDetails(Request $request){

        $pet_id = $request->has('pet_id') ? $request->input('pet_id') : null;

        $pet_details = Pet::with(['pettype','breed','petnote'])->where('id', $pet_id)->first();

        $items =New PetDetailsResource($pet_details);

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => 'Pet Details',
        ], 200);

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
                'breed' => [
                    'name' => $pet->breed->name,
                    'description' => $pet->breed->description,
                    'gender' => $pet->gender,
                    'weight' => $pet->weight,
                    'weight_unit' => $pet->weight_unit,
                    'height' => $pet->height,
                    'height_unit' => $pet->height_unit,
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
        // Define las reglas de validación
        $rules = [
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

        if (!$breed) {
            return response()->json([
                'message' => __('pet.invalid_breed'),
            ], 422);
        }

        $validatedData['breed_id'] = $breed->id;

        // Generar el slug automáticamente si no está presente
        $slug = Str::slug($validatedData['name']);
        $slugCount = Pet::where('slug', 'LIKE', "{$slug}%")->count();
        if ($slugCount > 0) {
            $slug .= '-' . ($slugCount + 1);
        }

        $validatedData['pettype_id'] = PetType::where('slug', 'dog')->first()->id;
        $validatedData['slug'] = $slug;

        // Manejo de la imagen de la mascota
         if ($request->hasFile('pet_image')) {
            $image = $request->file('pet_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'images/pets/' . $imageName;

            // Mueve la imagen a la carpeta public/images/pets
            $image->move(public_path('images/pets'), $imageName);
        }

        // Crear la nueva mascota
        $pet = Pet::create($validatedData);

        if (!empty($request['pet_image'])) {
            // $media = $pet->addMediaFromUrl($request['pet_image'])->toMediaCollection('pet_image');
            storeMediaFile($pet, $request->file('pet_image'), 'pet_image');
        }

        return response()->json([
            'message' => __('pet.pet_created_successfully'),
            'data' => $pet
        ], 201);
    }

    public function update(Request $request, $id)
    {
        try{
            $pet = Pet::findOrFail($id);

            $rules = [
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

            $validatedData = $request->validate($rules);

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

            if($request->has('pet_image')){
                if ($request->hasFile('pet_image')) {
                    storeMediaFile($pet, $request->file('pet_image'), 'pet_image');
                } elseif ( $request->pet_image != null && $request->pet_image != '') {
                    $pet->clearMediaCollection('pet_image');
                    $pet->addMediaFromUrl($request['pet_image'])->toMediaCollection('pet_image');
                } else {
                    return response()->json([
                        'message' => 'El campo pet_image debe ser un archivo o una URL válida.'
                    ], 422);
                }
            }


            $pet->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => __('pet.pet_updated_successfully'),
                'data' => $pet
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ]);
        }
    }

    public function show($id)
    {
        $pet = Pet::with(['pettype', 'breed'])->findOrFail($id);

        return response()->json([
            'data' => $pet,
            'message' => __('pet.pet_retrieved_successfully'),
        ]);
    }

    public function destroy($id)
    {
        $petSelected = Pet::find($id);
        $petSelected->delete();

        return response()->json([
            'success' => true,
            'message' => __('pet.pet_deleted_successfully'),
            'data' => $petSelected
        ]);
    }
}
