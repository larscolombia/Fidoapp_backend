<?php

namespace App\Http\Controllers;

use App\Models\Antigarrapata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Pet\Models\Pet;
use Yajra\DataTables\DataTables;

class GarrapataController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        $this->module_title = 'antigarrapata.title';
        $this->module_name = 'antigarrapata.title';
        $this->module_icon = 'fa-solid fa-dumbbell';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function mascotas () {
        $pets = Pet::with('user')->get();
        return view('backend.antigarrapatas.mascotas', compact('pets'));
    }

    public function mascotas_data(DataTables $datatable, Request $request)
    {
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
                $routeName = 'backend.mascotas.antigarrapatas.index';
                $buttonText = __('antigarrapata.View Antigarrapatas');
            
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
        $antigarrapatas = Antigarrapata::with('pet')->where('pet_id', $pet);

        return view('backend.antigarrapatas.index', compact('pet', 'antigarrapatas'));
    }

    public function antigarrapatas_data (DataTables $datatable, Request $request, $pet) {
        $antigarrapatas = Antigarrapata::with('pet')->where('pet_id', $pet);
        return $datatable->eloquent($antigarrapatas)
            ->addColumn('pet_type', function ($antigarrapata) {
                return $antigarrapata->pet->pettype->name;
            })
            ->addColumn('antigarrapata_name', function ($antigarrapata) {
                return $antigarrapata->antigarrapata_name;
            })
            ->addColumn('fecha_aplicacion', function ($antigarrapata) {
                return $antigarrapata->fecha_aplicacion;
            })
            ->addColumn('fecha_refuerzo_antigarrapata', function ($antigarrapata) {
                return $antigarrapata->fecha_refuerzo_antigarrapata;
            })
            ->addColumn('action', function ($data) {
                return view('backend.antigarrapatas.action_column', compact('data'));
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create(Pet $pet)
    {
        return view('backend.antigarrapatas.create', compact('pet'));
    }

    public function store(Request $request, Pet $pet)
    {
        // Validación de los datos recibidos
        $request->validate([
            'antigarrapata_name' => 'required|string|max:255',
            'fecha_aplicacion' => 'required|date',
            'fecha_refuerzo_antigarrapata' => 'required|date|after_or_equal:fecha_aplicacion',
        ]);

        // Crear la nueva antigarrapata asociada a la mascota
        $antigarrapata = new Antigarrapata();
        $antigarrapata->pet_id = $pet->id;
        $antigarrapata->antigarrapata_name = $request->antigarrapata_name;
        $antigarrapata->fecha_aplicacion = $request->fecha_aplicacion;
        $antigarrapata->fecha_refuerzo_antigarrapata = $request->fecha_refuerzo_antigarrapata;
        $antigarrapata->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('backend.mascotas.antigarrapatas.index', ['pet' => $pet->id])
                        ->with('success', __('Antigarrapata creada exitosamente.'));
    }

    public function show(Pet $pet, Antigarrapata $antigarrapata)
    {
        return view('backend.antigarrapatas.show', compact('pet', 'antigarrapata'));
    }

    public function edit(Pet $pet, Antigarrapata $antigarrapata)
    {
        return view('backend.antigarrapatas.edit', compact('pet', 'antigarrapata'));
    }

    public function update(Request $request, Pet $pet, Antigarrapata $antigarrapata)
    {
        $request->validate([
            'antigarrapata_name' => 'required|string|max:255',
            'fecha_aplicacion' => 'required|date',
            'fecha_refuerzo_antigarrapata' => 'required|date|after_or_equal:fecha_aplicacion',
        ]);

        $antigarrapata->antigarrapata_name = $request->antigarrapata_name;
        $antigarrapata->fecha_aplicacion = $request->fecha_aplicacion;
        $antigarrapata->fecha_refuerzo_antigarrapata = $request->fecha_refuerzo_antigarrapata;
        $antigarrapata->save();

        return redirect()->route('backend.mascotas.antigarrapatas.index', ['pet' => $pet->id])
                        ->with('success', __('Antigarrapata actualizada exitosamente.'));
    }

    public function destroy(Antigarrapata $antigarrapata)
    {
        $antigarrapata->delete();

        return redirect()->route('backend.mascotas.antigarrapatas.index', ['pet' => $antigarrapata->pet->id])
                        ->with('success', __('Antigarrapata eliminada exitosamente.'));
    }
}
