<?php

namespace App\Http\Controllers;

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
}
