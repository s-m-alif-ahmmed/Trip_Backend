<?php

namespace Tests\Feature\API\Search;

use App\Enums\TripBookingStatus;
use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\TripBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Illuminate\Support\Facades\Log;

class SearchTripShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_api_returns_correct_available_weight()
    {
        $user = User::factory()->create();

        // Create a trip with 100kg available weight
        $trip = Trip::create([
            'user_id' => $user->id,
            'departure_city' => 'Daka',
            'departure_country' => 'Bangladesh',
            'departure_country_code' => 'BD',
            'arrival_city' => 'Dubai',
            'arrival_country' => 'UAE',
            'arrival_country_code' => 'AE',
            'available_weight' => 100,
            'status' => TripStatus::ACTIVE,
            'date' => now()->addDays(5)->toDateString(),
            'time' => '10:00',
        ]);

        $commonData = [
            'trip_id' => $trip->id,
            'date' => now()->toDateString(),
            'time' => '10:00:00',
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '1234567890',
            'address' => '123 Street',
        ];

        // Create a booking with 30kg
        TripBooking::create(array_merge($commonData, [
            'weight' => 30,
            'status' => TripBookingStatus::COMPLETED,
            'is_paid' => true,
        ]));

        // Create another booking with 20kg
        TripBooking::create(array_merge($commonData, [
            'weight' => 20,
            'status' => TripBookingStatus::PENDING,
            'is_paid' => false,
        ]));

        // Create a cancelled booking with 50kg (should NOT be subtracted)
        TripBooking::create(array_merge($commonData, [
            'weight' => 50,
            'status' => TripBookingStatus::CANCELLED,
            'is_paid' => false,
        ]));

        $response = $this->getJson("/api/search/trip/show/{$trip->id}");

        $response->assertStatus(200);

        // Expected available weight: 100 - (30 + 20) = 50
        $response->assertJsonPath('data.trip.available_weight', 50);
        $response->assertJsonPath('data.trip.total_weight', 100);
    }
}
