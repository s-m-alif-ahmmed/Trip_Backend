<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceManage extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_fee',
        'service_fee',
        'weight_per_kg_price',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
