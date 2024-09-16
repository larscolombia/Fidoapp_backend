<?php

namespace App\Http\Controllers;

use App\Models\Diario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Pet\Models\Pet;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;


class DiarioController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'diarios.title';
        // module name
        $this->module_name = 'diarios.title';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function mascotas () {
        $pets = Pet::with('user')->get();

        return view('backend.diarios.mascotas', compact('pets'));
    }

    public function mascotas_data(DataTables $datatable, Request $request)
    {
        Log::info('Diarios');
        $pets = Pet::with('user')->select('pets.*');

        return $datatable->eloquent($pets)
            ->addColumn('owner_name', function ($pet) {
                return $pet->user->first_name . ' ' . $pet->user->last_name;
            })
            ->addColumn('pet_name', function ($pet) {
                return $pet->name;
            })
            ->addColumn('breed', function ($pet) {
                return $pet->breed->name;
            })
            ->addColumn('action', function ($pet) {
                $currentUrl = url()->current();
                $routeName = '';
                $buttonText = '';
                $routeName = 'backend.mascotas.diarios.index';
                $buttonText = __('diarios.View Diarios');
                $return = '<a href="';
                $return .= route($routeName, ['pet' => $pet->id]);
                $return .= '" class="btn btn-primary">';
                $return .= $buttonText;
                $return .= '</a>';
                return $return;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function index($pet) {
        $diarios = Diario::with('pet')->where('pet_id', $pet);

        return view('backend.diarios.index', compact('pet'));
    }

    public function diarios_data(DataTables $datatable, Request $request, $pet)
    {
        $diarios = Diario::with('pet')->where('pet_id', $pet);
        
        return $datatable->eloquent($diarios)
            ->addColumn('mascota', function ($diario) {
                return $diario->pet->name;
            })
            ->addColumn('date', function ($diario) {
                return $diario->date;
            })
            ->addColumn('activity', function ($diario) {
                return isset($diario->actividad) ? $diario->actividad : 'N/A';
            })
            ->addColumn('notes', function ($diario) {
                return $diario->notas;
            })
            ->addColumn('action', function ($data) {
                return view('backend.diarios.action_columns', compact('data'));
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show($pet, $diario)
    {
        $diario = Diario::findOrFail($diario);
        return view('backend.diarios.show', compact('diario'));
    }

    public function create($pet)
    {
        return view('backend.diarios.create', compact('pet'));
    }

    public function store(Request $request, $pet)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'actividad' => 'required|string|max:255',
            'notas' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.avif';
            $imagePath = public_path('images/diarios/' . $imageName);

            // Usar la función de conversión a AVIF
            $convertedPath = convertToAvif($image, $imagePath);

            if (!$convertedPath) {
                return redirect()->back()->withErrors(['image' => 'Error al convertir la imagen'])->withInput();
            }

            // Guardar la ruta relativa en la base de datos
            $imagePath = 'images/diarios/' . $imageName;
        }

        Diario::create([
            'date' => $request->input('date'),
            'actividad' => $request->input('actividad'),
            'notas' => $request->input('notas'),
            'pet_id' => $pet,
            'image' => $imagePath,
        ]);

        return redirect()->route('backend.mascotas.diarios.index', ['pet' => $pet])->with('success', __('Diario creado exitosamente.'));
    }

    public function edit($pet, $diario)
    {
        $diario = Diario::findOrFail($diario);
        return view('backend.diarios.edit', compact('diario', 'pet'));
    }

    public function update(Request $request, $pet, $diario)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'actividad' => 'required|string|max:255',
            'notas' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $diario = Diario::findOrFail($diario);

        $imagePath = $diario->image;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/diarios'), $imageName);
            $imagePath = 'images/diarios/' . $imageName;
        }

        $diario->update([
            'date' => $request->input('date'),
            'actividad' => $request->input('actividad'),
            'notas' => $request->input('notas'),
            'image' => $imagePath,
        ]);

        return redirect()->route('backend.mascotas.diarios.index', ['pet' => $pet])->with('success', __('Diarios.Updated successfully'));
    }
}
