<?php

namespace Database\Factories;

use App\Enums\TripBookingStatus;
use App\Models\Trip;
use App\Models\TripBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripBookingFactory extends Factory
{
    protected $model = TripBooking::class;

    public function definition()
    {
        return [
            'trip_id'               => Trip::factory(),
            'date'                  => $this->faker->date(),
            'time'                  => $this->faker->time('H:i'),
            'full_name'             => $this->faker->name,
            'email'                 => $this->faker->safeEmail,
            'phone_number'          => $this->faker->phoneNumber,
            'address'               => $this->faker->address,
            'pickup_address'        => $this->faker->address,
            'weight'                => $this->faker->numberBetween(1, 50),
            'weight_per_kg_price'   => $this->faker->randomFloat(2, 5, 20),
            'service_fee'           => $this->faker->randomFloat(2, 10, 50),
            'pickup_fee'            => $this->faker->randomFloat(2, 5, 15),
            'total'                 => $this->faker->randomFloat(2, 50, 500),
            'pickup_service_status' => $this->faker->boolean,
            'transaction_id'        => $this->faker->uuid,
            'is_paid'               => $this->faker->boolean,
            'status'                => TripBookingStatus::PENDING,
        ];
    }
}
