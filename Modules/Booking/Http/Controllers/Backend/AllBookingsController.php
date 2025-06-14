<?php

namespace Modules\Booking\Http\Controllers\Backend;

use DateTime;
use Carbon\Carbon;
use App\Models\Coin;
use App\Models\User;
use App\Authorizable;
use Modules\Tax\Models\Tax;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Booking\Models\Booking;
use Modules\Service\Models\Service;
use App\Http\Controllers\Controller;
use Modules\Constant\Models\Constant;
use Modules\Booking\Trait\BookingTrait;
use Modules\Booking\Trait\PaymentTrait;
use Illuminate\Database\Query\Expression;
use Modules\Service\Models\SystemService;
use Modules\Booking\Models\BookingService;
use Modules\Service\Models\ServiceFacility;
use  Modules\Service\Models\ServiceDuration;
use Modules\Booking\Models\BookingTransaction;
use Modules\Booking\Models\BookingWalkerMapping;
use Modules\Booking\Http\Requests\BookingRequest;
use Modules\Booking\Models\BookingDayCareMapping;
use Modules\Booking\Models\BookingTrainerMapping;
use Modules\Booking\Transformers\BookingResource;
use Modules\Booking\Models\BookingBoardingMapping;

use Modules\Booking\Models\BookingGroomingMapping;
use Modules\Booking\Models\BookingVeterinaryMapping;
// use Modules\CustomField\Models\CustomField;
// use Modules\CustomField\Models\CustomFieldGroup;

class AllBookingsController extends Controller
{
    // use Authorizable;
    use BookingTrait;
    use PaymentTrait;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'booking.title';

        // module name
        $this->module_name = 'bookings';

        // module icon
        $this->module_icon = 'fa-regular fa-sun';

        view()->share([
            'module_title' => $this->module_title,
            'module_name' => $this->module_name,
            'module_icon' => $this->module_icon,
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

        $statusList = $this->statusList();

        $booking = Booking::find(request()->booking_id);

        $date = $booking->start_date_time ?? date('Y-m-d');

        // $columns = CustomFieldGroup::columnJsonValues(new Booking());
        // $customefield = CustomField::exportCustomFields(new Booking());


        return view('booking::backend.allbooking.index', compact('module_action', 'statusList', 'date'));
    }

    public function statusList()
    {
        $booking_status = Constant::getAllConstant()->where('type', 'BOOKING_STATUS');
        $checkout_sequence = $booking_status->where('name', 'check_in')->first()->sequence ?? 0;
        $bookingColors = Constant::getAllConstant()->where('type', 'BOOKING_STATUS_COLOR');
        $statusList = [];

        foreach ($booking_status as $key => $value) {
            if ($value->name !== 'cancelled') {
                $statusList[$value->name] = [
                    'title' => $value->value,
                    'color_hex' => $bookingColors->where('sub_type', $value->name)->first()->name,
                    'is_disabled' => $value->sequence >= $checkout_sequence,
                ];
                $nextStatus = $booking_status->where('sequence', $value->sequence + 1)->first();
                if ($nextStatus) {
                    $statusList[$value->name]['next_status'] = $nextStatus->name;
                }
            } else {
                $statusList[$value->name] = [
                    'title' => $value->value,
                    'color_hex' => $bookingColors->where('sub_type', $value->name)->first()->name,
                    'is_disabled' => true,
                ];
            }
        }

        return $statusList;
    }

    /**
     * @return Response
     */
    public function index_list(Request $request)
    {
        $date = $request->date;

        $data = BookingService::with('booking', 'employee', 'service')
            ->whereHas('booking', function ($q) use ($date) {
                if (! empty($date)) {
                    $q->whereDate('start_date_time', $date);
                }
                $q->where('status', '!=', 'cancelled');
            })
            ->get();

        $updated_data = [];
        $statusList = $this->statusList();
        foreach ($data as $key => $value) {
            $duration = $value->duration_min;

            $startTime = $value->start_date_time;

            $endTime = Carbon::parse($startTime)->addMinutes($duration);

            $serviceName = $value->service->name ?? '';

            $customerName = $value->booking->user->full_name ?? 'Anonymous';

            $updated_data[$key] = [
                'id' => $value->booking_id,
                'start' => customDate($startTime, 'Y-m-d H:i'),
                'end' => customDate($endTime, 'Y-m-d H:i'),
                'resourceId' => $value->employee_id,
                'title' => $serviceName,
                'titleHTML' => view('booking::backend.allbooking.calender.event', compact('serviceName', 'customerName'))->render(),
                'color' => $statusList[$value->booking->status]['color_hex'],
            ];
            $startTime = $endTime;
        }
        $employees = User::bookingEmployeesList()->get();
        $resource = [];
        foreach ($employees as $employee) {
            $resource[] = [
                'id' => $employee->id,
                'title' => $employee->full_name,
                'titleHTML' => '<div class="d-flex gap-3 justify-content-center align-items-center py-3"><img src="'.$employee->profile_image.'" class="avatar avatar-40 rounded-pill" alt="employee" />'.$employee->full_name.'</div>',
            ];
        }

        return response()->json([
            'data' => $updated_data,
            'employees' => $resource,
        ]);
    }

    public function services_index_list(Request $request)
    {
        $employee_id = $request->employee_id;
        $branch_id = $request->branch_id;
        $data = Service::select('services.name as service_name', 'service_branches.*')
            ->with('employee')
            ->leftJoin('service_branches', 'service_branches.service_id', 'services.id')
            ->whereHas('category', function ($q) {
                $q->active();
            })
            ->where('branch_id', $branch_id);

        if (isset($employee_id)) {
            $data = $data->whereHas('employee', function ($q) use ($employee_id) {
                $q->where('employee_id', $employee_id);
            });
        }

        $data = $data->get();

        return response()->json($data);
    }

    public function datatable_view(Request $request)
    {
        $module_action = 'List';

        $filter = [
            'status' => $request->status,
        ];

        $commission_employee_id='';

        if($request->has('commission_employee_id') && $request->commission_employee_id !=''){

           $commission_employee_id= $request->commission_employee_id;
        };

        $booking_status = Constant::getAllConstant()->where('type', 'BOOKING_STATUS');

        return view('booking::backend.allbooking.index_datatable', compact('module_action', 'filter', 'booking_status','commission_employee_id'));
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;

        $query = Booking::query()->branch()->with(['user','boarding','pet','employee','payment']);

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
            if (isset($filter['booking_date'])) {
                try {
                    $startDate = explode(' to ', $filter['booking_date'])[0];
                    $endDate = explode(' to ', $filter['booking_date'])[1];
                    $query->whereBetween('start_date_time', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            }
            if (isset($filter['user_id'])) {
                $query->where('user_id', $filter['user_id']);
            }
            if (isset($filter['employee_id'])) {

                $query->where('employee_id', $filter['employee_id']);
            }

            if($request->has('commission_employee_id') && $request->commission_employee_id != '') {

                  $query->where('employee_id', $request->commission_employee_id)
                        ->where('status', 'completed')
                        ->WhereHas('payment', function ($paymentQuery) use ($request) {
                            $paymentQuery->where('payment_status', 1);
                      });
             }

        }

        $booking_status = Constant::getAllConstant()->where('type', 'BOOKING_STATUS');
        $booking_colors = Constant::getAllConstant()->where('type', 'BOOKING_STATUS_COLOR');

        $employee = User::where('user_type', 'boarder')->get();

        $payment_status = Constant::getAllConstant()->where('type', 'PAYMENT_STATUS')->where('status', '=', '1');
        $coin = Coin::first();
        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('booking::backend.allbooking.datatable.action_column', compact('module_name', 'data'));
            })
            ->editColumn('status', function ($data) use ($booking_status, $booking_colors) {
                return view('booking::backend.allbooking.datatable.select_column', compact('data', 'booking_status', 'booking_colors'));
            })
            ->editColumn('id', function ($data) {
                $url = route('backend.bookings.bookingShow', ['id' => $data->id]);
                return "<a href='$url' class='text-primary'>#".$data->id."</a>";
            })

            ->editColumn('payment_status', function ($data) use ($payment_status,$booking_colors) {
                return view('booking::backend.allbooking.datatable.select_payment_status', compact('data','payment_status','booking_colors'));
            })

            ->editColumn('user_id', function ($data) {
                return view('booking::backend.allbooking.datatable.user_id', compact('data'));
            })
            ->editColumn('employee_id', function ($data)  {
                // $employee = User::where('user_type', 'boarder')->get();
                $employeeData = User::where('status', 1);
                switch ($data->booking_type) {
                    case 'boarding':
                        $employeeData = $employeeData->where('user_type','boarder');
                        break;

                    case 'veterinary':
                        $employeeData = $employeeData->where('user_type','vet');
                        break;
                    case 'grooming':
                        $employeeData = $employeeData->where('user_type','groomer');
                        break;
                    case 'training':
                        $employeeData = $employeeData->where('user_type','trainer');
                        break;
                    case 'walking':
                        $employeeData = $employeeData->where('user_type','walker');
                        break;
                    case 'daycare':
                        $employeeData = $employeeData->where('user_type','day_taker');
                        break;

                    default:
                        # code...
                        break;
                }

                $employeeData  = $employeeData->get();
                if($data->employee_id){
                    if($data->employee !=null){

                        return $data->employee->first_name.' '.$data->employee->last_name;
                    }else{

                        return '-';
                    }

                }
                else{
                    return view('booking::backend.allbooking.datatable.select_employee', compact('data', 'employeeData'));
                }
            })
            ->editColumn('system_service_id', function ($data) {
                return $this->translateService($data->systemservice->name);
            })
            ->orderColumn('system_service_id', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT name FROM system_services WHERE id = bookings.system_service_id LIMIT 1)'), $order);
            }, 1)
            ->filterColumn('system_service_id', function ($query, $keyword) {
                // If a keyword is provided, filter the results based on the 'pettype' name
                if (!empty($keyword)) {
                    $query->whereHas('systemservice', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->editColumn('service_amount', function ($data) use ($coin) {
                return '<span class="text-primary">'.number_format($data->total_amount,2).$coin->symbol.'</span>';
            })
            ->orderColumn('service_amount', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT total_amount FROM booking_transactions WHERE booking_id = bookings.id)'), $order);
            }, 1)
            ->filterColumn('service_amount', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('payment', function ($q) use ($keyword) {
                        $q->where('total_amount', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->editColumn('pet_name', function ($data) {
                return '<span class="text-primary">'.($data->pet ? optional($data->pet)->name : '-').'</span>';
            })
            ->filterColumn('pet_name', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('pet', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->orderColumn('pet_name', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT name FROM pets WHERE id = bookings.pet_id LIMIT 1)'), $order);
            }, 1)

            ->editColumn('pettype_id', function ($data) {
                $value =optional(optional($data->pet)->pettype)->name;
                if(isset($data->pet)){
                    if(isset($data->pet->breed)){
                        $breed = $data->pet->breed->name;
                        $value = $data->pet->pettype->name . ' ('. $data->pet->breed->name .')';
                    }
                }
                return !empty($value) ? $value : '-';
            })
            ->filterColumn('pettype_id', function ($query, $keyword) {
                // If a keyword is provided, filter the results based on the 'pettype' name
                if (!empty($keyword)) {
                    $query->whereHas('pet.pettype', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('pettype_id', function ($query, $order) {
                $query->select('bookings.*')
                ->leftJoin('pets', 'pets.id', '=', 'bookings.pet_id')
                ->leftJoin('pets_type', 'pets.pettype_id', '=', 'pets_type.id')
                ->orderBy(new Expression('(SELECT name FROM pets_type WHERE pets_type.id = pets.pettype_id LIMIT 1)'), $order);
            }, 1)

            ->editColumn('updated_at', function ($data) {

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumn('employee_id', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT first_name FROM users WHERE id = bookings.employee_id LIMIT 1)'), $order);
            }, 1)
            ->filterColumn('employee_id', function ($query, $keyword) {
                if (! empty($keyword)) {
                        $query->whereHas('employee', function ($qn) use ($keyword) {
                            $qn->where('first_name', 'like', '%'.$keyword.'%');
                        });
                }
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->rawColumns(['check', 'action','pet_name', 'status', 'services', 'service_duration', 'pick_date_time','service_amount','start_date_time','id'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }
    private function translateService($serviceName) {
        switch ($serviceName) {
            case 'training':
                return 'Entrenador';
            case 'boarding':
                return 'Pensión';
            case 'dayCare':
                return 'Guardería';
            case 'veterinary':
                return 'Veterinario';
            case 'grooming':
                return 'Estética';
            case 'walking':
                return 'Paseador';
            default:
                return 'Servicio no disponible';
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(BookingRequest $request)
    {

        $data = $request->all();

        $branch_id=get_pet_center_id();

        $system_service=SystemService::where('slug', $data['type'])->first();

        if(!isset($data['addition_information'])){

            $data['addition_information']='';

        }

        $bookingData=[

            'start_date_time'=>Carbon::now(),
            'user_id'=>$data['user_id'],
            'employee_id'=>$data['employee_id'],
            'pet_id'=>$data['pet'],
            'booking_extra_info'=>$data['addition_information'],
            'booking_type'=>$data['type'],
            'branch_id'=> $branch_id,
            'system_service_id'=>$system_service->id,
            'status'=> 'confirmed',

        ];

        $booking = Booking::create($bookingData);

        switch ($booking['booking_type']) {
            case 'boarding':

                $service_amount=geTotalAmount($data['drop_off_date'],$data['pick_up_date'],$data['type']);

                $service_facility=ServiceFacility::where('slug',$data['facility'])->get();

                $booking_mapping=[

                    'booking_id'=>$booking['id'],
                    'dropoff_date_time'=>new DateTime($data['drop_off_date'].''.$data['drop_off_time']),
                    'dropoff_address'=>$data['drop_off_address'],
                    'pickup_date_time'=>new DateTime($data['pick_up_date'].''.$data['pick_up_time']),
                    // 'pickup_address'=>$data['pick_up_address'],
                    'price'=> $service_amount['service_amount'],
                 ];

                if(!empty($data['facility'])){
                    $booking_mapping['additional_facility'] =json_encode($service_facility);
                }
                BookingBoardingMapping::create($booking_mapping);

                break;

            case 'veterinary':
                $service_amount=geServiceAmount($data['type'], $data['service_id']);
                $service_data = Service::where('id', $data['service_id'])->first();
                $service_name = $service_data->name;
                storeMediaFile($booking, $request->file('medical_report'), 'medical_report');
                $booking_mapping=[

                    'booking_id'=>$booking['id'],
                    'date_time'=>new DateTime($data['date'].''.$data['time']),
                    'service_id'=>$data['service_id'],
                    'service_name'=> $service_name,
                    'price'=> $service_amount['service_amount'],
                    'reason'=>$data['reason'],
                    ];

                BookingVeterinaryMapping::create($booking_mapping);

                break;

            case 'grooming':

                $service_amount=geServiceAmount($data['type'], $data['service']);

                $service_data = getServiceDetails($data['service']);

                $service_name = $service_data->name;
                $duration = $service_data->duration_min;


                $booking_mapping=[

                    'booking_id'=>$booking['id'],
                    'date_time'=>new DateTime($data['date'].''.$data['time']),
                    'service_id'=>$data['service'],
                    'service_name'=> $service_name,
                    'price'=> $service_amount['service_amount'],
                    'duration'=> $duration,
                    ];

                BookingGroomingMapping::create($booking_mapping);
                break;


            case 'training':

                $service_amount=geServiceAmount($data['type'], $data['duration']);

                $booking_mapping=[

                    'booking_id'=>$booking['id'],
                    'date_time'=>new DateTime($data['date'].''.$data['time']),
                    'training_id'=>$data['training'],
                    'duration'=>$service_amount['duration'],
                    'price'=> $service_amount['service_amount'],
                    ];

                BookingTrainerMapping::create($booking_mapping);
                break;


            case 'walking':

                $service_amount=geServiceAmount($data['type'], $data['duration']);

                $booking_mapping=[

                    'booking_id'=>$booking['id'],
                    'date_time'=>new DateTime($data['date'].''.$data['time']),
                    'address'=>$data['address'],
                    'duration'=> $service_amount['duration'],
                    'price'=> $service_amount['service_amount'],
                    ];

                BookingWalkerMapping::create($booking_mapping);
                break;

                case 'daycare':

                    $service_amount=geDaycareAmount($data['type']);

                    $booking_mapping=[

                        'booking_id'=>$booking['id'],
                        'date'=>$data['date'],
                        'dropoff_time'=>$data['drop_off_time'],
                        'pickup_time'=>$data['pick_up_time'],
                        'address'=>$data['address'],
                        'food'=>$data['favorite_food'],
                        'activity'=>$data['favorite_activity'],
                        'price'=> $service_amount['service_amount'],

                     ];

                     BookingDayCareMapping::create($booking_mapping);

                    break;



            default:
                // Handle the case where the booking type is not recognized
                // For example:
                echo "Unknown booking type.";
                break;
        }

        $tax=Tax::active()->whereNull('module_type')->orWhere('module_type', 'services')->get();

        $booking_transaction_details=[

          'booking_id'=>$booking['id'],
          'total_amount'=> $service_amount['total_amount'],
          'tax_percentage'=>json_encode($tax),
          'payment_status'=>0

         ];

       BookingTransaction::create($booking_transaction_details);


        $message = __('messages.create_form', ['form' => __('booking.singular_title')]);

        try {
            $this->sendNotificationOnBookingUpdate('new_booking', $booking);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }


        return response()->json(['message' => $message, 'status' => true ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $booking = Booking::with(['services', 'user'])->find($id);

        if (is_null($booking)) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        $bookingTransaction = BookingTransaction::where('booking_id', $booking->id)->where('payment_status', 1)->first();

        $data = [
            'booking' => new BookingResource($booking),
            'services_total_amount' => $booking->services->sum('service_price'),
            'booking_transaction' => $bookingTransaction,
        ];

        return response()->json(['status' => true, 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {

        $booking= Booking::findOrFail($id);

         switch ($booking['booking_type']) {
            case 'boarding':

                $data = Booking::with('boarding', 'user','pet')->findOrFail($id);

                $data['facility']=json_decode($data->boarding->additional_facility);

                $dropoff_dateTimeObj = new DateTime($data->boarding->dropoff_date_time);

                $data['dropoff_date']= $dropoff_dateTimeObj->format('Y-m-d');
                $data['dropoff_time'] = $dropoff_dateTimeObj->format('H:i');

                 $pickup_dateTimeObj = new DateTime($data->boarding->pickup_date_time);

                 $data['pickup_date'] =  $pickup_dateTimeObj->format('Y-m-d');
                 $data['pickup_time']=  $pickup_dateTimeObj->format('H:i');
                 $data['amount']=  $data->boarding->price;

                 $data['dropoff_address']=$data->boarding->dropoff_address;
                 $data['pickup_address']=$data->boarding->pickup_address;


                break;
            case 'veterinary':

                $data = Booking::with('veterinary', 'user','pet')->findOrFail($id);

                $dateTimeObj = new DateTime($data->veterinary->date_time);

                $data['date']= $dateTimeObj->format('Y-m-d');
                $data['time'] = $dateTimeObj->format('H:i');
                $data['veterinary_type']=$data->veterinary->service->category->id;
                $data['service_id']=$data->veterinary->service_id;
                $data['reason']=$data->veterinary->reason;
                $data['medical_report'] = $data->medical_report;

                break;

                case 'grooming':

                    $data = Booking::with('grooming', 'user','pet')->findOrFail($id);

                    $dateTimeObj = new DateTime($data->grooming->date_time);

                    $data['date']= $dateTimeObj->format('Y-m-d');
                    $data['time'] = $dateTimeObj->format('H:i');

                    $data['service_id']=$data->grooming->service_id;
                    $data['price']=$data->grooming->price;


                 break;

                 case 'training':

                    $data = Booking::with('training', 'user','pet')->findOrFail($id);

                    $dateTimeObj = new DateTime($data->training->date_time);

                    $updatedData = [
                        'date' => $dateTimeObj->format('Y-m-d'),
                        'time' => $dateTimeObj->format('H:i'),
                        'training' => $data->training->training_id,
                        'price' => $data->training->price,
                        'duration' => $data->training->duration,
                        // Add other additional data if needed
                    ];

                    // Now, merge the original attributes of the $data object with the additional data array
                    $data = $data->toArray(); // Convert the original $data object to an array
                    $data = array_merge($data, $updatedData);

                 break;

                 case 'walking':

                    $data = Booking::with('walking', 'user','pet')->findOrFail($id);

                    $dateTimeObj = new DateTime($data->walking->date_time);
                    $data['date']= $dateTimeObj->format('Y-m-d');
                    $data['time'] = $dateTimeObj->format('H:i');
                    $data['address']= $data->walking->address;
                    $data['duration']=$data->walking->duration;
                    $data['price']=$data->walking->price;


                 break;

                 case 'daycare':

                    $data = Booking::with('daycare', 'user','pet')->findOrFail($id);

                    $dateTimeObj = new DateTime($data->daycare->date_time);
                    $data['date']= $dateTimeObj->format('Y-m-d');
                    $data['dropoff_time'] = $data->daycare->dropoff_time;
                    $data['pickup_time'] = $data->daycare->pickup_time;
                    $data['address']= $data->daycare->address;
                    $data['food']=$data->daycare->food;
                    $data['activity']=$data->daycare->activity;
                    $data['price']=$data->daycare->price;

                 break;

            default:
                // Handle the case where the booking type is not recognized
                // For example:
                echo "Unknown booking type.";
                break;
        }


        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(BookingRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $data = $request->all();

        $bookingData=[

            'user_id'=>$data['user_id'],
            'employee_id'=>$data['employee_id'],
            'pet_id'=>$data['pet'],
            'booking_extra_info'=>$data['addition_information'],
          ];

        $booking->update($bookingData);

        switch ($booking['booking_type']) {
            case 'boarding':

                $boarding_details=BookingBoardingMapping::where('booking_id',$booking['id'])->first();

                $total_amount=geTotalAmount($data['drop_off_date'],$data['pick_up_date'],$booking['booking_type']);

                $booking_mapping=[

                    'dropoff_date_time'=>new DateTime($data['drop_off_date'].''.$data['drop_off_time']),
                    'dropoff_address'=>$data['drop_off_address'],
                    'pickup_date_time'=>new DateTime($data['pick_up_date'].''.$data['pick_up_time']),
                    'pickup_address'=>$data['pick_up_address'],
                    'price'=> $total_amount,
                    'additional_facility'=>json_encode($data['facility'])
                 ];

                $boarding_details->update($booking_mapping);

                break;

            case 'grooming':

                $grooming_details=BookingGroomingMapping::where('booking_id',$booking['id'])->first();

                $grooming_total_amount=geServiceAmount($data['type'], $data['service']);

                $service_data = getServiceDetails($data['service']);

                $service_name = $service_data->name;
                $duration = $service_data->duration_min;

                $booking_mapping=[

                    'date_time'=>new DateTime($data['date'].''.$data['time']),
                    'service_id'=>$data['service'],
                    'service_name'=> $service_name,
                    'price'=> $grooming_total_amount,
                    'duration'=> $duration,
                    ];

                $grooming_details->update($booking_mapping);

              break;

              case 'veterinary':

                $veterinary_details=BookingVeterinaryMapping::where('booking_id',$booking['id'])->first();

                $vet_total_amount=geServiceAmount($data['type'], $data['service_id']);
                $service_data = Service::where('id', $data['service_id'])->first();
                $service_name = $service_data->name;
                storeMediaFile($booking, $request->file('medical_report'), 'medical_report');
                $booking_mapping=[

                    'booking_id'=>$booking['id'],
                    'date_time'=>new DateTime($data['date'].''.$data['time']),
                    'service_id'=>$data['service_id'],
                    'service_name'=> $service_name,
                    'price'=> $vet_total_amount,
                    'reason'=>$data['reason'],
                    ];

                $veterinary_details->update($booking_mapping);

                break;

              case 'training':

                $training_details=BookingTrainerMapping::where('booking_id',$booking['id'])->first();

                $training_total_amount=geServiceAmount($data['type'], $data['duration']);

                $booking_mapping=[

                    'booking_id'=>$booking['id'],
                    'date_time'=>new DateTime($data['date'].''.$data['time']),
                    'training_id'=>$data['training'],
                    'duration'=>$data['duration'],
                    'price'=> $training_total_amount,
                    ];

                 $training_details->update($booking_mapping);
                break;

                case 'walking':


                $walking_details=BookingWalkerMapping::where('booking_id',$booking['id'])->first();

                $walking_total_amount=geServiceAmount($data['type'], $data['duration']);

                $booking_mapping=[

                    'booking_id'=>$booking['id'],
                    'date_time'=>new DateTime($data['date'].''.$data['time']),
                    'address'=>$data['address'],
                    'price'=> $walking_total_amount,
                    'duration'=>$data['duration'],
                    ];

                $walking_details->update($booking_mapping);

               break;

               case 'daycare':

                $daycare_details=BookingDayCareMapping::where('booking_id',$booking['id'])->first();

                $daycare_total_amount=geDaycareAmount($data['type']);

                $booking_mapping=[

                    'booking_id'=>$booking['id'],
                    'date'=>$data['date'],
                    'dropoff_time'=>$data['drop_off_time'],
                    'pickup_time'=>$data['pick_up_time'],
                    'address'=>$data['address'],
                    'food'=>$data['favorite_food'],
                    'activity'=>$data['favorite_activity'],
                    'price'=> $daycare_total_amount,
                 ];

                $daycare_details->update($booking_mapping);

               break;


            default:
                // Handle the case where the booking type is not recognized
                // For example:
                echo "Unknown booking type.";
                break;
        }




        $message = __('booking.booking_service_update', ['form' => __('booking.singular_title')]);



        return response()->json(['message' => $message, 'status' => true, 'data' => $data], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        switch ($booking['booking_type']) {
            case 'boarding':

                BookingBoardingMapping::where('booking_id',$id)->delete();

                break;

            case 'grooming':

                BookingGroomingMapping::where('booking_id',$id)->delete();

              break;

            case 'veterinary':

                BookingVeterinaryMapping::where('booking_id',$id)->delete();

                break;

            case 'training':

                BookingTrainerMapping::where('booking_id',$id)->delete();

                break;

                case 'walking':

                BookingWalkerMapping::where('booking_id',$id)->delete();

               break;

               case 'daycare':

                BookingDayCareMapping::where('booking_id',$id)->delete();

                break;


            default:
                // Handle the case where the booking type is not recognized
                // For example:
                echo "Unknown booking type.";
                break;
        }

        BookingTransaction::where('booking_id',$id)->delete();

        $booking->delete();

        $message = __('messages.delete_form', ['form' => __('booking.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function updateStatus($id, Request $request)
    {
        $booking = Booking::findOrFail($id);
        $status = $request->status;

        if (isset($request->action_type) && $request->action_type == 'update-status') {
            $status = $request->value;
        }

        $booking->update(['status' => $status]);

        $notify_type = null;

        switch ($status) {
            case 'check_in':
                $notify_type = 'check_in_booking';
                break;
            case 'checkout':
                $notify_type = 'checkout_booking';
                break;
            case 'completed':
                $notify_type = 'complete_booking';
                break;
            case 'cancelled':
                $notify_type = 'cancel_booking';
                break;
        }

        $booking->latitude = null;
        $booking->longitude = null;

        if (isset($notify_type)) {
            try {
                $this->sendNotificationOnBookingUpdate($notify_type, $booking);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        $message = __('booking.status_update');

        return response()->json(['message' => $message, 'status' => true]);
    }

    public function updatePaymentStatus($id, Request $request)
    {

        if (isset($request->action_type) && $request->action_type == 'update-payment-status') {
            $status = $request->value;
        }



        BookingTransaction::where('booking_id',$id)->update(['payment_status' => $request->value]);


        // $notify_type = null;

        // switch ($status) {
        //     case 'check_in':
        //         $notify_type = 'check_in_booking';
        //         break;
        //     case 'checkout':
        //         $notify_type = 'checkout_booking';
        //         break;
        //     case 'completed':
        //         $notify_type = 'complete_booking';
        //         break;
        //     case 'cancelled':
        //         $notify_type = 'cancel_booking';
        //         break;
        // }

        // if (isset($notify_type)) {
        //     try {
        //         $this->sendNotificationOnBookingUpdate($notify_type, $booking);
        //     } catch (\Exception $e) {
        //         \Log::error($e->getMessage());
        //     }
        // }

        $message = __('booking.status_update');

        return response()->json(['message' => $message, 'status' => true]);
    }




    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = Booking::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_booking_update');
                break;

            case 'delete':
                Booking::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_booking_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('booking.booking_action_invalid')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function booking_slots(Request $request)
    {
        $day = date('l', strtotime($request->date));

        $branch_id = $request->branch_id;

        $slots = $this->getSlots($request->date, $day, $branch_id);

        return response()->json(['status' => true, 'data' => $slots]);
    }

    public function payment_create(Request $request)
    {
        $booking_id = $request->booking_id;
        $booking = Booking::find($booking_id);

        $booking_services = BookingService::where('booking_id', $booking_id)->get();
        $total_service_amount = $booking_services->sum('service_price');

        $currency = \Currency::getDefaultCurrency();
        $payment_methods = $booking->branch->payment_method;
        $constant = Constant::where('type', 'PAYMENT_METHODS')->whereIn('name', $payment_methods)->get();
        $payment_methods = $constant->map(function ($row) {
            return [
                'id' => $row->name,
                'text' => $row->value,
            ];
        })->toArray();
        $data = [
            'booking_amounts' => [
                'amount' => $total_service_amount,
                'currency' => $currency->currency_symbol,
            ],
            'PAYMENT_METHODS' => $payment_methods,
            'tax' =>Tax::active()->whereNull('module_type')->orWhere('module_type', 'services')->get(),
        ];

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function booking_payment(Request $request, Booking $booking_id)
    {

        $data = $request->all();

        $booking_id = $booking_id['id'];

        $responseData = $this->getpayment_method($data, $booking_id);

        return response()->json(['status' => true, 'data' => $responseData]);
    }

    public function booking_payment_update(Request $request, $booking_transaction_id)
    {
        $data = $request->all();

        $responseData = $this->getrazorpaypayments($data, $booking_transaction_id);

        if (isset($responseData['booking'])) {
            $queryData = Booking::find($responseData['booking']->id);
            $queryData->latitude = null;
            $queryData->longitude = null;

            try {
                $this->sendNotificationOnBookingUpdate('complete_booking', $queryData);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        return response()->json(['status' => true, 'data' => $responseData]);
    }

    public function checkout(Booking $booking_id, Request $request)
    {
        $this->updateBookingService($request->services, $booking_id->id);

        $queryData = Booking::with('services', 'user')->findOrFail($booking_id->id);

        return response()->json(['status' => true, 'data' => new BookingResource($queryData), 'message' => __('booking.booking_service_update')]);
    }

    public function stripe_payment(Request $request)
    {

        $data = $request->data;

        $checkout_session = $this->getstripepayments($data);

        if (isset($checkout_session['message'])) {

            return response()->json(['status' => false, 'data' => $checkout_session]);
        } else {

            BookingTransaction::where('id', $data['booking_transaction_id'])->update(['request_token' => $checkout_session['id']]);

            return response()->json(['status' => true, 'data_url' => $checkout_session->url, 'data' => $checkout_session]);
        }
    }

    public function payment_success($id)
    {

        $booking_transaction = BookingTransaction::where('id', $id)->first();

        $request_token = $booking_transaction['request_token'];

        $booking_id = $booking_transaction['booking_id'];

        $session_object = $this->getstripePaymnetId($request_token);

        if ($session_object['payment_intent'] !== '' && $session_object['payment_status'] == 'paid') {

            BookingTransaction::where('id', $id)->update(['external_transaction_id' => $session_object['payment_intent'], 'payment_status' => 1]);

            Booking::where('id', $booking_id)->update(['status' => 'completed']);

            $queryData = Booking::where('id', $booking_id)->first();

            $queryData->latitude = null;
            $queryData->longitude = null;
            try {
                $this->sendNotificationOnBookingUpdate('complete_booking', $queryData);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        return redirect()->route('backend.bookings.index');
    }


    public function updateEmployee($id, Request $request)
    {
        $booking = Booking::with('user')->findOrFail($id);
        $employee_id = $request->value;

        $booking->update(['employee_id' => $employee_id]);

        $message = __('booking.employee_assign');

        return response()->json(['message' => $message, 'status' => true]);
    }


}
