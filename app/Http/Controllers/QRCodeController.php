<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Pet\Models\Pet;
use Yajra\DataTables\DataTables;


class QRCodeController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'qr_code.title';
        // module name
        $this->module_name = 'qr_code.title';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function mascotas(Pet $pet)
    {
        $pets = Pet::with('user')->whereHas('pettype', function ($query) {
            $query->where('slug', 'dog');
        })->get();

        return view('backend.qr_codes.mascotas', compact('pet'));
    }

    public function mascotas_data(DataTables $datatable, Request $request)
    {
        $pets = Pet::with('user')->whereHas('pettype', function ($query) {
            $query->where('slug', 'dog');
        })->select('pets.*');

        $filter = $request->filter;
        if (isset($filter)) {
            $search = $filter['search'];
        
            // Aplica la condición de búsqueda en el nombre del dueño y el nombre de la mascota
            $pets->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('breed', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

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
                $action = '';
    
                if ($pet->qr_code_url) {
                    $action .= '<a href="' . $pet->qr_code_url . '" class="btn btn-info" target="_blank">';
                    $action .= __('qr_code.View QR');
                    $action .= '</a>';
                }
    
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
