<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'booking_name'   => $this->full_name,
            'email'          => $this->email,
            'phone'          => $this->phone_number,
            'address'        => $this->address,
            'pickup_address' => $this->pickup_address,
            'weight'         => (float) $this->weight,
            'weight_price'   => (float) $this->weight_per_kg_price,
            'service_fee'    => (float) $this->service_fee,
            'pickup_fee'     => (float) $this->pickup_fee,
            'total_paid'     => (float) $this->total,
            'pickup_status'  => (bool) $this->pickup_service_status,
            'status'         => $this->status->label(),
            'is_paid'        => (bool) $this->is_paid,
            'created_at'     => $this->created_at->format('Y-m-d H:i'),
            
            // Nested Trip Information (matching TripResource format)
            'trip' => [
                'id'            => $this->trip->id,
                'departureCity' => [
                    'city'        => $this->trip->departure_city,
                    'country'     => $this->trip->departure_country,
                    'countryCode' => $this->trip->departure_country_code,
                ],
                'arrivalCity' => [
                    'city'        => $this->trip->arrival_city,
                    'country'     => $this->trip->arrival_country,
                    'countryCode' => $this->trip->arrival_country_code,
                ],
                'date'   => $this->trip->date?->format('Y-m-d'),
                'time'   => Carbon::parse($this->trip->time)->format('H:i'),
                'status' => $this->trip->status->value,
            ],
        ];
    }
}
