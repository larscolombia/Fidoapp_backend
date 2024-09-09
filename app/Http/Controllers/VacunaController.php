<?php

namespace App\Http\Controllers;

use App\Models\Vacuna;
use Illuminate\Http\Request;
use Modules\Pet\Models\Pet;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
USE Illuminate\Support\Facades\Log;

class VacunaController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        $this->module_title = 'vacunas.title';
        $this->module_name = 'vacunas.title';
        $this->module_icon = 'fa-solid fa-dumbbell';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function mascotas () {
        $pets = Pet::with('user')->get();

        return view('backend.vacunas.mascotas', compact('pets'));
    }

    public function mascotas_data(DataTables $datatable, Request $request)
    {
        Log::info('bsjs');
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
                $return = '<a href="';
                $return .= route('backend.mascotas.vacunas.index', ['pet' => $pet->id]);
                $return .= '" class="btn btn-primary">';
                $return .= __('vacunas.View Vacunas');
                $return .= '</a>';
                return $return;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function index($pet) {
        $vacunas = Vacuna::with('pet')->where('pet_id', $pet);

        return view('backend.vacunas.index', compact('pet', 'vacunas'));
    }

    public function vacunas_data (DataTables $datatable, Request $request, $pet) {
        $vacunas = Vacuna::with('pet')->where('pet_id', $pet);
        Log::info('aaa');
        return $datatable->eloquent($vacunas)
            ->addColumn('pet_type', function ($vacuna) {
                return $vacuna->pet->pettype->name;
            })
            ->addColumn('vacuna_name', function ($vacuna) {
                Log::info($vacuna);
                return $vacuna->vacuna_name;
            })
            ->addColumn('fecha_aplication', function ($vacuna) {
                return $vacuna->fecha_aplicacion;
            })
            ->addColumn('fecha_refuerzo_vacuna', function ($vacuna) {
                return $vacuna->fecha_refuerzo_vacuna;
            })
            ->addColumn('action', function ($data) {
                return view('backend.vacunas.action_columns', compact('data'));
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function create($pet)
    {
        return view('backend.vacunas.create', compact('pet'));
    }

    public function store(Request $request, Pet $pet)
    {
        // Validación de los datos recibidos
        $request->validate([
            'vacuna_name' => 'required|string|max:255',
            'fecha_aplicacion' => 'required|date',
            'fecha_refuerzo_vacuna' => 'required|date|after_or_equal:fecha_aplicacion',
        ]);

        // Crear la nueva vacuna asociada a la mascota
        $vacuna = new Vacuna();
        $vacuna->pet_id = $pet->id;
        $vacuna->vacuna_name = $request->vacuna_name;
        $vacuna->fecha_aplicacion = $request->fecha_aplicacion;
        $vacuna->fecha_refuerzo_vacuna = $request->fecha_refuerzo_vacuna;
        $vacuna->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('backend.mascotas.vacunas.index', ['pet' => $pet->id])
                        ->with('success', __('Vacuna creada exitosamente.'));
    }


    public function show(Pet $pet, Vacuna $vacuna)
    {
        // Mostrar los detalles de la vacuna
        return view('backend.vacunas.show', compact('pet', 'vacuna'));
    }


    public function edit(Pet $pet, Vacuna $vacuna)
    {
        // Pasamos la mascota y la vacuna a la vista
        return view('backend.vacunas.edit', compact('pet', 'vacuna'));
    }


    public function update(Request $request, Pet $pet, Vacuna $vacuna)
    {
        // Validar los datos actualizados
        $request->validate([
            'vacuna_name' => 'required|string|max:255',
            'fecha_aplicacion' => 'required|date',
            'fecha_refuerzo_vacuna' => 'required|date|after_or_equal:fecha_aplicacion',
        ]);

        // Actualizar los datos de la vacuna
        $vacuna->vacuna_name = $request->vacuna_name;
        $vacuna->fecha_aplicacion = $request->fecha_aplicacion;
        $vacuna->fecha_refuerzo_vacuna = $request->fecha_refuerzo_vacuna;
        $vacuna->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('backend.mascotas.vacunas.index', ['pet' => $pet->id])
                        ->with('success', __('Vacuna actualizada exitosamente.'));
    }

    public function destroy(Pet $pet, Vacuna $vacuna)
    {
        // Eliminar la vacuna
        $vacuna->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('backend.mascotas.vacunas.index', ['pet' => $pet])
                        ->with('success', __('Vacuna eliminada exitosamente.'));
    }

}
