<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SparePart extends Model
{
    protected $fillable = [
        'part_number', 'name', 'stock_quantity', 'min_stock_level', 'unit',
    ];

    public function workOrders(): BelongsToMany
    {
        return $this->belongsToMany(WorkOrder::class, 'work_order_parts')
            ->withPivot('quantity_used')
            ->withTimestamps();
    }
}