<?php

namespace App\Http\Controllers\API\Trip;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trip\TripRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Services\TripService;
use App\Traits\AllTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ProposeTripController extends Controller
{
    use AllTraits;

    protected TripService $service;

    public function __construct(TripService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $perPage = (int) request()->get('per_page', 10);

        $trips = $this->service->getTripsForUser(Auth::id(), $perPage);
        $resource = TripResource::collection($trips);

        return Helper::jsonResponsePagination(
            true,
            'Data retrieved successfully',
            200,
            $resource,
            true,
            $trips
        );
    }

    public function show($id)
    {
        $trip = $this->service->showTrip($id);
        $data = new TripResource($trip);
        return $this->ok('Data retrieved successfully', $data, 200);
    }

    public function store(TripRequest $request)
    {
        $result = $this->service->createTrip(Auth::id(), $request->validated());

        $data = $result instanceof Trip
            ? new TripResource($result)
            : TripResource::collection($result);

        return $this->success('Trip created successfully', $data, 201);
    }

    public function update(TripRequest $request, Trip $trip)
    {
        $trip = $this->service->updateTrip($trip, $request->validated());
        $data = new TripResource($trip);
        return $this->ok('Trip updated successfully', $data, 200);
    }

    public function destroy(Trip $trip)
    {
        $this->service->deleteTrip($trip);
        return $this->success('Trip deleted successfully', [], 200);
    }

    public function myBookings()
    {
        $perPage = (int) request()->get('per_page', 10);

        $bookings = $this->service->getBookingsForUser(Auth::id(), $perPage);
        $resource = BookingResource::collection($bookings);

        return Helper::jsonResponsePagination(
            true,
            'Data retrieved successfully',
            200,
            $resource,
            true,
            $bookings
        );
    }

    public function showBooking($id)
    {
        $booking = \App\Models\TripBooking::whereHas('users', function ($q) {
            $q->where('users.id', Auth::id());
        })
            ->with('trip')
            ->findOrFail($id);

        return $this->ok('Data retrieved successfully', new BookingResource($booking), 200);
    }
}
