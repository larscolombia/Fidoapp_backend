<?php

namespace Modules\Earning\Http\Controllers\Backend;

use Currency;
use Carbon\Carbon;
use App\Models\Coin;
use App\Models\User;
use App\Authorizable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Tip\Models\TipEarning;
use App\Http\Controllers\Controller;
use Modules\Earning\Trait\EarningTrait;
use Modules\Earning\Models\EmployeeEarning;
use Modules\Commission\Models\CommissionEarning;

class EarningsController extends Controller
{
    // use Authorizable;
    use EarningTrait;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'earning.title';

        // module name
        $this->module_name = 'earnings';

        // directory path of the module
        $this->module_path = 'earning::backend';

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
    public function index()
    {
        $module_action = 'List';

        $module_title = 'earning.lbl_staff_earning';

        return view('earning::backend.earnings.index_datatable', compact('module_action', 'module_title'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        if (empty($term)) {
            return response()->json([]);
        }

        $query_data = Earning::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name . ' (Slug: ' . $row->slug . ')',
            ];
        }

        return response()->json($data);
    }

    public function update_status(Request $request, Earning $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(DataTables $datatable)
    {
        $module_name = $this->module_name;

        $query = User::select('users.*')
            ->withCount(['employeeBooking as totalBookings' => function ($q) {
                $q->whereHas('payment', function ($paymentQuery) {
                    $paymentQuery->where('payment_status', 1);
                })->groupBy('employee_id');
            }])

            ->with(['employeeBooking' => function ($q) {
                $q->select('employee_id')
                    ->selectRaw('COUNT(DISTINCT id) as totalBookings')
                    ->selectRaw('SUM(total_amount) as total_service_amount')
                    ->groupBy('employee_id')
                    ->whereHas('payment', function ($paymentQuery) {
                        $paymentQuery->where('payment_status', 1);
                    });
            }])
            ->with('commission_earning')
            ->with('tip_earning')
            ->with('employeeEarnings')
            ->whereHas('commission_earning', function ($q) {
                $q->where('commission_status', 'unpaid');
            })->orderBy('updated_at', 'desc');
            $coin = Coin::first();
        return $datatable->eloquent($query)
            // ->addColumn('action', function ($data) use ($module_name) {
            //     $commissionAmount = $data->commission_earning->where('commission_status', 'unpaid')->sum('commission_amount');
            //     $tipAmount = $data->tip_earning->where('tip_status', 'unpaid')->sum('tip_amount');
            //     $data['total_pay'] = $commissionAmount + $tipAmount;

            //     return view('earning::backend.earnings.action_column', compact('module_name', 'data'));
            // })

            ->editColumn('user_id', function ($data) {
                return view('earning::backend.earnings.user_id', compact('data'));
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
                }
            })
            ->orderColumn('user_id', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)

            // ->editColumn('image', function ($data) {
            //     return "<img src='" . $data->profile_image . "'class='avatar avatar-50 rounded-pill'>";
            // })
            // ->editColumn('first_name', function ($data) {
            //     return $data->full_name;
            // })
            ->editColumn('total_booking', function ($data) {

                if ($data->totalBookings > 0) {

                    return "<b><a href='" . route('backend.booking.datatable_view', ['commission_employee_id' => $data->id]) . "' data-assign-module='" . $data->id . "'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Employee Bookings'>" . $data->totalBookings . "</a> </b>";
                } else {

                    return "<b><span  data-assign-module='" . $data->id . "'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Employee Bookings'>0</span>";
                }
            })
            ->editColumn('total_service_amount', function ($data) use ($coin) {
                $totalServiceAmount = $data->employeeBooking->isEmpty() ? 0 : $data->employeeBooking->first()->total_service_amount;
                $totalServiceAmountFormat = str_replace('$','',$totalServiceAmount);
                return number_format($totalServiceAmountFormat,2).$coin->symbol;
            })
            ->editColumn('total_commission_earn', function ($data) use ($coin) {

                //return "<b><span  data-assign-module='".$data->id."' data-assign-target='#view_commission_list' data-assign-event='assign_commssions' class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Employee Commissions'> <i class='fa-regular fa-eye'></i></span>";

                if (!is_null($data->commission) && $data->commission->getCommission->commission_type == 'percentage') {
                    return $data->commission->getCommission->commission_value . '' . '%';
                } else {
                    if(!is_null($data->commission)){
                        return number_format($data->commission->getCommission->commission_value,2).$coin->symbol;
                    }
                    return number_format(0,2).$coin->symbol;
                }
            })
            ->editColumn('total_pay', function ($data) use ($coin) {
                return number_format($this->getUnpaidAmount($data)->total_pay,2).$coin->symbol;
            })
            ->orderColumn('total_services', function ($query, $order) {
                $query->orderBy('booking_servicesdata_count', $order);
            }, 'total_services')
            ->orderColumn('total_service_amount', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT SUM(service_price) FROM booking_services WHERE employee_id = users.id)'), $order);
            }, 1)
            // ->orderColumn('total_commission_earn', function ($query, $order) {
            //     $query->orderBy(new Expression('(SELECT SUM(commission_amount) FROM commission_earnings WHERE employee_id = users.id)'), $order);
            // }, 1)
            ->addIndexColumn()
            ->rawColumns(['image', 'user_id', 'total_commission_earn', 'total_booking'])
            ->toJson();
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

        $data = Earning::findOrFail($id);

        return view('earning::backend.earning.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */


    public function edit($id)
    {
        $query = User::where('id', $id)->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.mobile')
            ->withCount(['employeeBooking as totalBookings' => function ($q) {
                $q->groupBy('employee_id');
            }])
            ->with(['employeeBooking' => function ($q) {
                $q->select('employee_id')
                    ->selectRaw('COUNT(DISTINCT id) as totalBookings')
                    ->selectRaw('SUM(total_amount) as total_service_amount')
                    ->groupBy('employee_id');
            }])
            ->with('commission_earning')
            ->with('tip_earning')
            ->with('employeeEarnings')
            ->whereHas('commission_earning', function ($q) {
                $q->where('commission_status', 'unpaid');
            })->first();

        $unpaidAmount = $this->getUnpaidAmount($query);
        $data = [
            'id' => $query->id,
            'full_name' => $query->full_name,
            'email' => $query->email,
            'mobile' => $query->mobile,
            'profile_image' => $query->profile_image,
            'description' => '',
            'commission_earn' => Currency::format($unpaidAmount->total_commission_earn),
            'amount' => Currency::format($unpaidAmount->total_pay),
            'payment_method' => '',
        ];

        return response()->json(['data' => $data, 'status' => true]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {

        $data = $request->all();
        $query = User::with('commission_earning', 'tip_earning')->find($id);

        $unpaidAmount = $this->getUnpaidAmount($query);

        $earning_data = [
            'employee_id' => $id,
            'total_amount' => $unpaidAmount->total_pay,
            'payment_date' => Carbon::now(),
            'payment_type' => $data['payment_method'],
            'description' => $data['description'],
            'commission_amount' => $unpaidAmount->total_commission_earn,
        ];

        $earning_data = EmployeeEarning::create($earning_data);

        CommissionEarning::where('employee_id', $id)->where('commission_status', 'unpaid')->update(['commission_status' => 'paid']);
        TipEarning::where('employee_id', $id)->update(['tip_status' => 'paid']);

        $message = __('messages.payment_done');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function get_employee_commissions($id)
    {


        $data = User::where('id', $id)->with('commissions_data')->first();

        return response()->json(['data' => $data, 'status' => true]);
    }
}
