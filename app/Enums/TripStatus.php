<?php

namespace App\Enums;

enum TripStatus: string
{
    case PENDING     = 'Pending';
    case ACTIVE      = 'Active';
    case DEACTIVATED = 'Deactivated';

    public function label(): string
    {
        return match($this) {
            self::PENDING      => 'Pending',
            self::ACTIVE       => 'Active',
            self::DEACTIVATED  => 'Deactivated',
        };
    }
}
