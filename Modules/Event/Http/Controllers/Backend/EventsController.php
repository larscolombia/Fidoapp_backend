<?php

namespace Modules\Event\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EventCreatedNotification;
use App\Notifications\EventUpdatedNotification;
use Modules\Event\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Database\Query\Expression;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EventsController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'event.title';

        // module name
        $this->module_name = 'events';

        // directory path of the module
        $this->module_path = 'event::backend';

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
        $create_title = 'Event';

        return view('event::backend.events.index_datatable',  compact('module_action','create_title'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = Event::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_events_update');
                break;

            case 'delete':
                Event::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_events_delete');
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
        $term = trim($request->q);

        if (empty($term)) {
            return response()->json([]);
        }

        $query_data = Event::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name.' (Slug: '.$row->slug.')',
            ];
        }
        return response()->json($data);
    }

    public function index_data()
    {
        $currentDate = Carbon::now()->toDateString();
        $query = Event::query()
                ->whereDate('date', $currentDate)
                ->orWhere('date', '>', $currentDate);

        return Datatables::of($query)
                        ->addColumn('check', function ($row) {
                            return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
                        })
                        ->editColumn('image', function ($data) {
                            return "<img src='" . $data->event_image . "'class='avatar avatar-40 img-fluid rounded-pill'>";
                        })
                        ->editColumn('date', function ($data) {
                            $date = Carbon::parse($data->date);
                            $diff = Carbon::now()->diffInHours($date);

                            if ($diff < 25) {
                                return $date->diffForHumans();
                            } else {
                                return $date->isoFormat('llll');
                            }
                        })
                        ->editColumn('user_id', function ($data) {
                            $userType = isset($data->user) ? str_replace('day_taker', 'dayCare_taker', $data->user->user_type) : '';
                            $userDescription = '';
                            $userTypeFormat = $userType ? ucwords(str_replace('_', ' ', $userType)) : null;
                            if($userTypeFormat === 'Vet'){
                                $userDescription = 'Veterinario';
                            }
                            if($userTypeFormat === 'Trainer'){
                                $userDescription = 'Entrenador';
                            }
                            if($userTypeFormat === 'Admin'){
                                $userDescription = 'Administrador';
                            }
                            $user = isset($data->user->first_name) ? $data->user->first_name . ' ' . $data->user->last_name . ' ' .$userDescription : '-';

                            return $user;

                        })
                        ->filterColumn('user_id', function ($query, $keyword) {
                            if (!empty($keyword)) {
                                $query->whereHas('user', function ($q) use ($keyword) {
                                    $q->where('first_name', 'like', '%'.$keyword.'%');
                                });
                            }
                        })
                        ->orderColumn('user_id', function ($query, $order) {
                            $query->orderBy(new Expression('(SELECT first_name FROM users WHERE id = user_id)'), $order);
                        }, 1)
                        ->addColumn('action', function ($data) {
                            return view('event::backend.events.action_column', compact('data'));
                        })
                        ->editColumn('status', function ($row) {
                            $checked = '';
                            if ($row->status) {
                                $checked = 'checked="checked"';
                            }

                            return '
                            <div class="form-check form-switch ">
                                <input type="checkbox" data-url="'.route('backend.events.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'"  name="status" value="'.$row->id.'" '.$checked.'>
                            </div>
                           ';
                        })
                        // ->editColumn('updated_at', function ($data) {
                        //     $module_name = $this->module_name;

                        //     $diff = Carbon::now()->diffInHours($data->updated_at);

                        //     if ($diff < 25) {
                        //         return $data->updated_at->diffForHumans();
                        //     } else {
                        //         return $data->updated_at->isoFormat('llll');
                        //     }
                        // })
                        ->rawColumns(['action', 'status', 'check', 'image'])
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
        $users = User::all();
        $events = Event::all()->map(function ($event) {
            return [
                'title' => $event->name,
                'start' => $event->date,
                'description' => $event->description,
            ];
        })->toArray(); // Asegúrate de convertir a un array

        return view('event::backend.events.create', compact('users', 'events'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'fecha' => 'required|date_format:Y-m-d\TH:i', // Valida el formato de fecha y hora
            'end_date' => 'required|date_format:Y-m-d\TH:i|after_or_equal:fecha',
            'user_id' => 'required|exists:users,id',
            'descripcion' => 'nullable|string',
            'ubication' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Valida que sea una imagen
        ]);

        // Verifica si se subió una imagen y guárdala en el directorio público
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->move(public_path('images/events'), $request->file('image')->getClientOriginalName());
            $imagePath = 'images/events/' . $request->file('image')->getClientOriginalName(); // Guarda la ruta relativa
        }

        $event = Event::create([
            'name' => $request->input('titulo'),
            'tipo' => $request->input('tipo'),
            'date' => $request->input('fecha'),
            'end_date' => $request->input('end_date'),
            'user_id' => $request->input('user_id'),
            'description' => $request->input('descripcion'),
            'location' => $request->input('ubication'),
            'image' => $imagePath,
        ]);

         // Enviar notificación a todos los usuarios
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new EventCreatedNotification($event));
        }

        return redirect()->route('backend.events.index')->with('success', __('event.Event created successfully.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);
        // Asegúrate de que $event->date sea una instancia de Carbon.
        $event->fecha = $event->date ? Carbon::parse($event->date)->format('Y-m-d') : null;
        $event->hora = $event->date ? Carbon::parse($event->date)->format('H:i') : null;
        return view('event::backend.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $users = User::all();
        $events = Event::all()->map(function ($event) {
            return [
                'title' => $event->name,
                'start' => $event->date,
                'end' => $event->end_date,
                'description' => $event->description,
            ];
        })->toArray(); // Asegúrate de convertir a un array

        return view('event::backend.events.edit', compact('event', 'users', 'events'));
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
        $request->validate([
            'name' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:date',
            'user_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Agregar validación de la imagen
        ]);

        $event = Event::findOrFail($id);

        // Si se sube una nueva imagen, elimine la anterior y guarde la nueva
        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe
            if ($event->image && file_exists(public_path($event->image))) {
                unlink(public_path($event->image));
            }

            // Almacenar la nueva imagen
            $imagePath = $request->file('image')->store('images/events', 'public');
            $event->image = $imagePath;
        }

        $event->update([
            'name' => $request->input('name'),
            'tipo' => $request->input('tipo'),
            'date' => $request->input('date'),
            'end_date' => $request->input('end_date'),
            'user_id' => $request->input('user_id'),
            'description' => $request->input('description'),
            'location' => $request->input('location'),
            'image' => $imagePath ?? $event->image, // Mantener la imagen anterior si no se actualiza
        ]);

        // Enviar notificación a todos los usuarios interesados (por ejemplo, organizadores)
        $users = User::all(); // Ajustar según los roles o criterios de usuarios
        foreach ($users as $user) {
            $user->notify(new EventUpdatedNotification($event));
        }

        return redirect()->route('backend.events.index')->with('success', __('event.Event updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = Event::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
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

        $data = Event::with('user')->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('pet::backend.events.trash', compact('data', 'module_name_singular', 'module_action'));
    }
    public function restore($id)
    {
        $data = Event::withTrashed()->find($id);
        $data->restore();

        $message = Str::singular($this->module_title).' Data Restoreded Successfully';

        return redirect('app/events');
    }
    public function update_status(Request $request, Event $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

}
