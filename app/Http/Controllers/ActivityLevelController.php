<?php

namespace App\Http\Controllers;

use App\Models\ActivityLevel;
use Illuminate\Http\Request;
use Modules\Pet\Models\Pet;
use Yajra\DataTables\DataTables;


class ActivityLevelController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'activity_levels.title';
        // module name
        $this->module_name = 'activity_levels.title';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

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

        return view('backend.activity_levels.mascotas', compact('pets'));
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
                return view('backend.activity_levels.action_column', compact('pet'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function index($pet_id)
    {
        return view('backend.activity_levels.index', compact('pet_id'));
    }

    public function index_data(DataTables $datatable, Request $request, $pet_id)
    {
        $query = ActivityLevel::where('pet_id', $pet_id);

        // Aplicar filtros si existen
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        return $datatable->eloquent($query)
            ->addColumn('action', function ($activityLevel) {
                // return view('backend.activity_levels.action_column', compact('activityLevel'));
            })
            ->addColumn('daily_steps', function ($activityLevel) {
                return $activityLevel->daily_steps;
            })
            ->addColumn('distance_covered', function ($activityLevel) {
                return number_format($activityLevel->distance_covered, 2) . ' km';
            })
            ->addColumn('calories_burned', function ($activityLevel) {
                return $activityLevel->calories_burned;
            })
            ->addColumn('active_minutes', function ($activityLevel) {
                return $activityLevel->active_minutes;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create($pet_id)
    {
        $pet = Pet::findOrFail($pet_id);
        return view('backend.activity_levels.create', compact('pet'));
    }

    public function store(Request $request, $pet_id)
    {
        // Validación de los datos de entrada (ninguno de los campos es obligatorio)
        $request->validate([
            'daily_steps' => 'nullable|integer|min:0',
            'distance_covered' => 'nullable|numeric|min:0',
            'calories_burned' => 'nullable|integer|min:0',
            'active_minutes' => 'nullable|integer|min:0',
            'goal_steps' => 'nullable|integer|min:0',
            'goal_distance' => 'nullable|numeric|min:0',
            'goal_calories' => 'nullable|integer|min:0',
            'goal_active_minutes' => 'nullable|integer|min:0',
        ]);
    
        // Crear un nuevo registro de nivel de actividad
        $activityLevel = new ActivityLevel([
            'pet_id' => $pet_id,
            'daily_steps' => $request->input('daily_steps') ?? 0,
            'distance_covered' => $request->input('distance_covered') ?? 0,
            'calories_burned' => $request->input('calories_burned') ?? 0,
            'active_minutes' => $request->input('active_minutes') ?? 0,
            'goal_steps' => $request->input('goal_steps') ?? 0,
            'goal_distance' => $request->input('goal_distance') ?? 0,
            'goal_calories' => $request->input('goal_calories') ?? 0,
            'goal_active_minutes' => $request->input('goal_active_minutes') ?? 0,
        ]);
    
        // Guardar el registro en la base de datos
        $activityLevel->save();
    
        // Redirigir a la página de índices de niveles de actividad con un mensaje de éxito
        return redirect()->route('backend.mascotas.activity_levels')
                         ->with('success', __('activity_levels.created_successfully'));
    }

    public function edit ($id) {
        // Buscar el registro de ActivityLevel por ID
        $activityLevel = ActivityLevel::findOrFail($id);

        // Cargar la vista de edición y pasarle el registro
        return view('backend.activity_levels.edit', compact('activityLevel'));
    }

    public function update(Request $request, $id)
    {
        // Validación de los datos de entrada (ninguno de los campos es obligatorio)
        $request->validate([
            'daily_steps' => 'nullable|integer|min:0',
            'distance_covered' => 'nullable|numeric|min:0',
            'calories_burned' => 'nullable|integer|min:0',
            'active_minutes' => 'nullable|integer|min:0',
            'goal_steps' => 'nullable|integer|min:0',
            'goal_distance' => 'nullable|numeric|min:0',
            'goal_calories' => 'nullable|integer|min:0',
            'goal_active_minutes' => 'nullable|integer|min:0',
        ]);

        // Buscar el registro existente
        $activityLevel = ActivityLevel::findOrFail($id);

        // Actualizar los valores
        $activityLevel->daily_steps = $request->input('daily_steps', $activityLevel->daily_steps);
        $activityLevel->distance_covered = $request->input('distance_covered', $activityLevel->distance_covered);
        $activityLevel->calories_burned = $request->input('calories_burned', $activityLevel->calories_burned);
        $activityLevel->active_minutes = $request->input('active_minutes', $activityLevel->active_minutes);
        $activityLevel->goal_steps = $request->input('goal_steps', $activityLevel->goal_steps);
        $activityLevel->goal_distance = $request->input('goal_distance', $activityLevel->goal_distance);
        $activityLevel->goal_calories = $request->input('goal_calories', $activityLevel->goal_calories);
        $activityLevel->goal_active_minutes = $request->input('goal_active_minutes', $activityLevel->goal_active_minutes);

        // Guardar los cambios en la base de datos
        $activityLevel->save();

        // Redirigir a la página de índices de niveles de actividad con un mensaje de éxito
        return redirect()->route('backend.mascotas.activity_levels', $request->input('pet_id'))
                        ->with('success', __('activity_levels.updated_successfully'));
    }

    public function update_steps(Request $request, $id)
    {
        // Validar los datos
        $request->validate([
            'daily_steps' => 'nullable|integer|min:0',
            'goal_steps' => 'nullable|integer|min:0',
        ]);

        // Buscar el registro de ActivityLevel por ID
        $activityLevel = ActivityLevel::findOrFail($id);

        // Actualizar los valores
        $activityLevel->daily_steps = $request->input('daily_steps', $activityLevel->daily_steps);
        $activityLevel->goal_steps = $request->input('goal_steps', $activityLevel->goal_steps);

        // Guardar cambios
        $activityLevel->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('backend.mascotas.activity_levels', $activityLevel->pet_id)
                        ->with('success', __('activity_levels.updated_successfully'));
    }

    public function update_distance(Request $request, $id)
    {
        // Validar los datos
        $request->validate([
            'distance_covered' => 'nullable|numeric|min:0',
            'goal_distance' => 'nullable|numeric|min:0',
        ]);

        // Buscar el registro de ActivityLevel por ID
        $activityLevel = ActivityLevel::findOrFail($id);

        // Actualizar los valores
        $activityLevel->distance_covered = $request->input('distance_covered', $activityLevel->distance_covered);
        $activityLevel->goal_distance = $request->input('goal_distance', $activityLevel->goal_distance);

        // Guardar cambios
        $activityLevel->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('backend.mascotas.activity_levels', $activityLevel->pet_id)
                        ->with('success', __('activity_levels.updated_successfully'));
    }

    public function update_calories(Request $request, $id)
    {
        // Validar los datos
        $request->validate([
            'calories_burned' => 'nullable|integer|min:0',
            'goal_calories' => 'nullable|integer|min:0',
        ]);

        // Buscar el registro de ActivityLevel por ID
        $activityLevel = ActivityLevel::findOrFail($id);

        // Actualizar los valores
        $activityLevel->calories_burned = $request->input('calories_burned', $activityLevel->calories_burned);
        $activityLevel->goal_calories = $request->input('goal_calories', $activityLevel->goal_calories);

        // Guardar cambios
        $activityLevel->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('backend.mascotas.activity_levels', $activityLevel->pet_id)
                        ->with('success', __('activity_levels.updated_successfully'));
    }

    public function update_minutes(Request $request, $id)
    {
        // Validar los datos
        $request->validate([
            'active_minutes' => 'nullable|integer|min:0',
            'goal_active_minutes' => 'nullable|integer|min:0',
        ]);

        // Buscar el registro de ActivityLevel por ID
        $activityLevel = ActivityLevel::findOrFail($id);

        // Actualizar los valores
        $activityLevel->active_minutes = $request->input('active_minutes', $activityLevel->active_minutes);
        $activityLevel->goal_active_minutes = $request->input('goal_active_minutes', $activityLevel->goal_active_minutes);

        // Guardar cambios
        $activityLevel->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('backend.mascotas.activity_levels', $activityLevel->pet_id)
                        ->with('success', __('activity_levels.updated_successfully'));
    }

    public function store_steps(Request $request, $pet_id)
    {
        $request->validate([
            'daily_steps' => 'nullable|integer|min:0',
            'goal_steps' => 'nullable|integer|min:0',
        ]);

        $activityLevel = new ActivityLevel([
            'pet_id' => $pet_id,
            'daily_steps' => $request->input('daily_steps'),
            'goal_steps' => $request->input('goal_steps'),
        ]);
        $activityLevel->save();

        return redirect()->route('backend.mascotas.activity_levels', $pet_id)
                        ->with('success', __('activity_levels.created_successfully'));
    }

    public function store_distance(Request $request, $pet_id)
    {
        $request->validate([
            'distance_covered' => 'nullable|numeric|min:0',
            'goal_distance' => 'nullable|numeric|min:0',
        ]);

        $activityLevel = new ActivityLevel([
            'pet_id' => $pet_id,
            'distance_covered' => $request->input('distance_covered'),
            'goal_distance' => $request->input('goal_distance'),
        ]);
        $activityLevel->save();

        return redirect()->route('backend.mascotas.activity_levels', $pet_id)
                        ->with('success', __('activity_levels.created_successfully'));
    }

    public function store_calories(Request $request, $pet_id)
    {
        $request->validate([
            'calories_burned' => 'nullable|integer|min:0',
            'goal_calories' => 'nullable|integer|min:0',
        ]);

        $activityLevel = new ActivityLevel([
            'pet_id' => $pet_id,
            'calories_burned' => $request->input('calories_burned'),
            'goal_calories' => $request->input('goal_calories'),
        ]);
        $activityLevel->save();

        return redirect()->route('backend.mascotas.activity_levels', $pet_id)
                        ->with('success', __('activity_levels.created_successfully'));
    }

    public function store_minutes(Request $request, $pet_id)
    {
        $request->validate([
            'active_minutes' => 'nullable|integer|min:0',
            'goal_active_minutes' => 'nullable|integer|min:0',
        ]);

        $activityLevel = new ActivityLevel([
            'pet_id' => $pet_id,
            'active_minutes' => $request->input('active_minutes'),
            'goal_active_minutes' => $request->input('goal_active_minutes'),
        ]);
        $activityLevel->save();

        return redirect()->route('backend.mascotas.activity_levels', $pet_id)
                        ->with('success', __('activity_levels.created_successfully'));
    }
}
