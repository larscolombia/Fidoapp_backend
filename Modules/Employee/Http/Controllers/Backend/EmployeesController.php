<?php

namespace Modules\Employee\Http\Controllers\Backend;

use Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Authorizable;
use App\Models\Branch;
use App\Models\Setting;
use App\Models\BlogRating;
use App\Models\BookRating;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\CoursePlatformVideoRating;
use Illuminate\Database\Query\Expression;
use Modules\Commission\Models\Commission;
use Modules\CustomField\Models\CustomField;
use Modules\Employee\Models\BranchEmployee;
use Modules\Employee\Models\EmployeeRating;
use Modules\Service\Models\ServiceEmployee;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Commission\Models\EmployeeCommission;
use Modules\Employee\Http\Requests\EmployeeRequest;

class EmployeesController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'employee.title';

        // module name
        $this->module_name = 'employees';

        // directory path of the module
        $this->module_path = 'employee::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new User());
        $customefield = CustomField::exportCustomFields(new User());
        $type = request()->employee_type;

        switch ($type) {

            case 'boarder':

                $module_title = 'menu.boarder_list';
                $create_title = 'booking.lbl_care_taker';

                break;
            case 'vet':
                $module_title = 'menu.veterinarian_list';
                $create_title  = 'booking.lbl_veterinarian';

                break;
            case 'groomer':
                $module_title = 'menu.groomer_list';
                $create_title  = 'booking.lbl_groomer';

                break;

            case 'trainer':
                $module_title = 'menu.trainer_list';
                $create_title  = 'booking.lbl_trainer';

                break;
            case 'walker':
                $module_title = 'menu.walker_list';
                $create_title  = 'booking.lbl_walker';

                break;

            case 'day_taker':
                $module_title = 'menu.daycare_taker_list';
                $create_title = 'booking.lbl_daycare_taker';

                break;
            case 'pet_sitter':
                $module_title = 'menu.pet_sitter_list';
                $create_title = 'booking.lbl_pet_sitter';

                break;
            case 'pending_employee':
                $module_title = 'menu.pending_employee';
                $create_title = 'booking.lbl_pending_employee';

                break;
            default:

                $module_title = 'menu.lbl_employee_list';
                $create_title = 'menu.lbl_employee';

                break;
        }

        // $module_title = 'menu.lbl_employee_list';
        //  $create_title = 'menu.lbl_employee';


        $filter = [
            'commission' => $request->commission,


        ];


        $commissions_list = Commission::all();



        return view('employee::backend.employees.index', compact('module_action', 'columns', 'customefield', 'type', 'module_title', 'create_title', 'commissions_list', 'filter'));
    }

    public function employees_type_data($type)
    {


        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new User());
        $customefield = CustomField::exportCustomFields(new User());


        switch ($type) {
            case 'boarder':

                $module_title = 'menu.boarder_list';
                $create_title = 'booking.lbl_care_taker';

                break;
            case 'vet':
                $module_title = 'menu.veterinarian_list';
                $create_title  = 'booking.lbl_veterinarian';

                break;
            case 'groomer':
                $module_title = 'menu.groomer_list';
                $create_title  = 'booking.lbl_groomer';

                break;

            case 'trainer':
                $module_title = 'menu.trainer_list';
                $create_title  = 'booking.lbl_trainer';

                break;
            case 'walker':
                $module_title = 'menu.walker_list';
                $create_title  = 'booking.lbl_walker';

                break;

            case 'day_taker':
                $module_title = 'menu.daycare_taker_list';
                $create_title = 'booking.lbl_daycare_taker';

                break;
            case 'pet_sitter':
                $module_title = 'menu.pet_sitter_list';
                $create_title = 'booking.lbl_pet_sitter';

                break;

            case 'pending_employee':
                $module_title = 'menu.pending_employee';
                $create_title = 'booking.lbl_pending_employee';

                break;

            default:
                $module_title = 'menu.lbl_employee_list';
                $create_title = 'menu.lbl_employee';

                break;
        }

        // $module_title = 'menu.lbl_employee_list';
        //  $create_title = 'menu.lbl_employee';


        return view('employee::backend.employees.index', compact('module_action', 'columns', 'customefield', 'type', 'module_title', 'create_title'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        $query_data = Branch::where('status', 1)
            ->where(function ($q) use ($term) {
                if (!empty($term)) {
                    $q->orWhere('name', 'LIKE', "%$term%");
                }
            })->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,

            ];
        }

        return response()->json($data);
    }

    public function employee_list(Request $request)
    {
        $term = trim($request->q);

        $branchId = $request->branch_id;

        $role = $request->role;

        // Need To Add Role Base
        //codigo original
        // $query_data = User::role(['vet', 'groomer', 'walker', 'boarder', 'pet_sitter', 'trainer'])->with('media', 'branches')->where(function ($q) use ($term) {
        //     if (!empty($term)) {
        //         $q->orWhere('first_name', 'LIKE', "%$term%");
        //         $q->orWhere('last_name', 'LIKE', "%$term%");
        //     }
        // });

        $query_data = User::role(['vet','trainer'])->with('media', 'branches')->where(function ($q) use ($term) {
            if (!empty($term)) {
                $q->orWhere('first_name', 'LIKE', "%$term%");
                $q->orWhere('last_name', 'LIKE', "%$term%");
            }
        });

        if ($request->show_in_calender) {
            $query_data->CalenderResource();
        }

        if (!empty($role)) {
            $query_data->role($role);
        }

        if (isset($branchId) && !empty($branchId)) {
            $query_data->whereHas('branches', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $query_data = $query_data->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->full_name,
                'avatar' => $row->profile_image,
            ];
        }

        return response()->json($data);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                // Need To Add Role Base
                $employee = User::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_employee_update');
                break;

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                User::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_employee_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {


        $module_name = $this->module_name;
        $query = User::select('users.*')->branch()->whereNotNull('email_verified_at')->with('media', 'mainBranch', 'commissions_data');


        if ($request->has('type')) {

            if ($request->type == 'pending_employee') {

                $query = User::select('users.*')->branch()->whereNull('email_verified_at')->with('media', 'mainBranch', 'commissions_data');
            } else {

                $query = $query->role($request->type);
            }
        }

        $filter = $request->filter;


        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->whereHas('commissions_data', function ($subquery) use ($filter) {
                    $subquery->where('commission_id', $filter['column_status']);
                });
            }
        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) {

                $other_settings = Setting::where('name', 'is_provider_push_notification')->first();

                $enable_push_notification = 0;

                if (!empty($other_settings)) {

                    $enable_push_notification = $other_settings->val;
                }
                return view('employee::backend.employees.action_column', compact('data', 'enable_push_notification'));
            })
            ->editColumn('user_id', function ($data) {
                return view('employee::backend.employees.user_id', compact('data'));
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
                }
            })
            ->orderColumn('user_id', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)
            ->editColumn('image', function ($data) {
                return "<img src='" . $data->profile_image . "'class='avatar avatar-50 rounded-pill'>";
            })

            ->editColumn('email_verified_at', function ($data) {

                return view('employee::backend.employees.verify_action', compact('data'));
            })
            ->editColumn('user_type', function ($data) {
                $userType = $this->getTranslateUserType($data->user_type);
                return '<span class="badge booking-status bg-soft-primary p-3">' . str_replace("_", "", ucfirst($userType)) . '</span>';
            })
            ->editColumn('full_name', function ($data) {
                return $data->first_name . ' ' . $data->last_name;
            })
            ->filterColumn('full_name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%');
                }
            })
            ->orderColumn('full_name', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)
            // ->orderColumn('full_name', function ($query, $order) {
            //     $query->orderBy(new Expression('(SELECT first_name FROM users LIMIT 1)'), $order);
            // }, 1)
            ->addColumn('branch_id', function ($data) {
                return optional($data->mainBranch)->pluck('name')->toArray() ?? '-';
            })
            ->editColumn('is_banned', function ($data) {
                $checked = '';
                if ($data->is_banned) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.employees.block-employee', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $data->id . '"  name="is_banned" value="' . $data->id . '" ' . $checked . '>
                    </div>
                 ';
            })

            ->editColumn('status', function ($data) {
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.employees.update_status', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $data->id . '"  name="status" value="' . $data->id . '" ' . $checked . '>
                    </div>
                ';
            })

            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, User::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'is_banned', 'email_verified_at', 'check', 'image', 'user_type'], $customFieldColumns))
            ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(EmployeeRequest $request)
    {

        $data = $request->all();

        $data['password'] = Hash::make($data['password']);

        if ($request->confirmed == 1) {
            $data = \Arr::add($data, 'email_verified_at', Carbon::now());
        } else {
            $data = \Arr::add($data, 'email_verified_at', null);
        }

        $data = User::create($data);

        $profile = [
            'about_self' => $request->about_self,
            'expert' => $request->expert,
            'facebook_link' => $request->facebook_link,
            'instagram_link' => $request->instagram_link,
            'twitter_link' => $request->twitter_link,
            'dribbble_link' => $request->dribbble_link,
        ];

        $data->profile()->updateOrCreate([], $profile);

        if ($request->custom_fields_data) {

            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        if ($request->has('profile_image')) {

            $request->file('profile_image');

            storeMediaFile($data, $request->file('profile_image'), 'profile_image');
        }

        $employee_id = $data['id'];

        $roles = [$request->user_type];

        // $roles = ['vet','groomer','walker','boarder','pet_sitter','trainer'];

        // if ($request->is_manager) {
        //     array_push($roles, 'manager');
        //     if ($request->has('branch_id')) {
        //         $branch = Branch::where('id', $request->branch_id)->first();
        //         $branch->update(['manager_id' => $employee_id]);
        //     }
        // }

        $data->syncRoles($roles);

        if ($request->has('branch_id')) {

            $branch_data = [
                'employee_id' => $employee_id,
                'branch_id' => $request->branch_id,
            ];
            BranchEmployee::create($branch_data);
        }

        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('config:cache');


        if ($request->has('service_id')) {

            if ($request->service_id !== null) {

                $services = explode(',', $request->service_id);

                foreach ($services as $value) {

                    $service_data = [

                        'employee_id' => $employee_id,
                        'service_id' => $value,

                    ];
                    ServiceEmployee::create($service_data);
                }
            }
        }
        if (isset($request->commission_id) && $request->has('commission_id')) {
            if ($request->commission_id !== null) {

                $commissions = explode(',', $request->commission_id);

                foreach ($commissions as $value) {
                    $commission_data = [
                        'employee_id' => $employee_id,
                        'commission_id' => $value,
                    ];

                    EmployeeCommission::create($commission_data);
                }
            }
        }

        $message = __('messages.create_form', ['form' => __('employee.singular_title')]);

        return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_action = 'Show';

        //codigo original
        //$data = User::role(['vet', 'groomer', 'walker', 'boarder', 'pet_sitter', 'trainer'])->findOrFail($id);
        $data = User::role(['vet','trainer'])->findOrFail($id);
        return view('employee::backend.employees.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //consulta original
       // $data = User::role(['vet', 'groomer', 'walker', 'boarder', 'trainer', 'day_taker', 'pet_sitter'])->where('id', $id)->with('branches', 'services', 'commissions_data', 'profile')->first();
       $data = User::role(['vet', 'trainer'])->where('id', $id)->with('branches', 'services', 'commissions_data', 'profile')->first();

        if (!is_null($data)) {
            $custom_field_data = $data->withCustomFields();
            $data['custom_field_data'] = collect($custom_field_data->custom_fields_data)
                ->filter(function ($value) {
                    return $value !== null;
                })
                ->toArray();
        }

        $data['branch_id'] = $data->branch->branch_id ?? null;

        $data['service_id'] = $data->services->pluck('service_id') ?? [];

        $data['commission_id'] = $data->commissions_data->pluck('commission_id') ?? [];

        $data['profile_image'] = $data->profile_image;

        $data['about_self'] = $data->profile->about_self ?? null;

        $data['expert'] = $data->profile->expert ?? null;

        $data['facebook_link'] = $data->profile->facebook_link ?? null;

        $data['instagram_link'] = $data->profile->instagram_link ?? null;

        $data['twitter_link'] = $data->profile->twitter_link ?? null;

        $data['dribbble_link'] = $data->profile->dribbble_link ?? null;

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(EmployeeRequest $request, $id)
    {
        //codigo original
        //$data = User::role(['vet', 'groomer', 'walker', 'boarder', 'trainer', 'day_taker', 'pet_sitter'])->findOrFail($id);
        $data = User::role(['vet', 'trainer'])->findOrFail($id);
        $request_data = $request->except('profile_image');

        if ($request_data['password'] != '') {

            $request_data['password'] = Hash::make($request_data['password']);
        } else {

            $request_data = $request->except('password');
        }

        $data->update($request_data);

        $profile = [
            'about_self' => $request->about_self,
            'expert' => $request->expert,
            'facebook_link' => $request->facebook_link,
            'instagram_link' => $request->instagram_link,
            'twitter_link' => $request->twitter_link,
            'dribbble_link' => $request->dribbble_link,
        ];

        $data->profile()->updateOrCreate([], $profile);

        if ($request->custom_fields_data) {

            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        if ($request->file('profile_image') != null) {

            storeMediaFile($data, $request->file('profile_image'), 'profile_image');
        }


        BranchEmployee::where('employee_id', $id)->delete();

        ServiceEmployee::where('employee_id', $id)->delete();

        EmployeeCommission::where('employee_id', $id)->delete();

        $roles = [$request->user_type];

        $employee_id = $data->id;

        // if ($request->is_manager) {
        //     array_push($roles, 'manager');
        //     if ($request->has('branch_id')) {
        //         $branch = Branch::where('id', $request->branch_id)->first();
        //         $branch->update(['manager_id' => $employee_id]);
        //     }
        // }

        $data->syncRoles($roles);

        if ($request->has('branch_id')) {

            $branch_data = [
                'employee_id' => $id,
                'branch_id' => $request->branch_id,
            ];

            BranchEmployee::create($branch_data);
        }

        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('config:cache');


        if ($request->has('service_id')) {

            if ($request->service_id !== null) {

                $services = explode(',', $request->service_id);

                foreach ($services as $value) {

                    $service_data = [

                        'employee_id' => $employee_id,
                        'service_id' => $value,

                    ];
                    ServiceEmployee::create($service_data);
                }
            }
        }

        if (isset($request->commission_id) && $request->has('commission_id')) {
            if ($request->commission_id !== null) {

                $commissions = explode(',', $request->commission_id);

                foreach ($commissions as $value) {
                    $commission_data = [
                        'employee_id' => $employee_id,
                        'commission_id' => $value,
                    ];

                    EmployeeCommission::create($commission_data);
                }
            }
        }
        $usertype = str_replace("_", " ", ucfirst($request->user_type));

        $message = __('messages.update_form', ['form' => $usertype]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }
        //codigo original
        //$data = User::role(['vet', 'groomer', 'walker', 'boarder', 'trainer', 'day_taker', 'pet_sitter'])->findOrFail($id);
        $data = User::role(['vet', 'trainer','user'])->findOrFail($id);
        $data->delete();

        $message = __('messages.delete_form', ['form' => __('employee.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function update_status(Request $request, User $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function change_password(Request $request)
    {

        $data = $request->all();

        $employee_id = $data['employee_id'];
        //codigo original
        //$data = User::role(['vet', 'groomer', 'walker', 'boarder', 'trainer', 'day_taker', 'pet_sitter'])->findOrFail($employee_id);
        $data = User::role(['vet',  'trainer','user','admin'])->findOrFail($employee_id);

        $request_data = $request->only('password');
        $request_data['password'] = Hash::make($request_data['password']);

        $data->update($request_data);

        $message = __('messages.password_update');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function block_employee(Request $request, User $id)
    {

        $id->update(['is_banned' => $request->status]);

        if ($request->status == 1) {

            $message = __('messages.employee_block');
        } else {

            $message = __('messages.employee_unblock');
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function verify_employee(Request $request, $id)
    {
        //:Linea original
        //$data = User::role(['vet', 'groomer', 'walker', 'boarder', 'trainer', 'day_taker', 'pet_sitter'])->findOrFail($id);
        $data = User::role(['vet', 'trainer', 'user'])->findOrFail($id);
        $current_time = Carbon::now();

        $data->update(['email_verified_at' => $current_time]);

        return response()->json(['status' => true, 'message' => __('messages.employee_verify')]);
    }

    public function review(Request $request)
    {
        $module_title = __('employee.review_title');
        $filter = $request->filter;
        return view('employee::backend.employees.review', compact('module_title', 'filter'));
    }

    public function review_data(Datatables $datatable, Request $request)
    {
        //consultas
        $employeeQuery = EmployeeRating::with('user')->select('id', 'user_id', 'employee_id', 'review_msg', 'rating', 'status', 'created_at', 'updated_at')
            ->addSelect(DB::raw("'employee' as module"));
        $bookQuery = BookRating::with('user', 'ebook')
            ->select('id', 'user_id', 'e_book_id', 'review_msg', 'rating', 'status', 'created_at', 'updated_at')
            ->addSelect(DB::raw("'books' as module"));
        $videoQuery = CoursePlatformVideoRating::with('user')
            ->select('id', 'user_id', 'course_platform_video_id', 'review_msg', 'rating', 'status', 'created_at', 'updated_at')
            ->addSelect(DB::raw("'courses' as module"));
        $blogQuery = BlogRating::with('user')
            ->select('id', 'user_id', 'blog_id', 'review_msg', 'rating', 'status', 'created_at', 'updated_at')
            ->addSelect(DB::raw("'blogs' as module"));
        //union
        $query = $employeeQuery->union($bookQuery)->union($videoQuery)->union($blogQuery);
        $filter = $request->filter;
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $query;
        } else {
            $query->where('employee_id', auth()->id());
        }
        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                $html = '<div class="form-check">';
                $html .= '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $data->id . '" name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheckRating(' . $data->id . ')">';
                $html .= '<input type="hidden" value="' . $data->module . '" name="module_name[' . $data->id . ']">'; // Cambia aquí para usar un array
                $html .= '</div>';
                return $html;
            })
            ->addColumn('image', function ($data) {
                if (isset($data->user->profile_image)) {
                    return '<img src=' . $data->user->profile_image . " class='avatar avatar-50 rounded-pill'>";
                } else {
                    return "<img src='https://dummyimage.com/600x300/cfcfcf/000000.png' class='avatar avatar-50 rounded-pill'>";
                }
            })
            ->addColumn('action', function ($data) {
                return view('employee::backend.employees.review_action_column', compact('data'));
            })
            ->filterColumn('employee_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('employee', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%' . $keyword . '%');
                        $q->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->editColumn('employee_id', function ($data) {
                if ($data->module === 'employee') {
                    $employee_id = isset($data->employee->full_name) ? $data->employee->full_name : '-';
                    if (isset($data->employee->profile_image)) {
                        return '<img src=' . $data->employee->profile_image . " class='avatar avatar-40 rounded-pill me-2'>" . ' ' . $employee_id;
                    } else {
                        $assetImage = asset('images/default/default.jpg');
                        return "<img style='max-width:40px;' src='" . $assetImage . "' class='avatar avatar-10 rounded-pill me-2'>" . ' ' . $employee_id;
                    }
                }
                if ($data->module === 'books') {
                    $bookRatoing = BookRating::find($data->id);
                    if ($bookRatoing) {
                        return $bookRatoing->ebook->title;
                    }
                    return __('rating.title_not_found');
                }
                if ($data->module === 'courses') {
                    $courseRating = CoursePlatformVideoRating::find($data->id);
                    if ($courseRating) {
                        return $courseRating->course_platform_video->title;
                    }
                    return __('rating.title_not_found');
                }

                if ($data->module === 'blogs') {
                    $blogRating = BlogRating::find($data->id);
                    if ($blogRating) {
                        return $blogRating->blog->name;
                    }
                    return __('rating.title_not_found');
                }
                // return $employee_id;
            })
            ->editColumn('review_msg', function ($data) {
                return '<div class="text-desc">' . $data->review_msg . '</div>';
            })
            ->editColumn('rating', function ($data) {
                $ratingNumber = $data->rating - floor($data->rating) > 0 ? number_format($data->rating, 1) : $data->rating;
                $iconsHtml = '';
                if (empty($ratingNumber)) {
                    $ratingNumber = 1;
                }
                $icon = '<i class="fas fa-star" style="color: #FFD43B;"></i>';
                for ($i = 0; $i < floor($ratingNumber); $i++) {
                    $iconsHtml .= $icon;
                }

                $container = "<div class='d-flex justify-content-center'>" . $iconsHtml . "</div>";

                return $container;
            })
            ->addColumn('module', function ($data) {
                if ($data->module === 'employee') {
                    $nameModule = __('rating.employee');
                }
                if ($data->module === 'books') {
                    $nameModule = __('rating.books');
                }
                if ($data->module === 'courses') {
                    $nameModule = __('rating.courses');
                }

                if ($data->module === 'blogs') {
                    $nameModule = __('rating.blogs');
                }

                return ucfirst($nameModule);
            })

            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%' . $keyword . '%');
                        $q->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                }
            })

            ->editColumn('user_id', function ($data) {
                $user_id = isset($data->user->full_name) ? $data->user->full_name : '-';
                if (isset($data->user->profile_image)) {
                    return '<img src=' . $data->user->profile_image . " class='avatar avatar-40 rounded-pill me-2'>" . $user_id;
                } else {
                    return "<img src='https://dummyimage.com/600x300/cfcfcf/000000.png' class='avatar avatar-40 rounded-pill me-2'>.$user_id";
                }

                // return $user_id;
            })
            // ->orderColumn('user_id', function ($query, $order) {
            //     $query->orderBy(new Expression('(SELECT first_name FROM users WHERE id = employee_rating.user_id LIMIT 1)'), $order);
            // }, 1)
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->created_at->diffForHumans();
                } else {
                    return $data->created_at->isoFormat('llll');
                }
            })
            ->addColumn('enabled', function ($data) {
                $text = __('rating.no');
                if ($data->status == 1) {
                    $text = __('rating.yes');
                }
                return $text;
            })
            ->orderColumns(['id'], '-:column $1');

        return $datatable->rawColumns(array_merge(['action', 'image', 'check', 'employee_id', 'user_id', 'review_msg', 'module', 'rating']))
            ->toJson();
    }

    public function bulk_action_review(Request $request)
    {
        $actionType = $request->action_type;
        $rowData = json_decode($request->input('rowData', '[]'), true);

        // Mapeo de módulos a modelos
        $modelMap = [
            'employee' => EmployeeRating::class,
            'books' => BookRating::class,
            'blogs' => BlogRating::class,
            'courses' => CoursePlatformVideoRating::class,
        ];

        // Agrupar IDs por módulo
        $idsByModule = [];
        foreach ($rowData as $data) {
            if (isset($modelMap[$data['module']])) {
                $idsByModule[$data['module']][] = $data['id'];
            }
        }

        // Mensajes para las acciones
        $messages = [
            'delete' => __('messages.bulk_review_delete'),
            'approve' => __('messages.bulk_review_update'),
        ];

        // Verificación del entorno demo
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        switch ($actionType) {
            case 'delete':
                foreach ($idsByModule as $module => $ids) {
                    if (!empty($ids)) {
                        $modelMap[$module]::whereIn('id', $ids)->delete();
                    }
                }
                break;

            case 'approve':
                foreach ($idsByModule as $module => $ids) {
                    if (!empty($ids)) {
                        $modelMap[$module]::whereIn('id', $ids)->update(['status' => 1]);
                    }
                }
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
        }

        return response()->json(['status' => true, 'message' => $messages[$actionType]]);
    }

    public function destroy_review(Request $request, $id)
    {
        $module_title = __('employee.review');

        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }
        if ($request->module_name === 'employee') {
            $data = EmployeeRating::findOrFail($id);
            $data->delete();
        }
        if ($request->module_name === 'books') {
            $data = BookRating::findOrFail($id);
            $data->delete();
        }
        if ($request->module_name === 'blogs') {
            $data = BlogRating::findOrFail($id);
            $data->delete();
        }
        if ($request->module_name === 'courses') {
            $data = CoursePlatformVideoRating::findOrFail($id);
            $data->delete();
        }


        $message = __('messages.delete_form', ['form' => __($module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function send_push_notification(Request $request)
    {

        $data = SendPushNotification($request->all());

        $decoded_data = json_decode($data, true);

        if (isset($decoded_data['errors'])) {

            return response()->json(['message' => $decoded_data['errors'][0], 'status' => false], 200);
        } else {

            return response()->json(['message' => __('messages.notification_send'), 'status' => true], 200);
        }
    }

    public function enableRating(Request $request)
    {
        // Validación de los datos de entrada
        $data = $request->validate([
            'module_name' => 'required|string',
            'module_id' => 'required|integer'
        ]);

        // Inicializar una variable para el modelo correspondiente
        $model = null;

        // Determinar el modelo basado en el nombre del módulo
        switch ($data['module_name']) {
            case 'employee':
                $model = EmployeeRating::class;
                break;
            case 'books':
                $model = BookRating::class;
                break;
            case 'courses':
                $model = CoursePlatformVideoRating::class;
                break;
            case 'blogs':
                $model = BlogRating::class;
                break;
            default:
                return response()->json(['status' => false, 'message' => 'Módulo no válido.'], 400);
        }

        try {
            // Buscar el registro correspondiente
            $rating = $model::find($data['module_id']);

            // Verificar si se encontró el registro
            if ($rating) {
                $rating->status = 1; // Habilitar la calificación
                $rating->save(); // Guardar cambios
                return response()->json(['status' => true, 'message' => __('messages.Qualification successfully enabled')]);
            } else {
                return response()->json(['status' => false, 'message' =>  __('messages.Record not found')], 404);
            }
        } catch (\Exception $e) {
            // Manejo de excepciones
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getTranslateUserType($userType)
    {
        switch ($userType) {
            case 'vet':
                return __('employee.vet');
            case 'trainer':
                return __('employee.trainer');
            case 'groomer':
                return __('employee.groomer');
            case 'walker':
                return __('employee.walker');
            case 'boarder':
                return __('employee.boarder');
            case 'day_taker':
                return __('employee.day_taker');
            case 'pet_sitter':
                return __('employee.pet_sitter');
            default:
                return __('employee.user');
        }
    }

}
