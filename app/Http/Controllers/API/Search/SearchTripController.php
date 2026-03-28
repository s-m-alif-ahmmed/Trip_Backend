<?php

namespace App\Http\Controllers\API\Search;

use App\Enums\TripStatus;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Traits\AllTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SearchTripController extends Controller
{
    use AllTraits;

    public function search(Request $request)
    {
        $query = Trip::query()
            ->where('status', TripStatus::ACTIVE)
            ->whereDate('date', '>=', now()->toDateString())
            ->withSum(['bookings' => function ($query) {
                $query->where('status', '!=', \App\Enums\TripBookingStatus::CANCELLED->value);
            }], 'weight');

        // Apply granular filters based on provided parameters
        $query->when($request->filled('departure_country'), fn($q) => $q->where('departure_country', $request->departure_country))
            ->when($request->filled('departure_city'), fn($q) => $q->where('departure_city', $request->departure_city))
            ->when($request->filled('departure_country_code'), fn($q) => $q->where('departure_country_code', $request->departure_country_code))
            ->when($request->filled('arrival_country'), fn($q) => $q->where('arrival_country', $request->arrival_country))
            ->when($request->filled('arrival_city'), fn($q) => $q->where('arrival_city', $request->arrival_city))
            ->when($request->filled('arrival_country_code'), fn($q) => $q->where('arrival_country_code', $request->arrival_country_code))
            ->when($request->filled('date'), fn($q) => $q->whereDate('date', $request->date));

        // Pagination (default 8 per page)
        $trips = $query->orderBy('date')
            ->orderBy('time')
            ->paginate($request->per_page ?? 8);

        // Wrap data in resource
        $data = TripResource::collection($trips);

        return Helper::jsonResponsePagination(true, 'Data retrieved successfully', 200, $data, true, $trips);
    }

    public function show($id)
    {
        // Get main trip
        $trip = Trip::where('id', $id)
            ->where('status', TripStatus::ACTIVE)
            ->whereDate('date', '>=', Carbon::today())
            ->withSum(['bookings' => function ($query) {
                $query->where('status', '!=', \App\Enums\TripBookingStatus::CANCELLED->value);
            }], 'weight')
            ->firstOrFail();

        // Find other trips with same departure & arrival (exclude this trip)
        $matchingTrips = Trip::where('departure_country', $trip->departure_country)
            ->where('departure_city', $trip->departure_city)
            ->where('departure_country_code', $trip->departure_country_code)
            ->where('arrival_country', $trip->arrival_country)
            ->where('arrival_country_code', $trip->arrival_country_code)
            ->where('arrival_city', $trip->arrival_city)
            ->where('status', TripStatus::ACTIVE)
            ->where('id', '!=', $trip->id)
            ->whereDate('date', '>=', Carbon::today())
            ->withSum(['bookings' => function ($query) {
                $query->where('status', '!=', \App\Enums\TripBookingStatus::CANCELLED->value);
            }], 'weight')
            ->orderBy('date')
            ->orderBy('time')
            ->take(3)
            ->get();

        // Prepare response
        $data = [
            'trip'           => new TripResource($trip),
            'matching_trips' => TripResource::collection($matchingTrips),
        ];

        return $this->success('Trip retrieved successfully', $data, 200);
    }

}
