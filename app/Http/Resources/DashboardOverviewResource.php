<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_available_weight' => $this['total_available_weight'],
            'total_weight'           => $this['total_weight'],
            'total_active_trips'     => $this['total_active_trips'],
            'total_revenue'          => $this['total_revenue'],
        ];
    }
}
