<?php

namespace App\Services;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Repositories\TripRepository;

class TripService
{
    protected TripRepository $repo;

    public function __construct(TripRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllTrips($request = null)
    {
        return $this->repo->all();
    }

    public function getTripsForUser(int $userId, int $perPage = 10)
    {
        return $this->repo->forUser($userId, $perPage);
    }

    public function showTrip(int $id): Trip
    {
        return $this->repo->find($id);
    }

    public function createTrip(int $userId, array $tripsData)
    {
        $createdTrips = collect();

        foreach ($tripsData as $data) {

            $trip = $this->repo->create([
                'user_id' => $userId,

                // Departure
                'departure_city'         => $data['departureCity']['city'],
                'departure_country'      => $data['departureCity']['country'],
                'departure_country_code' => $data['departureCity']['countryCode'],

                // Arrival
                'arrival_city'           => $data['arrivalCity']['city'],
                'arrival_country'        => $data['arrivalCity']['country'],
                'arrival_country_code'   => $data['arrivalCity']['countryCode'],

                // Trip Info
                'available_weight' => $data['weight'],
                'date'             => $data['date'],
                'time'             => $data['time'],
                'status'           => TripStatus::ACTIVE->value,
            ]);

            $createdTrips->push($trip);
        }

        return $createdTrips->count() === 1
            ? $createdTrips->first()
            : $createdTrips;
    }

    public function updateTrip(Trip $trip, array $data): Trip
    {
        $tripData = $data[0] ?? $data;

        return $this->repo->update($trip, [

            'departure_city'         => $tripData['departureCity']['city'] ?? $trip->departure_city,
            'departure_country'      => $tripData['departureCity']['country'] ?? $trip->departure_country,
            'departure_country_code' => $tripData['departureCity']['countryCode'] ?? $trip->departure_country_code,

            'arrival_city'           => $tripData['arrivalCity']['city'] ?? $trip->arrival_city,
            'arrival_country'        => $tripData['arrivalCity']['country'] ?? $trip->arrival_country,
            'arrival_country_code'   => $tripData['arrivalCity']['countryCode'] ?? $trip->arrival_country_code,

            'available_weight' => $tripData['weight'] ?? $trip->available_weight,
            'date'             => $tripData['date'] ?? $trip->date,
            'time'             => $tripData['time'] ?? $trip->time,
        ]);
    }

    public function deleteTrip(Trip $trip)
    {
        return $this->repo->delete($trip);
    }

    public function getBookingsForUser(int $userId, int $perPage = 10)
    {
        return $this->repo->getBookingsForUser($userId, $perPage);
    }
}
