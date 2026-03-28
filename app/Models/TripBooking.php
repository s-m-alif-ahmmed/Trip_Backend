<?php

namespace App\Models;

use App\Enums\TripBookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripBooking extends Model
{
    use HasFactory;
    protected $fillable = [
        'trip_id',
        'date',
        'time',
        'full_name',
        'email',
        'phone_number',
        'address',
        'pickup_address',
        'weight',
        'weight_per_kg_price',
        'service_fee',
        'pickup_fee',
        'total',
        'pickup_service_status',
        'transaction_id',
        'is_paid',
        'status',
    ];

    protected $casts = [
        'pickup_service_status' => 'boolean',
        'is_paid' => 'boolean',
        'weight_per_kg_price' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'pickup_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'status' => TripBookingStatus::class,
    ];

    public function trip()
    {
        return $this->belongsTo(\App\Models\Trip::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trip_booking_user');
    }
}
