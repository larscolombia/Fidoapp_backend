<?php

namespace App\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchGallery;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Service\Models\Service;
use Modules\Service\Models\SystemService;
use App\Models\Setting;
use Modules\Service\Transformers\ServiceResource;
use Modules\Service\Transformers\SystemServiceResource;
use Modules\Blog\Transformers\BlogResource;
use Modules\Event\Transformers\EventResource;
use Modules\Slider\Models\Slider;
use Modules\Blog\Models\Blog;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingRequestMapping;
use Modules\Event\Models\Event;
use Modules\Tax\Models\Tax;
use Modules\Constant\Models\Constant;
use Modules\Slider\Transformers\SliderResource;
use Modules\Booking\Transformers\BookingDetailResource;
use Modules\Booking\Transformers\BookingBoardingResource;
use Modules\Booking\Transformers\BookingDaycareResource;
use Modules\Booking\Transformers\BookingWalkerResource;
use Modules\Booking\Transformers\BookingTrainerResource;
use Modules\Booking\Transformers\BookingListResource;
use Modules\Booking\Transformers\BookingVeterinaryResource;
use Modules\Tax\Transformers\TaxResource;
use Modules\Product\Models\Product;
use Modules\Product\Transformers\ProductResource;
use Modules\Commission\Models\CommissionEarning;
use Modules\Employee\Models\EmployeeRating;
use Modules\Employee\Transformers\EmployeeReviewResource;
use Carbon\Carbon;
use Auth;
class DashboardController extends Controller
{
    public function dashboardDetail(Request $request)
    {
        $perPage = $request->input('per_page', 6);
        $branchId = $request->input('branch_id'); // Assuming the branch ID is passed in the request

      

        // if (! $branch) {
        //     return response()->json(['status' => false, 'message' => __('branch.branch_notfound')], 404);
        // }
        $currDate = Carbon::now();

        $system_service = SystemService::where('status',1)->with(['media'])->paginate($perPage);
        $system_service = SystemServiceResource::collection($system_service);
        $petsitter = User::where('status',1)->where('user_type','pet_sitter')->take(5)->get();
        $slider = SliderResource::collection(Slider::where('status', 1)->paginate($perPage));
        $pet_daycare_amount = Setting::where('name', 'pet_daycare_amount')->get();
        $pet_boarding_amount = Setting::where('name', 'pet_boarding_amount')->get();

        $blog = BlogResource::collection(Blog::where('status',1)->orderBy('created_at', 'DESC')->paginate($perPage));
        // $event = EventResource::collection(Event::where('status',1)->paginate($perPage));

        $upcommingEvent = Event::where('status',1)->where('date', '>=', $currDate)->orderBy('date', 'asc')->paginate($perPage);
        $event = EventResource::collection($upcommingEvent);

        $upcomming_booking = null;
        $get_upcomming_booking = null;
        if(!empty($request->user_id)){
            $get_upcomming_booking = Booking::with(['boarding','training','daycare','veterinary','walking','bookingTransaction','systemservice'])
            ->where('user_id',$request->user_id)->where('status', 'pending')->orderBy('id', 'DESC')->first();
        }
       
        $tax = TaxResource::collection(Tax::active()->whereNull('module_type')->orWhere('module_type', 'services')->get());
        if($get_upcomming_booking !== null){

            if($get_upcomming_booking->booking_type === 'boarding'){

                $upcomming_booking = new BookingBoardingResource($get_upcomming_booking);
            }
            if($get_upcomming_booking->booking_type === 'walking'){
                $upcomming_booking = new BookingWalkerResource($get_upcomming_booking);
            }
            if($get_upcomming_booking->booking_type === 'daycare'){
                $upcomming_booking = new BookingDaycareResource($get_upcomming_booking);
            }
            if($get_upcomming_booking->booking_type === 'training'){
                $upcomming_booking = new BookingTrainerResource($get_upcomming_booking);
            }

            if($get_upcomming_booking->booking_type === 'veterinary'){
                $upcomming_booking = new BookingVeterinaryResource($get_upcomming_booking);
            }

            
        }
        $weight_unit = Constant::where('type','PET_WEIGHT_UNIT')->get();
        $height_unit = Constant::where('type','PET_HEIGHT_UNIT')->get();

       $featuresProduct=Product::where('status',1)->where('is_featured',1)->with('media','categories','brand','unit','product_variations','product_review')->take(6)->get();

        

        $responseData = [
            'system_service' =>$system_service,
            'pet_sitter' =>$petsitter,
            'slider' => $slider,
            'pet_daycare_amount' => $pet_daycare_amount,
            'pet_boarding_amount' => $pet_boarding_amount,
            'blog' => $blog,
            'event' => $event,
            'featuresProduct' => ProductResource::collection($featuresProduct),
            'upcomming_booking' => $upcomming_booking,
            'tax' => $tax,
            'weight_unit' => $weight_unit,
            'height_unit' => $height_unit
        ];

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.dashboard_detail'),
        ], 200);
    }


    public function employeeDashboard(Request $request){

        $employee_id=Auth::id();

        $total_booking = Booking::where('employee_id',$employee_id)->count();

        $employee_earning=CommissionEarning::where('employee_id',$employee_id)->where('commission_status','paid')->sum('commission_amount');

        $query = Booking::with(['boarding', 'training', 'daycare', 'walking', 'veterinary', 'bookingTransaction', 'systemservice'])
        ->where('employee_id', $employee_id)
        ->where('start_date_time', '>', now())
        ->whereIn('status', ['pending', 'confirmed'])
        ->orderBy('start_date_time', 'asc')
        ->take(3);

         $get_upcomming_booking = $query->get();

         $bookingRequestQuery = BookingRequestMapping::query()
                ->where('walker_id', auth()->id())
                ->where('status', 0);

          $bookingIds = $bookingRequestQuery->pluck('booking_id');

         $get_booking_request = Booking::with(['walking'])
         ->whereIn('id',$bookingIds )
         ->orderBy('start_date_time', 'desc')
         ->take(3)->get();
 

         $upcomming_booking = BookingListResource::collection($get_upcomming_booking);
         $booking_request = BookingListResource::collection($get_booking_request);

         $reviews = EmployeeRating::where('employee_id', $employee_id)
         ->orderBy('rating', 'desc')
         ->take(5)
         ->get(); 
     
        $review = EmployeeReviewResource::collection($reviews);


        $responseData = [

          'total_booking' =>$total_booking,
          'employee_earning'=>$employee_earning,
          'upcomming_booking' => $upcomming_booking,
          'booking_request'=>$booking_request,
          'review'=>$review,

        ];



        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.dashboard_detail'),
        ], 200);
      
    
       



    }

    public function searchList(Request $request)
    {

        $query = $request->input('query');
        $results = [];

        // Search in Branches
        $branches = Branch::where('name', 'like', "%{$query}%")->get();
        $results['branches'] = $branches;

        // Search in Employees // Need To Add Role Base
        $employees = User::where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%");
        })->get();
        $results['employees'] = $employees;

        // Search in Categories
        $categories = Category::where('name', 'like', "%{$query}%")->get();
        $results['categories'] = $categories;

        $subcategories = Category::where('name', 'like', "%{$query}%")
            ->orWhere('parent_id', 'like', "%{$query}%")
            ->get();
        $results['subcategory'] = $subcategories;

        // Search in Bookings
        $bookings = Booking::where('note', 'like', "%{$query}%")->get();
        $results['bookings'] = $bookings;

        // Search in Services
        $services = Service::where('name', 'like', "%{$query}%")->get();
        $results['services'] = $services;

        return response()->json($results);
    }
}
