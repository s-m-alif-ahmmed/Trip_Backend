<?php

namespace App\Enums;

enum TripBookingStatus: string
{
    case PENDING = 'Pending';
    case COMPLETED = 'Completed';
    case CANCELLED = 'Cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }
}
