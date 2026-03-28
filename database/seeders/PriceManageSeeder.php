<?php

namespace Database\Seeders;

use App\Models\PriceManage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceManageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PriceManage::create([
            'service_fee' => 20.00,
            'pickup_fee' => 10.00,
            'weight_per_kg_price' => 10.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
