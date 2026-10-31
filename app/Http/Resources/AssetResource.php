<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_code' => $this->asset_code,
            'name' => $this->name,
            'category' => $this->category,
            'location' => $this->location,
            'purchase_date' => $this->purchase_date?->toDateString(),
            'status' => $this->status,
            'work_orders_count' => $this->whenCounted('workOrders'),
            'created_at' => $this->created_at,
        ];
    }
}