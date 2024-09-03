<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Pet\Models\Pet;
use Yajra\DataTables\DataTables;

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

        return view('backend.diarios.mascotas', compact('pets'));
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
                $return = '<a href="';
                $return .= route('backend.vacunas.index', ['pet' => $pet->id]);
                $return .= '" class="btn btn-primary">';
                $return .= __('vacunas.View Vacunas');
                $return .= '</a>';
                return $return;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function index () {
        
    }

    public function create() {

    }

    public function store () {

    }

    public function show() {

    }

    public function edit () {

    }

    public function update () {

    }
}
