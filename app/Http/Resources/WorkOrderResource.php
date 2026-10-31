<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date?->toDateString(),
            'completed_at' => $this->completed_at,
            'asset' => $this->whenLoaded('asset', fn () => [
                'id' => $this->asset->id,
                'asset_code' => $this->asset->asset_code,
                'name' => $this->asset->name,
            ]),
            'creator' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'technician' => $this->whenLoaded('technician', fn () => $this->technician ? [
                'id' => $this->technician->id,
                'name' => $this->technician->name,
            ] : null),
            'parts' => $this->whenLoaded('spareParts', fn () => $this->spareParts->map(fn ($p) => [
                'id' => $p->id,
                'part_number' => $p->part_number,
                'name' => $p->name,
                'unit' => $p->unit,
                'quantity_used' => $p->pivot->quantity_used,
            ])),
            'created_at' => $this->created_at,
        ];
    }
}