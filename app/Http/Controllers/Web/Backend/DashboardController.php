<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use App\Models\Verificaction;
use App\Models\TripBooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     *
     * @return View
     */


    public function index(): View
    {
        $data['admins'] = User::where('role', 'Admin')->count();
        $data['total_users'] = User::where('role', '!=', 'Admin')->count();

        // Global Summaries (Platform-wide)
        $globalStats = TripBooking::where('is_paid', true)
            ->select([
                DB::raw('COUNT(id) as total_bookings'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(service_fee) as total_service_fees'),
                DB::raw('SUM(pickup_fee) as total_pickup_fees'),
                DB::raw('SUM(weight) as total_weight_booked'),
                DB::raw('AVG(weight) as avg_weight'),
                DB::raw('AVG(total) as avg_gross'),
                DB::raw('AVG(service_fee) as avg_fees'),
            ])->first();

        $data['total_bookings'] = $globalStats->total_bookings ?? 0;
        $data['total_revenue'] = $globalStats->total_revenue ?? 0;
        $data['total_service_fees'] = $globalStats->total_service_fees ?? 0;
        $data['total_pickup_fees'] = $globalStats->total_pickup_fees ?? 0;
        $data['total_weight_booked'] = $globalStats->total_weight_booked ?? 0;

        // Averages Overview
        $data['avg_weight_per_booking'] = $globalStats->avg_weight ?? 0;
        $data['avg_gross_per_booking'] = $globalStats->avg_gross ?? 0;
        $data['avg_fee_per_booking'] = $globalStats->avg_fees ?? 0;

        return view('backend.layouts.dashboard.index', $data);
    }
}
