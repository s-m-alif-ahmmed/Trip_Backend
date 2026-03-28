<?php

namespace Database\Factories;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition()
    {
        return [
            'user_id'                => User::factory(),
            'departure_city'         => $this->faker->city,
            'departure_country'      => $this->faker->country,
            'departure_country_code' => $this->faker->countryCode,
            'arrival_city'           => $this->faker->city,
            'arrival_country'        => $this->faker->country,
            'arrival_country_code'   => $this->faker->countryCode,
            'available_weight'       => $this->faker->numberBetween(1, 100),
            'date'                   => $this->faker->date('Y-m-d'),
            'time'                   => $this->faker->time('H:i'),
            'status'                 => TripStatus::ACTIVE,
        ];
    }
}
