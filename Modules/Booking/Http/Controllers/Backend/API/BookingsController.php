<?php

namespace Modules\Booking\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Booking\Models\Booking;
use Modules\Booking\Trait\BookingTrait;
use Modules\Booking\Trait\PaymentTrait;
use Modules\Booking\Transformers\BookingDetailResource;
use Modules\Booking\Transformers\BookingBoardingResource;
use Modules\Booking\Transformers\BookingDaycareResource;
use Modules\Booking\Transformers\BookingWalkerResource;
use Modules\Booking\Transformers\BookingTrainerResource;
use Modules\Booking\Transformers\BookingResource;
use Modules\Booking\Transformers\BookingVeterinaryResource;
use Modules\Booking\Transformers\BookingGroomingResource;
use Modules\Employee\Transformers\EmployeeReviewResource;
use Modules\Booking\Transformers\BookingListResource;
use Modules\Constant\Models\Constant;
use Modules\Booking\Models\BookingBoardingMapping;
use Modules\Booking\Models\BookingDayCareMapping;
use Modules\Booking\Models\BookingTrainerMapping;
use Modules\Booking\Models\BookingWalkerMapping;
use Modules\Booking\Models\BookingVeterinaryMapping;
use Modules\Booking\Models\BookingGroomingMapping;
use Modules\Employee\Models\EmployeeRating;
use Modules\Booking\Models\BookingRequestMapping;
use Modules\Booking\Models\BookingTransaction;
use Modules\Commission\Models\CommissionEarning;
use DateTime;
use Illuminate\Support\Facades\Log;
use Modules\Service\Models\SystemService;

//use Modules\Booking\Trait\BookingTrait;

class BookingsController extends Controller
{
    use BookingTrait;
    use PaymentTrait;


    public function __construct()
    {
        // Page Title
        $this->module_title = 'Bookings';
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($data['booking_type'] == 'boarding') {
            $start_date_time = $data['dropoff_date_time'];
        } else if ($data['booking_type'] == 'veterinary' || $data['booking_type'] == 'grooming' || $data['booking_type'] == 'training' || $data['booking_type'] == 'walking') {
            $start_date_time = $data['date_time'];
        } else if ($data['booking_type'] == 'daycare') {
            $start_date_time = new DateTime($data['date'] . '' . $data['dropoff_time']);
        }


        $system_service_id = SystemService::where('slug', $data['booking_type'])->first()->id;

        $data['start_date_time'] = $start_date_time;
        $data['user_id'] = ! empty($request->user_id) ? $request->user_id : auth()->user()->id;
        $data['branch_id'] = get_pet_center_id();
        $data['service_amount'] = $request->price;
        $data['system_service_id'] = $system_service_id;

        // Ejecutar updateOrCreate solo si $data['id'] existe
        if (!empty($data['id'])) {
            $booking = Booking::updateOrCreate(['id' => $data['id']], $data);
        } else {
            if(isset($data['is_seeder'])){
                $booking = Booking::create([
                    'status' => $data['status'],
                    'start_date_time' => $data['start_date_time'],
                    'user_id' => $data['user_id'],
                    'event_id' => $data['event_id'],
                    'branch_id' => $data['branch_id'],
                    'employee_id' => $data['employee_id'],
                    'system_service_id' => $data['system_service_id'],
                    'pet_id' => $data['pet_id'],
                    'booking_type' => $data['booking_type'],
                    'total_amount' => $data['total_amount'],
                    'service_amount' => $data['service_amount'],
                ]);
            }else{
                $booking = Booking::create($data);
            }
        }

        switch ($request->booking_type) {
            case 'boarding':
                $boarding = [
                    'dropoff_date_time' => $request->dropoff_date_time,
                    'dropoff_address' => $request->dropoff_address,
                    'pickup_date_time' => $request->pickup_date_time,
                    'pickup_address' => $request->pickup_address,
                    'additional_facility' => $request->additional_facility,
                    'price' => $request->price,
                    'booking_id' => $booking->id
                ];
                BookingBoardingMapping::updateOrCreate(['booking_id' => $booking->id], $boarding);
                break;

            case 'walking':
                $walking = [
                    'date_time' => $request->date_time,
                    'duration' => $request->duration,
                    'address' => $request->address,
                    'price' => $request->price,
                    'booking_id' => $booking->id
                ];
                BookingWalkerMapping::updateOrCreate(['booking_id' => $booking->id], $walking);
                break;

            case 'training':
                $training = [
                    'date_time' => $request->date_time,
                    'training_id' => $request->training_id,
                    'price' => $request->price,
                    'duration' => $request->duration,
                    'booking_id' => $booking->id
                ];
                BookingTrainerMapping::updateOrCreate(['booking_id' => $booking->id], $training);
                break;

            case 'daycare':
                $day_care = [
                    'date' => $request->date,
                    'dropoff_time' => $request->dropoff_time,
                    'pickup_time' => $request->pickup_time,
                    'food' => $request->food,
                    'activity' => $request->activity,
                    'address' => $request->address,
                    'price' => $request->price,
                    'booking_id' => $booking->id
                ];
                BookingDayCareMapping::updateOrCreate(['booking_id' => $booking->id], $day_care);
                break;

            case 'grooming':
                $grooming = [
                    'booking_id' => $booking->id,
                    'date_time' => $request->date_time,
                    'duration' => $request->duration,
                    'service_id' => $request->service_id,
                    'price' => $request->price,
                    'service_name' => $request->service_name,
                ];
                BookingGroomingMapping::updateOrCreate(['booking_id' => $booking->id], $grooming);
                break;


            case 'veterinary':
                storeMediaFile($booking, $request->file('medical_report'), 'medical_report');

                $veterinary = [
                    'booking_id' => $booking->id,
                    'date_time' => $request->date_time,
                    'duration' => $request->duration,
                    'service_id' => $request->service_id,
                    'price' => $request->price,
                    'reason' => $request->reason,
                    'service_name' => $request->service_name,
                    'start_video_link' => $request->start_video_link,
                    'join_video_link' => $request->join_video_link,

                ];
                BookingVeterinaryMapping::updateOrCreate(['booking_id' => $booking->id], $veterinary);
                break;

            default:
                # code...
                break;
        }
        if (!is_null($booking->event_id)) {
            $booking_transaction_details = [
                'booking_id' => $booking->id,
                'total_amount' => $booking->total_amount,
                'tax_percentage' => 0,
                'payment_status' => 1,
            ];
            if(!isset($data['is_seeder'])){
                $booking_transaction_details['event_id'] = $booking->event_id;
            }

            $this->getpayment_method($booking_transaction_details);
        }

        // $this->updateBookingService($req, $booking->id);

        $message = __('messages.new_booking_updated');
        if ($booking->wasRecentlyCreated) {
            $message = __('messages.new_booking_created');

            try {
                $notification_data = [
                    'id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'user_name' => optional($booking->user)->first_name ?? default_user_name(),
                    'employee_id' => optional($booking->employee)->id,
                    'employee_name' => optional($booking->employee)->first_name,
                    'booking_date' => $booking->start_date_time->format('d/m/Y'),
                    'booking_time' => $booking->start_date_time->format('h:i'),
                    'booking_services_names' => $booking->systemservice->name,
                    'booking_services_image' => $booking->systemservice->feature_image,
                    'booking_date_and_time' => $booking->start_date_time->format('Y-m-d H:i'),
                    'latitude' => $request->has('latitude') ? $request->latitude : null,
                    'longitude' => $request->has('longitude') ? $request->longitude : null,
                ];
                $this->sendNotificationOnBookingUpdate('new_booking', $notification_data);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }
        return response()->json(
            [
                'message' => $message,
                'status' => true,
                'data' => $booking
            ],
            200
        );
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update($request->all());

        if ($request->has('services')) {

            $this->updateBookingService($request->services, $booking->id);
        }

        $message = __('messages.new_booking_updated');

        return response()->json(
            [
                'message' => $message,
                'data' => $booking,
                'status' => true
            ],
            200
        );
    }

    public function updateStatus(Request $request)
    {
        $id = $request->id;
        $data = Booking::with('services')->findOrFail($id);

        $data->status = $request->status;
        $data->update();

        $message = __('booking.status_update');

        return response()->json(
            [
                'message' => $message,
                'status' => true,
                'data' => $data
            ],
            200
        );
    }

    public function updateStatusConfirmed(Request $request)
    {
        $id = $request->id;
        $data = Booking::with('services')->findOrFail($id);

        $data->status = 'confirmed';
        $data->update();

        $message = __('booking.status_update');

        return response()->json(
            [
                'message' => $message,
                'status' => true,
                'data' => $data
            ],
            200
        );
    }

    public function bookingList(Request $request)
    {
        if ($request->has('user_id')) {
            $userId = $request->input('user_id', \Auth::user());
            $user = User::find($userId);
        } else {
            $user = \Auth::user();
        }
        if ($user->user_type == 'user') {
            $booking = Booking::where('user_id', $user->id);
        } else {

            $booking = Booking::where('employee_id', $user->id);
        }

        if ($request->has('booking_type') && isset($request->booking_type)) {
            $booking->where('booking_type', $request->booking_type);
        }

        if ($request->has('nearby_booking') && $request->nearby_booking == 1) {

            $booking = Booking::query();

            $bookingRequestQuery = BookingRequestMapping::query()
                ->where('walker_id', $user->id)
                ->where('status', 0);

            $bookingIds = $bookingRequestQuery->pluck('booking_id');

            $booking->whereIn('id', $bookingIds);
        }

        $booking = $booking->with(['boarding', 'training', 'daycare', 'walking', 'bookingTransaction', 'systemservice']);



        if ($request->has('system_service_name') && isset($request->system_service_name)) {
            $serviceNames = explode(',', $request->system_service_name);

            $booking->whereHas('systemservice', function ($query) use ($serviceNames) {
                $query->whereIn('name', $serviceNames);
            });
        }



        if ($request->has('status') && isset($request->status)) {

            $status = explode(',', $request->status);
            $booking->whereIn('status', $status);
        }
        $per_page = $request->input('per_page', 10);
        if ($request->has('per_page') && ! empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $booking->count();
            }
        }
        $orderBy = 'desc';
        if ($request->has('order_by') && ! empty($request->order_by)) {
            $orderBy = $request->order_by;
        }
        // Apply search conditions for booking ID, employee name, and service name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $booking->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")

                    ->orWhereHas('pet', function ($petQuery) use ($search) {
                        $petQuery->where('name', 'LIKE', "%$search%");
                    })

                    ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                        $employeeQuery->where(function ($nameQuery) use ($search) {
                            $nameQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                                ->orWhere('email', 'LIKE', "%$search");
                        });
                    })

                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where(function ($nameQuery) use ($search) {
                            $nameQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                                ->orWhere('email', 'LIKE', "%$search");
                        });
                    });
            });
        }

        $booking = $booking->orderBy('updated_at', $orderBy)->paginate($per_page);

        // if($request->booking_type === 'boarding'){
        //     $items = BookingBoardingResource::collection($booking);
        // }
        // if($request->booking_type === 'walking'){
        //     $items = BookingWalkerResource::collection($booking);
        // }
        // if($request->booking_type === 'daycare'){
        //     $items = BookingDaycareResource::collection($booking);
        // }
        // if($request->booking_type === 'training'){
        //     $items = BookingTrainerResource::collection($booking);
        // }
        // if($request->booking_type === 'veterinary'){
        //     $items = BookingVeterinaryResource::collection($booking);
        // }
        // if($request->booking_type === 'grooming'){
        //     $items = BookingGroomingResource::collection($booking);
        // }
        Log::info($booking);
        $items = BookingListResource::collection($booking);
        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('booking.booking_list'),
        ], 200);
    }

    public function bookingListTraining(Request $request)
    {
        if ($request->has('user_id')) {
            $userId = $request->input('user_id', \Auth::user());
            $user = User::find($userId);
        } else {
            $user = \Auth::user();
        }
        if ($user->user_type == 'user') {
            $booking = Booking::where('user_id', $user->id);
        } else {

            $booking = Booking::where('employee_id', $user->id);
        }

        $booking->where('booking_type', 'training');

        if ($request->has('nearby_booking') && $request->nearby_booking == 1) {

            $booking = Booking::query();

            $bookingRequestQuery = BookingRequestMapping::query()
                ->where('walker_id', $user->id)
                ->where('status', 0);

            $bookingIds = $bookingRequestQuery->pluck('booking_id');

            $booking->whereIn('id', $bookingIds);
        }

        $booking = $booking->with(['boarding', 'training', 'daycare', 'walking', 'bookingTransaction', 'systemservice']);



        if ($request->has('system_service_name') && isset($request->system_service_name)) {
            $serviceNames = explode(',', $request->system_service_name);

            $booking->whereHas('systemservice', function ($query) use ($serviceNames) {
                $query->whereIn('name', $serviceNames);
            });
        }



        if ($request->has('status') && isset($request->status)) {

            $status = explode(',', $request->status);
            $booking->whereIn('status', $status);
        }
        $per_page = $request->input('per_page', 10);
        if ($request->has('per_page') && ! empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $booking->count();
            }
        }
        $orderBy = 'desc';
        if ($request->has('order_by') && ! empty($request->order_by)) {
            $orderBy = $request->order_by;
        }
        // Apply search conditions for booking ID, employee name, and service name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $booking->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")

                    ->orWhereHas('pet', function ($petQuery) use ($search) {
                        $petQuery->where('name', 'LIKE', "%$search%");
                    })

                    ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                        $employeeQuery->where(function ($nameQuery) use ($search) {
                            $nameQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                                ->orWhere('email', 'LIKE', "%$search");
                        });
                    })

                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where(function ($nameQuery) use ($search) {
                            $nameQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                                ->orWhere('email', 'LIKE', "%$search");
                        });
                    });
            });
        }
    }

    public function bookingDetail(Request $request)
    {
        $id = $request->id;
        $booking_data = Booking::with(['boarding', 'grooming', 'veterinary', 'training', 'daycare', 'walking', 'bookingTransaction', 'systemservice'])->where('id', $id)->first();

        if ($booking_data == null) {
            $message = __('booking.booking_not_found');

            return response()->json([
                'status' => false,
                'message' => __('booking.booking_not_found'),
            ], 200);
        }
        if ($booking_data->booking_type === 'boarding') {
            $booking_detail = new BookingBoardingResource($booking_data);
        }
        if ($booking_data->booking_type === 'walking') {
            $booking_detail = new BookingWalkerResource($booking_data);
        }
        if ($booking_data->booking_type === 'daycare') {
            $booking_detail = new BookingDaycareResource($booking_data);
        }
        if ($booking_data->booking_type === 'training') {
            $booking_detail = new BookingTrainerResource($booking_data);
        }
        if ($booking_data->booking_type === 'veterinary') {
            $booking_detail = new BookingVeterinaryResource($booking_data);
        }
        if ($booking_data->booking_type === 'grooming') {
            $booking_detail = new BookingGroomingResource($booking_data);
        }
        $customer_review = null;
        if (!empty($booking_data->employee_id)) {
            $review = EmployeeRating::where('user_id', auth()->user()->id)
                ->where('employee_id', $booking_data->employee_id)->first();
            if (!empty($review)) {
                $customer_review = new EmployeeReviewResource($review);
            }
        }


        return response()->json([
            'status' => true,
            'data' => $booking_detail,
            'customer_review' => $customer_review,
            'message' => __('booking.booking_detail'),
        ], 200);
    }

    public function searchBookings(Request $request)
    {
        $keyword = $request->input('keyword');

        $bookings = Booking::where('note', 'like', "%{$keyword}%")
            ->with('branch', 'user')
            ->get();

        return response()->json([
            'status' => true,
            'data' => BookingResource::collection($bookings),
            'message' => __('booking.search_booking'),
        ], 200);
    }

    public function statusList()
    {
        $booking_status = Constant::getAllConstant()->where('type', 'BOOKING_STATUS');
        $checkout_sequence = $booking_status->where('name', 'check_in')->first()->sequence ?? 0;
        $bookingColors = Constant::getAllConstant()->where('type', 'BOOKING_STATUS_COLOR');
        $statusList = [];
        $finalstatusList = [];

        foreach ($booking_status as $key => $value) {
            if ($value->name !== 'cancelled') {
                $statusList = [
                    'status' => $value->name,
                    'title' => $value->value,
                    // 'color_hex' => $bookingColors->where('sub_type', $value->name)->first()->name,
                    'is_disabled' => $value->sequence >= $checkout_sequence,
                ];
                array_push($finalstatusList, $statusList);
                $nextStatus = $booking_status->where('sequence', $value->sequence + 1)->first();
                if ($nextStatus) {
                    $statusList[$value->name]['next_status'] = $nextStatus->name;
                }
            } else {
                $statusList = [
                    'status' => $value->name,
                    'title' => $value->value,
                    'color_hex' => $bookingColors->where('sub_type', $value->name)->first()->name,
                    'is_disabled' => true,
                ];
                array_push($finalstatusList, $statusList);
            }
        }

        return response()->json([
            'status' => true,
            'data' => $finalstatusList,
            'message' => __('booking.booking_status_list'),
        ], 200);
    }

    public function bookingUpdate(Request $request)
    {

        $data = $request->all();
        $id = $request->id;

        // if (! empty($request->date)) {
        //     $data['start_date_time'] = $request->date;

        // }

        $booking = Booking::findOrFail($id);

        $booking->fill($data);

        $booking->save();

        if ($booking->booking_type === 'boarding') {

            $boading_data = BookingBoardingMapping::where('booking_id', $id)->first();

            $boading_data->fill($data);

            $boading_data->save();
        }
        if ($booking->booking_type === 'walking') {

            $walking_data = BookingWalkerMapping::where('booking_id', $id)->first();

            $walking_data->fill($data);

            $walking_data->save();
        }
        if ($booking->booking_type === 'daycare') {

            $daycare_data = BookingDayCareMapping::where('booking_id', $id)->first();

            $daycare_data->fill($data);

            $daycare_data->save();
        }
        if ($booking->booking_type === 'training') {

            $training_data = BookingTrainerMapping::where('booking_id', $id)->first();

            $training_data->fill($data);

            $training_data->save();
        }
        if ($booking->booking_type === 'veterinary') {

            $veterinary_data = BookingVeterinaryMapping::where('booking_id', $id)->first();

            $veterinary_data->fill($data);

            $veterinary_data->save();
        }
        if ($booking->booking_type === 'grooming') {

            $grooming_data = BookingGroomingMapping::where('booking_id', $id)->first();

            $grooming_data->fill($data);

            $grooming_data->save();
        }

        if ($request->has('services') && $request->services != null) {

            $this->updateBookingService($request->services, $booking->id);
        }

        return response()->json([
            'status' => true,
            'message' => __('booking.booking_update'),
        ], 200);
    }

    public function accept_booking($id)
    {

        $employee_id = auth()->id();

        $booking = Booking::where('id', $id)->first();

        if ($booking->employee_id == null) {

            Booking::where('id', $id)->update(['employee_id' => $employee_id]);

            $payment = BookingTransaction::where('booking_id', $id)->first();

            if ($payment) {

                $earning_data = $this->commissionData($payment);

                $booking->commission()->save(new CommissionEarning($earning_data['commission_data']));
            }

            BookingRequestMapping::where('booking_id', $id)->update(['status' => 1]);

            $booking = Booking::where('id', $id)->with('user', 'systemservice', 'employee')->first();

            $notify_type = 'accept_booking_request';

            $notification_data = [

                'id' => $booking->id,
                'user_id' => $booking->user_id,
                'user_name' => $booking->user->first_name,
                'employee_id' => $booking->employee->id,
                'employee_name' => optional($booking->employee)->first_name,
                'booking_date' => DateTime::createFromFormat('d/m/Y', $booking->start_date_time),
                'booking_time' => DateTime::createFromFormat('h:i A', $booking->start_date_time),
                'booking_services_names' => $booking->systemservice->name,
                'booking_services_image' => $booking->systemservice->feature_image,
                'booking_date_and_time' => DateTime::createFromFormat('Y-m-d H:i', $booking->start_date_time),
                'latitude' =>  null,
                'longitude' => null,

            ];

            if (isset($notify_type)) {
                try {
                    $this->sendNotificationOnBookingUpdate($notify_type, $notification_data);
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            }


            $message = __('booking.booking_accepted');

            return response()->json(['message' => $message, 'status' => true], 200);
        }

        return response()->json(['status' => false, 'message' => __('booking.Booking already accepted')]);
    }

    public function getWhoCaredForMyPet(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|integer|exists:pets,id'
            ]);
            $users = User::with(['employeeBooking' => function ($query) {
                $query->where('status', 'completed');
            }])
                ->whereHas('employeeBooking', function ($q) use ($data) {
                    return $q->where('pet_id', $data['pet_id'])
                        ->whereIn('booking_type', ['veterinary', 'training']);
                })
                ->get();
            // Mapeo para extraer solo los campos necesarios
            $result = $users->map(function ($user) {
                return $user->employeeBooking->map(function ($booking) use ($user) {
                    return [
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'specialty' => $booking->booking_type,
                        'date' => DateTime::createFromFormat('Y-m-d H:i:s', $booking->start_date_time)->format('d/m/Y'),
                        'note' => $booking->note,
                        'booking_extra_info' => $booking->booking_extra_info
                    ];
                });
            })->flatten(1); // Aplanar para eliminar arrays anidados
            return response()->json([
                'success' => true,
                'message' => 'Query completed successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {

        try {
            $booking = Booking::findOrFail($id);
            Log::info($booking->booking_type);
            switch ($booking->booking_type) {
                case 'boarding':

                    BookingBoardingMapping::where('booking_id', $id)->delete();

                    break;

                case 'grooming':

                    BookingGroomingMapping::where('booking_id', $id)->delete();

                    break;

                case 'veterinary':

                    BookingVeterinaryMapping::where('booking_id', $id)->delete();

                    break;

                case 'training':

                    BookingTrainerMapping::where('booking_id', $id)->delete();

                    break;

                case 'walking':

                    BookingWalkerMapping::where('booking_id', $id)->delete();

                    break;

                case 'daycare':

                    BookingDayCareMapping::where('booking_id', $id)->delete();

                    break;


                default:
                    // Handle the case where the booking type is not recognized
                    // For example:
                    echo "Unknown booking type.";
                    break;
            }

            BookingTransaction::where('booking_id', $id)->delete();

            $booking->delete();

            // Responder con un mensaje de éxito
            return response()->json([
                'success' => true,
                'status' => true,
                'message' => 'Registro eliminado con éxito.'
            ], 200);
        } catch (\Exception $e) {
            // Manejo de excepciones
            return response()->json([
                'success' => false,
                'status' => false,
                'error' => 'No se pudo eliminar el registro: ' . $e->getMessage()
            ], 500);
        }
    }
}
