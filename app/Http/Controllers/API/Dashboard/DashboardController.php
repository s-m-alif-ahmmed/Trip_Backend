<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Enums\TripBookingStatus;
use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardOverviewResource;
use App\Models\Trip;
use App\Models\TripBooking;
use App\Traits\AllTraits;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use AllTraits;

    public function index()
    {
        $userId = auth()->id();

        $trips = Trip::where('user_id', $userId)->where('status', TripStatus::ACTIVE)->get();
        $tripIds = $trips->pluck('id');

        $totalActiveTrips = Trip::where('user_id', $userId)
            ->where('status', TripStatus::ACTIVE)
            ->count();

        $totalWeight = $trips->sum('available_weight');

        $bookings_completed = TripBooking::whereIn('trip_id', $tripIds)
            ->where('status', TripBookingStatus::COMPLETED)
            ->get();

        $bookings = TripBooking::whereIn('trip_id', $tripIds)
            ->where('status', '!=', TripBookingStatus::CANCELLED)
            ->get();

        $bookedWeight = $bookings->sum('weight');
        $totalAvailableWeight = $totalWeight - $bookedWeight;

        $totalRevenue = round($bookings->where('is_paid', true)->sum('total'), 2);

        $overview = [
            'total_available_weight' => $totalAvailableWeight,
            'total_weight'           => $totalWeight,
            'total_active_trips'     => $totalActiveTrips,
            'total_revenue'          => $totalRevenue,
        ];

        $data = new DashboardOverviewResource($overview);

        return $this->success('Data retrieve successfully.', $data, 200);
    }

}
