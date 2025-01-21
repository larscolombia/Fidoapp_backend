<?php

namespace App\Http\Controllers\Backend;

use App\Models\Speciality;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class SpecialityController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'specialities.title';
        // module name
        $this->module_name = 'specialities.title';

        // module icon
        $this->module_icon = 'fas fa-certificate';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }
    public function index()
    {
        $specialities = Speciality::all();
        return view('backend.specialities.index', compact('specialities'));
    }

    public function indexDatatable(Datatables $datatable, Request $request)
    {
        $specialities = Speciality::query();

        $filter = $request->filter;

        $posOrder = [];

        if (isset($filter)) {
        }

        return $datatable->eloquent($specialities)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('backend.specialities.action_column', compact('data'));
            })
            ->editColumn('description', function ($data) {
                return $data->description ?? 'N/A';
            })

            ->editColumn('updated_at', function ($data) {
                return $data->updated_at ? $data->updated_at->format('d-m-Y') : 'N/A';
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at ? $data->created_at->format('d-m-Y') : 'N/A';
            })
            ->orderColumns(['id'], '-:column $1')
            ->rawColumns(['action', 'check'])
            ->toJson();
    }

    public function create()
    {
        return view('backend.specialities.create');
    }

    public function edit($id)
    {
        $speciality = Speciality::find($id);
        if ($speciality) {
            return view('backend.specialities.edit', compact('speciality'));
        }
        return redirect()->route('backend.specialities.index')->with('error', __('specialities.failed'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => ['required', 'max:255', 'string'],
        ]);
        $speciality = Speciality::create([
            'description' => $data['description']
        ]);
        return redirect()->route('backend.specialities.index')->with('success', __('specialities.created_successfully'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'description' => ['required', 'max:255', 'string'],
        ]);
        $speciality = Speciality::find($id);
        if ($speciality) {
            $speciality->description = $data['description'];
            $speciality->save();
            return redirect()->route('backend.specialities.index')->with('success', __('specialities.updated_successfully'));
        }
        return redirect()->route('backend.specialities.index')->with('error', __('specialities.failed'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'delete':
                Speciality::whereIn('id', $ids)->delete();
                $message = __('specialities.deleted_successfully_specialities');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function destroy($id)
    {
        $speciality = Speciality::find($id);
        if ($speciality) {
            $speciality->delete();

            return redirect()->route('backend.specialities.index')->with('success', __('specialities.deleted_successfully'));
        }
        return redirect()->route('backend.specialities.index')->with('error', __('specialities.failed'));
    }
}
