<?php

namespace App\Http\Controllers\Backend;

use DB;
use DateTimeZone;
use Carbon\Carbon;
use App\Models\Coin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use Modules\Booking\Models\BookingService;
use Modules\Employee\Models\EmployeeRating;
use Modules\Product\Models\ProductCategory;
use Modules\Booking\Models\BookingTransaction;
use Modules\Booking\Models\BookingGroomingMapping;
use Modules\Booking\Models\BookingVeterinaryMapping;

class BackendController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $recent_booking = Booking::with('user')->orderBy('id', 'desc')->take(6)->get();
        // $complete_service=Booking::where('status','completed')->count();

        $query = Booking::with(['user', 'pet', 'employee', 'payment'])->get();

        $completeBookingsCount = $query->where('status', 'completed')->count();
        $pendingBookingsCount = $query->where('status', 'pending')->count();

        $revenue_data = getRevenueData();

        $popular_employee = User::withCount(['employeeBooking' => function ($query) {
            $query->where('status', 'completed');
        }])
            ->where('user_type', '!=', 'user')
            ->orderByDesc('employee_booking_count')
            ->take(4)
            ->get();

        $popular_customers = User::withCount(['booking' => function ($query) {
            $query->where('status', 'completed');
        }])
            ->where('user_type', 'user')
            ->orderByDesc('booking_count')
            ->take(4)
            ->get();
        $popular_doctors = User::where('user_type', 'vet')->withCount(['rating as average_rating' => function ($query) {
            $query->select(DB::raw('coalesce(avg(rating),0)'));
        }])->orderByDesc('average_rating')->take(4)->get();

        $reviews = EmployeeRating::with('user')->get();
        $totalCustomer = $reviews->pluck('user.id')->unique()->count();
        $averageRating = number_format($reviews->avg('rating'), 1, '.', '');


        $topServices = Booking::where('booking_type', 'veterinary')
            ->with(['veterinary'])->get();

        Log::debug($topServices);

        $topproduct = ProductCategory::orderByDesc('total_sale_count')->limit(6)->get();
        $totalsale = ProductCategory::sum('total_sale_count');

        $veterinarybooking = BookingVeterinaryMapping::with('service')->select(DB::raw("(COUNT(*)) as count"), 'service_id')
            ->groupBy('service_id');

        $groomingbooking = BookingGroomingMapping::with('service')->select(DB::raw("(COUNT(*)) as count"), 'service_id')
            ->groupBy('service_id');
        $vetgroom = $veterinarybooking->union($groomingbooking);
        $totalservice = $vetgroom->count();
        $topservice = $vetgroom->orderByDesc('count')->take(4)->get();

        $coin= Coin::first();
        $data = [
            'recent_booking' => $recent_booking,
            'completeBookingsCount' => $completeBookingsCount,
            'pendingBookingsCount' => $pendingBookingsCount,
            'total_amount' => $revenue_data['total_amount'],
            'total_commission' => $revenue_data['total_commission'],
            'profit' => $revenue_data['admin_earnings'],
            'popular_employee' => $popular_employee,
            'reviews' => $reviews,
            'totalCustomer' => $totalCustomer,
            'averageRating' => $averageRating,
            'popular_customers' => $popular_customers,
            'popular_doctors' => $popular_doctors,
            'top_product' =>  $topproduct,
            'total_sale_product' => ($totalsale == 0) ? 1 : $totalsale,
            'topservice' => $topservice,
            'totalservice' => ($totalservice == 0) ? 1 : $totalservice,
            'symbol'=>$coin->symbol,
            // 'monthlyData'=>$monthlyData,

        ];

        return view('backend.index', compact('data'));
    }

    public function getRevenuechartData($type)
    {

        $currentMonth = Carbon::now()->month;


        if ($type == 'year') {

            $monthlyTotals = DB::table('bookings')
                ->select(DB::raw('YEAR(start_date_time) as year, MONTH(start_date_time) as month, SUM(total_amount) as total_amount'))
                ->where('status', 'completed')
                ->groupBy(DB::raw('YEAR(start_date_time), MONTH(start_date_time)'))
                ->orderBy(DB::raw('YEAR(start_date_time), MONTH(start_date_time)'))
                ->get();

            $chartData = [];

            for ($month = 1; $month <= 12; $month++) {
                $found = false;
                foreach ($monthlyTotals as $total) {
                    if ((int)$total->month === $month) {
                        $chartData[] = (float)$total->total_amount;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $chartData[] = 0;
                }
            };

            $category = [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ];
        } else if ($type == 'month') {

            $firstWeek = Carbon::now()->startOfMonth()->week;

            $monthlyWeekTotals = DB::table('bookings')
                ->select(DB::raw('YEAR(start_date_time) as year, MONTH(start_date_time) as month, WEEK(start_date_time) as week, COALESCE(SUM(total_amount), 0) as total_amount'))
                ->where('status', 'completed')
                ->where(DB::raw('YEAR(start_date_time)'), '=', Carbon::now()->year)
                ->where(DB::raw('MONTH(start_date_time)'), '=', $currentMonth)
                ->groupBy(DB::raw('YEAR(start_date_time), MONTH(start_date_time), WEEK(start_date_time)'))
                ->orderBy(DB::raw('YEAR(start_date_time), MONTH(start_date_time), WEEK(start_date_time)'))
                ->get();

            $chartData = [];


            for ($i = $firstWeek; $i <= $firstWeek + 4; $i++) {
                $found = false;

                foreach ($monthlyWeekTotals as $total) {

                    if ((int)$total->month === $currentMonth && (int)$total->week === $i) {
                        $chartData[] = (float)$total->total_amount;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $chartData[] = 0;
                }
            }

            $category = ["Semana 1", "Semana 2", "Semana 3", "Semana 4", 'Semana 5'];
        } else {

            $currentWeekStartDate = Carbon::now()->startOfWeek();
            $lastDayOfWeek = Carbon::now()->endOfWeek();

            $weeklyDayTotals = DB::table('bookings')
                ->select(DB::raw('DAY(start_date_time) as day, COALESCE(SUM(total_amount), 0) as total_amount'))
                ->where('status', 'completed')
                ->where(DB::raw('YEAR(start_date_time)'), '=', Carbon::now()->year)
                ->where(DB::raw('MONTH(start_date_time)'), '=', $currentMonth)
                ->whereBetween('start_date_time', [$currentWeekStartDate, $currentWeekStartDate->copy()->addDays(6)])
                ->groupBy(DB::raw('DAY(start_date_time)'))
                ->orderBy(DB::raw('DAY(start_date_time)'))
                ->get();

            $chartData = [];

            for ($day =  $currentWeekStartDate; $day <= $lastDayOfWeek; $day->addDay()) {
                $found = false;

                foreach ($weeklyDayTotals as $total) {
                    if ((int)$total->day === $day->day) {
                        $chartData[] = (float)$total->total_amount;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $chartData[] = 0;
                }
            };

            $category = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        }

        $data = [

            'chartData' => $chartData,
            'category' => $category

        ];

        return response()->json(['data' => $data, 'status' => true]);
    }

    public function getProfitchartData($type)
    {

        $currentMonth = Carbon::now()->month;

        if ($type == 'year') {

            $monthlyTotals = DB::table('commission_earnings')
                ->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(commission_amount) as commission_amount'))
                ->where('commission_status', '!=', 'pending')
                ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                ->get();

            $chartData = [];

            for ($month = 1; $month <= 12; $month++) {
                $found = false;
                foreach ($monthlyTotals as $total) {
                    if ((int)$total->month === $month) {
                        $chartData[] = (float)$total->commission_amount;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $chartData[] = 0;
                }
            };

            $category = [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ];
        } else if ($type == 'month') {

            $firstWeek = Carbon::now()->startOfMonth()->week;

            $monthlyWeekTotals = DB::table('commission_earnings')
                ->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, WEEK(created_at) as week, COALESCE(SUM(commission_amount), 0) as commission_amount'))
                ->where('commission_status', '!=', 'pending')
                ->where(DB::raw('YEAR(created_at)'), '=', Carbon::now()->year)
                ->where(DB::raw('MONTH(created_at)'), '=', $currentMonth)
                ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at), WEEK(created_at)'))
                ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at), WEEK(created_at)'))
                ->get();

            $chartData = [];


            for ($i = $firstWeek; $i <= $firstWeek + 4; $i++) {
                $found = false;

                foreach ($monthlyWeekTotals as $total) {

                    if ((int)$total->month === $currentMonth && (int)$total->week === $i) {
                        $chartData[] = (float)$total->commission_amount;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $chartData[] = 0;
                }
            }

            $category = ["Semana 1", "Semana 2", "Semana 3", "Semana 4", "Semana 5"];
        } else {

            $currentWeekStartDate = Carbon::now()->startOfWeek();
            $lastDayOfWeek = Carbon::now()->endOfWeek();

            $weeklyDayTotals = DB::table('commission_earnings')
                ->select(DB::raw('DAY(created_at) as day, COALESCE(SUM(commission_amount), 0) as commission_amount'))
                ->where('commission_status', '!=', 'pending')
                ->where(DB::raw('YEAR(created_at)'), '=', Carbon::now()->year)
                ->where(DB::raw('MONTH(created_at)'), '=', $currentMonth)
                ->whereBetween('created_at', [$currentWeekStartDate, $currentWeekStartDate->copy()->addDays(6)])
                ->groupBy(DB::raw('DAY(created_at)'))
                ->orderBy(DB::raw('DAY(created_at)'))
                ->get();

            $chartData = [];

            for ($day =  $currentWeekStartDate; $day <= $lastDayOfWeek; $day->addDay()) {
                $found = false;

                foreach ($weeklyDayTotals as $total) {
                    if ((int)$total->day === $day->day) {
                        $chartData[] = (float)$total->commission_amount;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $chartData[] = 0;
                }
            };

            $category = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        }

        $data = [

            'chartData' => $chartData,
            'category' => $category

        ];

        return response()->json(['data' => $data, 'status' => true]);
    }

    public function getBookingchartData($type)
    {

        $currentMonth = Carbon::now()->month;

        if ($type == 'year') {

            $monthlyTotals = DB::table('bookings')
                ->select(DB::raw('YEAR(start_date_time) as year, MONTH(start_date_time) as month, COUNT(*) as total_bookings'))
                ->groupBy(DB::raw('YEAR(start_date_time), MONTH(start_date_time)'))
                ->orderBy(DB::raw('YEAR(start_date_time), MONTH(start_date_time)'))
                ->get();

            $chartData = [];

            for ($month = 1; $month <= 12; $month++) {
                $found = false;
                foreach ($monthlyTotals as $total) {
                    if ((int)$total->month === $month) {
                        $chartData[] = (float)$total->total_bookings;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $chartData[] = 0;
                }
            };

            $category = [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ];
        } else if ($type == 'month') {

            $firstWeek = Carbon::now()->startOfMonth()->week;

            $monthlyWeekTotals = DB::table('bookings')
                ->select(DB::raw('YEAR(start_date_time) as year, MONTH(start_date_time) as month, WEEK(start_date_time) as week, COUNT(*) as total_bookings'))
                ->where(DB::raw('YEAR(start_date_time)'), '=', Carbon::now()->year)
                ->where(DB::raw('MONTH(start_date_time)'), '=', $currentMonth)
                ->groupBy(DB::raw('YEAR(start_date_time), MONTH(start_date_time), WEEK(start_date_time)'))
                ->orderBy(DB::raw('YEAR(start_date_time), MONTH(start_date_time), WEEK(start_date_time)'))
                ->get();

            $chartData = [];


            for ($i = $firstWeek; $i <= $firstWeek + 4; $i++) {
                $found = false;

                foreach ($monthlyWeekTotals as $total) {

                    if ((int)$total->month === $currentMonth && (int)$total->week === $i) {
                        $chartData[] = (float)$total->total_bookings;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $chartData[] = 0;
                }
            }

            $category = ["Semana 1", "Semana 2", "Semana 3", "Semana 4", 'Semana5'];
        } else {

            $currentWeekStartDate = Carbon::now()->startOfWeek();
            $lastDayOfWeek = Carbon::now()->endOfWeek();

            $weeklyDayTotals = DB::table('bookings')
                ->select(DB::raw('DAYOFWEEK(start_date_time) - 1 as day, COUNT(*) as total_bookings')) // Subtract 1 to align with Carbon
                ->where(DB::raw('YEAR(start_date_time)'), '=', Carbon::now()->year)
                ->where(DB::raw('MONTH(start_date_time)'), '=', $currentMonth)
                ->whereBetween('start_date_time', [$currentWeekStartDate, $currentWeekStartDate->copy()->addDays(6)])
                ->groupBy(DB::raw('DAYOFWEEK(start_date_time) - 1')) // Subtract 1 to align with Carbon
                ->orderBy(DB::raw('DAYOFWEEK(start_date_time) - 1')) // Subtract 1 to align with Carbon
                ->get();

            $chartData = [];

            for ($day = $currentWeekStartDate->copy(); $day <= $lastDayOfWeek; $day->addDay()) { // Use a copy of the start date
                $found = false;

                foreach ($weeklyDayTotals as $total) {
                    if ((int)$total->day === $day->dayOfWeek) {
                        $chartData[] = (float)$total->total_bookings;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $chartData[] = 0;
                }
            };

            $category = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        }


        $data = [

            'chartData' => $chartData,
            'category' => $category

        ];


        return response()->json(['data' => $data, 'status' => true]);
    }


    public function getStatusBookingChartData($type)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $yearlyTotalsQuery = DB::table('bookings')
            ->select('status')
            ->addSelect(DB::raw('
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_bookings,
                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_bookings,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_bookings,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_bookings,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_bookings'))
            ->whereYear('start_date_time', $currentYear);

        if ($type == 'month') {
            $yearlyTotalsQuery->whereMonth('start_date_time', $currentMonth);
        } elseif ($type == 'week') {
            $currentWeekStartDate = Carbon::now()->startOfWeek();
            $currentWeekEndDate = Carbon::now()->endOfWeek();
            $yearlyTotalsQuery->whereBetween('start_date_time', [$currentWeekStartDate, $currentWeekEndDate]);
        }

        $yearlyTotals = $yearlyTotalsQuery->groupBy('status')->get();

        $statusCounts = [];
        foreach ($yearlyTotals as $total) {
            $status = $total->status;
            $statusCounts[] = (int) $total->{$status . '_bookings'};
        }

        return response()->json(['data' => $statusCounts, 'status' => true]);
    }




    public function setCurrentBranch($branch_id)
    {
        request()->session()->forget('selected_branch');

        request()->session()->put('selected_branch', $branch_id);

        return redirect()->back()->with('success', 'Current Branch Has Been Changes')->withInput();
    }

    public function resetBranch()
    {
        request()->session()->forget('selected_branch');

        return redirect()->back()->with('success', 'Show All Branch Content')->withInput();
    }

    public function setUserSetting(Request $request)
    {
        auth()->user()->update(['user_setting' => $request->settings]);

        return response()->json(['status' => true]);
    }
}
