<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Chip;

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
    public function create()
    {
        return view('backend.chips.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'num_identificacion' => 'required|integer|unique:chips,num_identificacion',
            'fecha_implantacion' => 'required|date',
            'nombre_fabricante' => 'required|string|max:255',
            'num_contacto' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Chip::create([
            'num_identificacion' => $request->num_identificacion,
            'fecha_implantacion' => $request->fecha_implantacion,
            'nombre_fabricante' => $request->nombre_fabricante,
            'num_contacto' => $request->num_contacto,
        ]);

        return redirect()->route('backend.chips.index')->with('success', __('chips.created_successfully'));
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
        $chip = Chip::findOrFail($id);
        return view('backend.chips.edit', compact('chip'));
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
        $validator = Validator::make($request->all(), [
            'num_identificacion' => 'required|integer|unique:chips,num_identificacion,' . $id,
            'fecha_implantacion' => 'required|date',
            'nombre_fabricante' => 'required|string|max:255',
            'num_contacto' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $chip = Chip::findOrFail($id);
        $chip->update([
            'num_identificacion' => $request->num_identificacion,
            'fecha_implantacion' => $request->fecha_implantacion,
            'nombre_fabricante' => $request->nombre_fabricante,
            'num_contacto' => $request->num_contacto,
        ]);

        return redirect()->route('backend.chips.index')->with('success', __('chips.updated_successfully'));
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

        return redirect()->route('backend.chips.index')->with('success', __('chips.deleted_successfully'));
    }
}
