<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'departureCity' => [
                'city'        => $this->departure_city,
                'country'     => $this->departure_country,
                'countryCode' => $this->departure_country_code,
            ],

            'arrivalCity' => [
                'city'        => $this->arrival_city,
                'country'     => $this->arrival_country,
                'countryCode' => $this->arrival_country_code,
            ],

            'total_weight'     => (float) $this->available_weight,
            'available_weight' => (float) ($this->available_weight - ($this->bookings_sum_weight ?? 0)),
            'date'             => $this->date?->format('Y-m-d'),
            'time'             => Carbon::parse($this->time)->format('H:i'),
            'status'           => $this->status->value,
        ];
    }
}
