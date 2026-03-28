<?php

namespace Database\Seeders;

use App\Models\TripBooking;
use App\Models\User;
use Illuminate\Database\Seeder;

class TripBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create();
        }

        TripBooking::factory()
            ->count(rand(5, 10))
            ->create()
            ->each(function ($booking) use ($users) {
                // Attach 1 to 3 random users to each booking
                $booking->users()->attach(
                    $users->random(rand(1, min(3, $users->count())))->pluck('id')->toArray()
                );
            });
    }
}
