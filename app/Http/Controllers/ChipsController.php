<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Chip;
use App\Models\Fabricante;
use Modules\Pet\Models\Pet;

class ChipsController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'chips.title';
        // module name
        $this->module_name = 'chips.title';

        // module icon
        $this->module_icon = 'fa-solid fa-microchip';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function mascotas () {
        $pets = Pet::with('user')->whereHas('pettype', function ($query) {
            $query->where('slug', 'dog');
        })->get();

        return view('backend.chips.mascotas', compact('pets'));
    }

    public function mascotas_data(DataTables $datatable, Request $request)
    {
        $pets = Pet::with('user')->whereHas('pettype', function ($query) {
            $query->where('slug', 'dog');
        })->select('pets.*');

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
                return view('backend.chips.action_column', compact('pet'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.chips.index');
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $chips = Chip::query();

        return $datatable->eloquent($chips)
            ->addColumn('action', function ($data) {
                return view('backend.chips.action_column', compact('data'));
            })
            ->addColumn('num_identificacion', function ($data) {
                return $data->num_identificacion;
            })
            ->addColumn('fecha_implantacion', function ($data) {
                return $data->fecha_implantacion;
            })
            ->addColumn('nombre_fabricante', function ($data) {
                return $data->nombre_fabricante;
            })
            ->addColumn('num_contacto', function ($data) {
                return $data->num_contacto;
            })
            ->orderColumns(['id'], '-:column $1')
            ->rawColumns(['action'])
            ->toJson();
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Obtener el ID de la mascota desde los parámetros de la consulta
        $petId = $request->query('pet_id');
        
        // Obtener la mascota seleccionada
        $pet = Pet::findOrFail($petId);
        
        // Obtener todos los fabricantes para el select
        $fabricantes = Fabricante::all();

        // Pasar la mascota y los fabricantes a la vista
        return view('backend.chips.create', compact('pet', 'fabricantes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'num_identificacion' => 'required|integer|unique:chips,num_identificacion',
            'pet_id' => 'required|exists:pets,id',
            'fecha_implantacion' => 'required|date',
            'fabricante_id' => 'required|exists:fabricantes,id',
            'num_contacto' => 'required|string|max:15',
        ]);

        // Crear un nuevo chip
        $chip = Chip::create([
            'num_identificacion' => $request->input('num_identificacion'),
            'pet_id' => $request->input('pet_id'),
            'fecha_implantacion' => $request->input('fecha_implantacion'),
            'fabricante_id' => $request->input('fabricante_id'),
            'num_contacto' => $request->input('num_contacto'),
        ]);

        // Redireccionar o devolver una respuesta
        return redirect()->route('backend.mascotas.chips')->with('success', __('chips.chip_created_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $chip = Chip::findOrFail($id);
        return view('backend.chips.show', compact('chip'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Obtener el chip y la mascota relacionada
        $chip = Chip::with('pet')->findOrFail($id);

        // Obtener todos los fabricantes para el select
        $fabricantes = Fabricante::all();

        // Pasar el chip y los fabricantes a la vista
        return view('backend.chips.edit', compact('chip', 'fabricantes'));
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
        // Validar los datos de entrada
        $request->validate([
            'num_identificacion' => 'required|integer|unique:chips,num_identificacion',
            'fecha_implantacion' => 'required|date',
            'fabricante_id' => 'required|exists:fabricantes,id',
            'num_contacto' => 'required|string|max:15',
        ]);

        // Obtener el chip
        $chip = Chip::findOrFail($id);
    
        // Actualizar los datos del chip
        $chip->update([
            'num_identificacion' => $request->input('num_identificacion'),
            'fecha_implantacion' => $request->input('fecha_implantacion'),
            'fabricante_id' => $request->input('fabricante_id'),
            'num_contacto' => $request->input('num_contacto'),
        ]);
    
        // Redireccionar o devolver una respuesta
        return redirect()->route('backend.mascotas.chips')->with('success', __('chips.chip_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $chip = Chip::findOrFail($id);
        $chip->delete();

        return redirect()->route('backend.mascotas.chips')->with('success', __('chips.deleted_successfully'));
    }
}
