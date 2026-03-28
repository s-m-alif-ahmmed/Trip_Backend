<?php

namespace Tests\Unit\Trip;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use App\Repositories\TripRepository;
use App\Services\TripService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TripService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TripService(new TripRepository(new Trip()));
    }

    public function test_service_can_create_trip()
    {
        $user = User::factory()->create();

        $trip = $this->service->createTrip($user->id, [
            [
                'departure_country' => 'USA',
                'departure_city'    => 'NYC',
                'arrival_city'      => 'Los Angeles',
                'available_weight'  => 10,
                'date'              => '2026-03-01',
                'time'              => '10:00',
                'status'            => TripStatus::ACTIVE->value,
            ]
        ]);

        $this->assertDatabaseHas('trips', [
            'departure_city' => 'NYC',
            'arrival_city'   => 'Los Angeles',
        ]);

        $this->assertEquals('NYC', $trip->departure_city);
        $this->assertEquals(TripStatus::ACTIVE->value, $trip->status->value);
    }

    public function test_service_can_update_trip()
    {
        $trip = Trip::factory()->create(['departure_city' => 'Old City']);

        $updated = $this->service->updateTrip($trip, [
            'departure_city' => 'New City'
        ]);

        $this->assertEquals('New City', $updated->departure_city);
        $this->assertDatabaseHas('trips', ['departure_city' => 'New City']);
    }

    public function test_service_can_delete_trip()
    {
        $trip = Trip::factory()->create();

        $deleted = $this->service->deleteTrip($trip);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('trips', ['id' => $trip->id]);
    }

    public function test_service_can_get_all_trips()
    {
        Trip::factory(3)->create();

        $all = $this->service->getAllTrips();

        $this->assertCount(3, $all);
    }
}
