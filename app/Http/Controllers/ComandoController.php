<?php

namespace App\Http\Controllers;

use App\Models\CategoryComando;
use App\Models\Comando;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;


class ComandoController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'comando_entrenamiento.title';
        // module name
        $this->module_name = 'comando_entrenamiento.title';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

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
        $categories = CategoryComando::all();
        return view('backend.comandos.index', compact('categories'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $comandos = Comando::query();

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['is_favorite'])) {
                $comandos->where('is_favorite', $filter['is_favorite']);
            }

            if (isset($filter['category_id'])) {
                $comandos->where('category_id', $filter['category_id']);
            }
        }

        return $datatable->eloquent($comandos)
            ->addColumn('action', function ($data) {
                return view('backend.comandos.action_column', compact('data'));
            })
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('description', function ($data) {
                return $data->description ?? 'N/A';
            })
            ->addColumn('type', function ($data) {
                return $data->type;
            })  
            ->addColumn('is_favorite', function ($data) {
                return $data->is_favorite ? 'Sí' : 'No';
            }) 
            ->addColumn('category', function ($data) {
                return $data->category->name;
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
              ->rawColumns(['action', 'is_favorite'])
              ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = CategoryComando::all();
        return view('backend.comandos.create', compact('categories'));
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:especializado,basico',
            'is_favorite' => 'required|boolean',
            'category_id' => 'required|exists:category_comandos,id',
            'instructions' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Comando::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'is_favorite' => $request->is_favorite,
            'category_id' => $request->category_id,
            'instructions' => $request->instructions,
        ]);

        return redirect()->route('backend.comandos.index')->with('success', __('comando_entrenamiento.created_successfully'));
    }

    public function toggleFavorite(Request $request)
    {
        $comando = Comando::findOrFail($request->id);
        $comando->is_favorite = $request->is_favorite;
        $comando->save();

        return response()->json(['success' => true]);
    }

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $category = CategoryComando::create([
            'name' => $request->name,
        ]);

        return response()->json(['success' => true, 'category' => $category]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comando = Comando::with('category')->findOrFail($id);
        return view('backend.comandos.show', compact('comando'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $comando = Comando::findOrFail($id);
        $categories = CategoryComando::all();
        return view('backend.comandos.edit', compact('comando', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:especializado,basico',
            'is_favorite' => 'required|boolean',
            'category_id' => 'required|exists:category_comandos,id',
            'instructions' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $comando = Comando::findOrFail($id);
        $comando->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'is_favorite' => $request->is_favorite,
            'category_id' => $request->category_id,
            'instructions' => $request->instructions,
        ]);

        return redirect()->route('backend.comandos.index')->with('success', __('comando_entrenamiento.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comando = Comando::findOrFail($id);
        $comando->delete();

        return redirect()->route('backend.comandos.index')->with('success', __('comando_entrenamiento.deleted_successfully'));
    }
}
