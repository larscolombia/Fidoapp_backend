<?php

namespace Modules\Pet\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Modules\Pet\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Modules\Pet\Http\Requests\PetsRequest;
use Carbon\Carbon;
use Illuminate\Database\Query\Expression;
use Modules\Pet\Models\Breed;
use App\Models\User;
use Modules\Pet\Models\PetNote;
use Modules\Booking\Models\Booking;
use App\Models\Setting;
use App\Models\SharedOwner;
use Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Http;
use Illuminate\Support\Facades\File;

class PetsController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'pet.owners_and_pets';

        // module name
        $this->module_name = 'pets';

        // directory path of the module
        $this->module_path = 'pet::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $module_action = 'List';

        return view('pet::backend.pets.index_datatable',  compact('module_action'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = User::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_pet_update');
                break;

            case 'delete':
                User::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_pet_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        // $term = trim($request->q);

        // if (empty($term)) {
        //     return response()->json([]);
        // }

        // $query_data = Pet::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        // $data = [];

        // foreach ($query_data as $row) {
        //     $data[] = [
        //         'id' => $row->id,
        //         'text' => $row->name.' (Slug: '.$row->slug.')',
        //     ];
        // }
        $pettype_id = $request->pettype_id;
        $data = Pet::with('pettype');

        if (isset($pettype_id)) {
            $data->where('pettype_id', $pettype_id);
        }

        $user_id = $request->user_id;

        if (isset($user_id)) {
            $data->where('user_id', $user_id);
        }

        $data = $data->get();

        return response()->json($data);
    }

    public function index_data()
    {
        $query = User::role('user')
            ->with('pets');

        $user = Auth::User();

        $usertype = $user->user_type;

        if ($usertype == "vet" || $usertype == "groomer" || $usertype == "walker" || $usertype == "boarder" || $usertype == "trainer" || $usertype == "day_taker") {

            $UserIds = Booking::where('employee_id', $user->id)->pluck('user_id');

            $query->whereIn('id', $UserIds);
        }

        return Datatables::of($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->editColumn('user_id', function ($data) {
                return view('pet::backend.pets.user_id', compact('data'));
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
                }
            })
            ->orderColumn('user_id', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)


            ->editColumn('pet_count', function ($data) {

                $pet_count = Pet::where('user_id', $data->id)->count();

                $user = Auth::User();

                $usertype = $user->user_type;

                if ($usertype == "vet" || $usertype == "groomer" || $usertype == "walker" || $usertype == "boarder" || $usertype == "trainer" || $usertype == "day_taker") {

                    $petIds = Booking::where('employee_id', $user->id)->pluck('pet_id');

                    $pet_count = Pet::whereIn('id', $petIds)->count();
                }

                $viewButton = '';

                $addButton = '';


                if (auth()->user()->can("view_owner's_pet")) {

                    $viewButton = "<b><button type='button' data-assign-module='{$data->id}' data-assign-target='#pet-assign-form' data-assign-event='assign_pet' class='btn btn-secondary btn-sm rounded text-nowrap px-1' data-bs-toggle='tooltip' title='" . __('pet.view_all_pet') . "'> {$pet_count}</button></b>";
                }

                if (auth()->user()->can("add_owner's_pet")) {

                    $addButton = "<button type='button' data-assign-module='{$data->id}' data-assign-target='#add-pet-form' data-assign-event='add-pet-form' class='btn btn-soft-primary btn-sm rounded text-nowrap px-2' data-bs-toggle='tooltip' title='" . __('pet.create') . "'><i class='fa-solid fa-plus p-0'></i></button>";
                }

                return $viewButton . $addButton;
            })

            ->editColumn('gender', function ($data) {
                $gender = ucfirst($data->gender);
                $genderTranslate = trans('users.other');
                if ($gender == 'Male') {
                    $genderTranslate = trans('users.male');
                }
                if ($gender == 'Female') {
                    $genderTranslate = trans('users.female');
                }
                return $genderTranslate;
            })

            ->addColumn('action', function ($data) {


                $other_settings = Setting::where('name', 'is_user_push_notification')->first();

                $enable_push_notification = 0;

                if (!empty($other_settings)) {

                    $enable_push_notification = $other_settings->val;
                }



                return view('pet::backend.pets.owner_action_column', compact('data', 'enable_push_notification'));
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                                <div class="form-check form-switch ">
                                    <input type="checkbox" data-url="' . route('backend.customers.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
                                </div>
                            ';
            })
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (! empty($keyword)) {

                    $query->where('first_name', 'like', '%' . $keyword . '%');
                }
            })
            ->rawColumns(['action', 'status', 'check', 'pet_count'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $module_action = 'Create';

        return view('pet::backend.pets.create', compact('module_action'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(PetsRequest $request)
    {
        $data = $request->except('pet_image');
        $data['breed_id'] = $request->breed;

        if ($request->age == NULL) {
            // Calcular la edad de la mascota
            $data['age'] = $this->calculateAge($data['date_of_birth']);
        }


        $query = Pet::create($data);
        $data['qr_code'] = $this->generateQrCode($query);
        //Log::info($data['qr_code']);
        $query->update([
            'qr_code' => $data['qr_code']
        ]);
        Log::info($query);

        storeMediaFile($query, $request->file('pet_image'), 'pet_image');

        $this->module_title = 'pet.title';

        $message = __('messages.create_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_action = 'Show';

        $data = Pet::findOrFail($id);

        return view('pet::backend.pets.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        // $module_action = 'Edit';

        // $data = Pet::findOrFail($id);

        // if (request()->wantsJson()) {
        //     return response()->json(['data' => $$module_name_singular, 'status' => true]);
        // } else {
        //     return view('pet::backend.pets.edit', compact('module_action', "$data"));
        // }
        $data = Pet::findOrFail($id);

        $data['breed_list'] = Breed::where('pettype_id', $data['pettype_id'])->with('pettype')->get();

        $data['pet_image'] = $data->pet_image;
        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // $data = Pet::findOrFail($id);

        // $data->update($request->all());

        // $message = Str::singular(Pets).' Updated Successfully';

        // if (request()->wantsJson()) {
        //     return response()->json(['message' => $message, 'status' => true], 200);
        // } else {
        //     flash("<i class='fas fa-check'></i> $message")->success()->important();

        //     return redirect()->route("backend.pets.show", $data->id);
        // }

        $query = Pet::findOrFail($id);
        $data = $request->except('pet_image');
        $data['breed_id'] = $request->breed;
        $query->update($data);
        $data['qr_code'] = $this->generateQrCode($query);
        $query->update([
            'qr_code' => $data['qr_code']
        ]);
        storeMediaFile($query, $request->file('pet_image'), 'pet_image');
        $this->module_title = 'pet.title';
        $message = __('messages.update_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

        $data = Pet::findOrFail($id);

        $user_id = $data['user_id'];

        $data->delete();

        $user_pet = Pet::where('user_id', $user_id)->with('breed')->get();

        $this->module_title = 'pet.title';

        $message = __('messages.delete_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'data' => $user_pet, 'status' => true], 200);
    }

    /**
     * List of trashed ertries
     * works if the softdelete is enabled.
     *
     * @return Response
     */
    public function trashed()
    {
        $module_name_singular = Str::singular($this->module_name);

        $module_action = 'Trash List';

        $data = Pet::with('user')->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('pet::backend.pets.trash', compact('data', 'module_name_singular', 'module_action'));
    }
    public function restore($id)
    {
        $data = Pet::withTrashed()->find($id);
        $data->restore();

        $message = Str::singular($this->module_title) . ' Data Restoreded Successfully';

        return redirect('app/pets');
    }
    public function update_status(Request $request, Pet $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function user_pet_list($id)
    {


        $all_pets = Pet::with('media')->get();

        $user_pet = Pet::where('user_id', $id)->with('breed')->get();

        $user_data = User::where('id', $id)->first();

        $user = Auth::User();

        $usertype = $user->user_type;


        if ($usertype == "vet" || $usertype == "groomer" || $usertype == "walker" || $usertype == "boarder" || $usertype == "trainer" || $usertype == "day_taker") {

            $petIds = Booking::where('employee_id', $user->id)->pluck('pet_id');

            $user_pet = Pet::whereIn('id', $petIds)->with('breed')->get();
        }

        $data = [

            'all_pet' => $all_pets,
            'user_pet' => $user_pet,
            'user_name' => $user_data['first_name'] . ' ' . $user_data['last_name']
        ];

        return response()->json(['data' => $data, 'status' => true,]);
    }

    public function pet_notes_list($id)
    {

        $user = Auth::user();
        $usertype = $user->user_type;

        $pet_notes = PetNote::where('pet_id', $id)
            ->when(
                $usertype !== "admin" && $usertype !== "demo_admin",
                function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {
                        $query->where('is_private', 0)
                            ->orWhere(function ($query) use ($user) {
                                $query->where('is_private', 1)
                                    ->where('created_by', $user->id);
                            });
                    });
                }
            )
            ->with('pet')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $pet_notes, 'status' => true]);
    }

    public function add_pet_notes(Request $request)
    {

        $data = $request->all();

        $data['created_by'] = auth()->user()->id;

        PetNote::create($data);

        $message = __('messages.create_pet_note');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function delete_pet_note($id)
    {
        $data = PetNote::findOrFail($id);

        $pet_id = $data->pet_id;

        $data->delete();

        $notes = PetNote::where('pet_id', $pet_id)->orderBy('created_at', 'desc')->get();

        $message = __('messages.delete_notes');

        return response()->json(['message' => $message, 'data' => $notes, 'status' => true], 200);
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

    public function sharedOwner(Request $request, $id)
    {
        $user = $request->user;
        foreach ($user as $user_id) {
            SharedOwner::create([
                'user_id' => $user_id,
                'pet_id' => $id,
            ]);
        }

        return redirect()->back()->with('success', __('pet.Pet shared successfully'));
    }

    public function users_and_owners(Request $request, $petId)
    {
        $users = User::all();

        $sharedOwners = SharedOwner::where('pet_id', $petId)->pluck('user_id')->toArray();

        // Devolver los datos necesarios para el frontend
        return response()->json([
            'users' => $users,
            'sharedOwners' => $sharedOwners
        ]);
    }

    //lost pet
    public function lostsPet()
    {
        $module_title = __('pet.lost_pets');
        return view('pet::backend.pets.lost.index',  compact('module_title'));
    }
    public function lostPetData()
    {
        $query = Pet::where('lost', 1)->get();

        return Datatables::of($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheckFoundPet(' . $row->id . ')">';
            })
            ->editColumn('name', function ($data) {
                return ucfirst($data->name);
            })
            ->editColumn('age', function ($data) {
                return ucfirst($data->age);
            })
            ->editColumn('breed_id', function ($data) {
                return ucfirst($data->breed->name);
            })
            ->editColumn('user_id', function ($data) {
                $data = $data->owner;
                return view('pet::backend.pets.user_id', compact('data'));
            })

            ->editColumn('lost_date', function ($data) {
                $formattedDate = Carbon::parse($data->lost_date)->format('d-m-Y');
                return $formattedDate;
            })

            ->addColumn('action', function ($data) {
                return view('pet::backend.pets.lost.actions', compact('data'));
            })
            ->rawColumns(['action', 'check'])

            ->make(true);
    }

    public function lostPetStoreId(Request $request, $id)
    {
        try {

            // Intenta encontrar la mascota por ID
            $pet = Pet::findOrFail($id);
            // Actualiza el estado de la mascota
            $pet->lost = false;
            $pet->found_date = Carbon::now();
            $pet->save();

            // Devuelve una respuesta exitosa
            return response()->json(['status' => true, 'message' => __('messages.pet_success')]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Maneja el caso en que no se encuentra la mascota
            Log::info($e->getMessage());
            return response()->json(['status' => false, 'message' => __('messages.pet_not_found')], 404);
        } catch (\Exception $e) {
            // Maneja cualquier otro tipo de excepción
            Log::info($e->getMessage());
            return response()->json(['status' => false, 'message' => __('messages.general_error')], 500);
        }
    }


    public function lostPetsStoreIds(Request $request)
    {
        $rowData = json_decode($request->input('rowData', '[]'), true);
        $idsByModule = [];
        foreach ($rowData as $data) {
            $idsByModule[] = $data['id'];
        }
        $actionType = $request->action_type;
        $message = __('messages.pets_success');
        $foundDate = Carbon::now();
        switch ($actionType) {
            case 'found_pet':
                $branches = Pet::whereIn('id', $idsByModule)->update(['lost' => false, 'found_date' => $foundDate]);
                $message = __('messages.pets_success');
                break;
            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.pets_success')]);
    }
}
