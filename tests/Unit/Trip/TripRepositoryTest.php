<?php

namespace Tests\Unit\Trip;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use App\Repositories\TripRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TripRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new TripRepository(new Trip());
    }

    public function test_repository_can_create_trip()
    {
        $user = User::factory()->create();

        $trip = $this->repo->create([
            'user_id' => $user->id,
            'departure_country' => 'USA',
            'departure_city' => 'NYC',
            'arrival_city' => 'LA',
            'available_weight' => 10,
            'date'              => '2026-03-01',
            'time' => '10:00',
            'status' => TripStatus::ACTIVE,
        ]);

        $this->assertDatabaseHas('trips', ['departure_city' => 'NYC']);
        $this->assertEquals('NYC', $trip->departure_city);
    }

    public function test_repository_can_find_trip()
    {
        $trip = Trip::factory()->create();

        $found = $this->repo->find($trip->id);

        $this->assertEquals($trip->id, $found->id);
    }

    public function test_repository_can_update_trip()
    {
        $trip = Trip::factory()->create(['departure_city' => 'Old City']);

        $updated = $this->repo->update($trip, [
            'departure_city' => 'New City'
        ]);

        $this->assertEquals('New City', $updated->departure_city);
        $this->assertDatabaseHas('trips', ['departure_city' => 'New City']);
    }

    public function test_repository_can_delete_trip()
    {
        $trip = Trip::factory()->create();

        $deleted = $this->repo->delete($trip);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('trips', ['id' => $trip->id]);
    }

    public function test_repository_can_get_all_trips()
    {
        Trip::factory(3)->create();

        $all = $this->repo->all();

        $this->assertCount(3, $all);
    }
}
