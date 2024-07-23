<?php

namespace App\Http\Controllers;

use App\Models\Herramienta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;



class HerramientaController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Herramientas de Entrenamiento';
        // module name
        $this->module_name = 'Herramientas de Entrenamiento';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function index()
    {
        $export_import = false;

        return view('backend.herramientas_entrenamiento.index', compact('export_import'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $herramientas = Herramienta::query();

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['status'])) {
                $herramientas->where('status', $filter['status']);
            }

            if (isset($filter['type'])) {
                $herramientas->where('type', $filter['type']);
            }
        }

        return $datatable->eloquent($herramientas)
            ->addColumn('action', function ($data) {
                return view('backend.herramientas_entrenamiento.action_column', compact('data'));
            })
            ->editColumn('name', function ($data) {
                return $data->name;
            })
            ->editColumn('description', function ($data) {
                return $data->description ?? 'N/A';
            })
            ->editColumn('type', function ($data) {
                return $data->type;
            })
            ->editColumn('status', function ($data) {
                return $data->status;
            })
            ->editColumn('updated_at', function ($data) {
                if ($data->updated_at === null) {
                    return 'N/A'; // O cualquier otro valor predeterminado adecuado
                }

                $diff = Carbon::now()->diffInHours($data->updated_at);
                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1')
            ->rawColumns(['action', 'check'])
            ->toJson();
    }

    public function create()
    {
        return view('backend.herramientas_entrenamiento.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:clicker,silbato,diarios',
            'status' => 'required|string|max:255',
            'audio' => 'required|mimes:mp3,wav,aac|max:10240', // Validación del archivo de audio
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

         // Manejar la carga del archivo de audio
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->move(public_path('audios/herramientas'), $request->file('audio')->getClientOriginalName());
        }

        Herramienta::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'status' => $request->status,
            'audio' => 'audios/herramientas/' . $request->file('audio')->getClientOriginalName(),
        ]);

        return redirect()->route('backend.herramientas_entrenamiento.index')->with('success', __('Herramienta de entrenamiento creada exitosamente.'));
    }

    public function edit($herramientas_entrenamiento)
    {
        $herramienta = Herramienta::findOrFail($herramientas_entrenamiento);
        return view('backend.herramientas_entrenamiento.edit', compact('herramienta'));
    }

    public function update(Request $request, $herramientas_entrenamiento)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:clicker,silbato,diarios',
            'status' => 'required|in:active,inactive',
            'audio' => 'nullable|mimes:mp3,wav',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $herramienta = Herramienta::findOrFail($herramientas_entrenamiento);
        
        $herramienta->name = $request->input('name');
        $herramienta->description = $request->input('description');
        $herramienta->type = $request->input('type');
        $herramienta->status = $request->input('status');

        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('audios/herramientas', 'public');
            $herramienta->audio = 'audios/herramientas/' . basename($audioPath);
        }

        $herramienta->save();

        return redirect()->route('backend.herramientas_entrenamiento.index')->with('success', __('herramientas_entrenamiento.Actualizado correctamente'));
    }

    public function destroy($herramientas_entrenamiento)
    {
        $herramienta = Herramienta::findOrFail($herramientas_entrenamiento);
        $herramienta->delete();

        return redirect()->route('backend.herramientas_entrenamiento.index')->with('success', __('Herramienta de entrenamiento eliminada exitosamente.'));
    }

    public function show($herramientas_entrenamiento)
    {
        $herramienta = Herramienta::findOrFail($herramientas_entrenamiento);
        return view('backend.herramientas_entrenamiento.show', compact('herramienta'));
    }
}
