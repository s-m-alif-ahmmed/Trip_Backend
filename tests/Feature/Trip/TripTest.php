<?php

namespace Tests\Feature\Trip;

use Tests\TestCase;
use App\Models\User;
use App\Models\Trip;
use App\Enums\TripStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_trip()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $payload = [
            [
                'departure_country' => 'USA',
                'departure_city'    => 'New York',
                'arrival_city'      => 'Los Angeles',
                'available_weight'  => 10,
                'date'              => '2026-03-01',
                'time'              => '10:00',
                'status'            => TripStatus::ACTIVE->value,
            ]
        ];

        $response = $this->postJson('/api/trips', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data',
                'message',
                'status'
            ]);

        $this->assertDatabaseHas('trips', [
            'departure_city' => 'New York',
            'arrival_city'   => 'Los Angeles',
            'status'         => TripStatus::ACTIVE->value,
        ]);
    }

    public function test_can_update_trip()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'departure_city' => 'Old City',
            'status' => TripStatus::ACTIVE->value,
        ]);

        $payload = [
            [
                'departure_country' => 'Canada',
                'departure_city'    => 'Toronto',
                'arrival_city'      => 'Vancouver',
                'available_weight'  => 25,
                'date'              => '2026-05-10',
                'time'              => '14:30',
                'status'            => TripStatus::ACTIVE->value,
            ]
        ];

        $response = $this->postJson("/api/trips/{$trip->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
                'status'
            ]);

        $this->assertDatabaseHas('trips', [
            'id'               => $trip->id,
            'departure_city'   => 'Toronto',
            'arrival_city'     => 'Vancouver',
            'status'           => TripStatus::ACTIVE->value,
        ]);
    }

    public function test_can_list_trips()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Trip::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJson('/api/trips');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
                'status'
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_delete_trip()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $trip = Trip::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->deleteJson("/api/trips/{$trip->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('trips', [
            'id' => $trip->id
        ]);
    }

    public function test_show_returns_404_if_trip_not_found()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/trips/9999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Resource not found',
                'status'  => 404,
            ]);
    }
}
