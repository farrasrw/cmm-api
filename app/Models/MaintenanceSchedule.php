<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'asset_id', 'task_name', 'frequency_days',
        'last_performed_date', 'next_due_date',
    ];

    protected $casts = [
        'last_performed_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}