<?php

namespace App\Models;

use App\Enums\TripStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'departure_city',
        'departure_country',
        'departure_country_code',
        'arrival_city',
        'arrival_country',
        'arrival_country_code',
        'available_weight',
        'date',
        'time',
        'date',
        'time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'available_weight' => 'decimal:2',
        'status' => TripStatus::class,
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(TripBooking::class);
    }
}
