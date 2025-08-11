<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total' => (int) $this['total'],
            'total_value' => (float) $this['total_value'],
            'average_price' => (float) $this['average_price'],
            'low_stock_count' => (int) $this['low_stock_count'],
        ];
    }
}
