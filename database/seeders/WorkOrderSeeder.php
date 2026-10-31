<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $assetIds = Asset::pluck('id')->all();
    $creator  = User::where('email', 'admin@cmms.test')->value('id');
    $techs    = User::whereHas('role', fn ($q) => $q->where('name', 'Technician'))->pluck('id')->all();

    $titles = [
        'Ganti seal pompa bocor', 'Kalibrasi sensor pH', 'Backwash filter',
        'Ganti bearing', 'Cek oli pompa', 'Penggantian media karbon',
        'Inspeksi getaran motor', 'Perbaikan dosing pump', 'Pembersihan strainer',
        'Cek panel listrik',
    ];
    $priorities = ['low', 'medium', 'high', 'critical'];
    $statuses   = ['open', 'in_progress', 'pending_parts', 'closed'];

    foreach ($titles as $i => $title) {
        $status = $statuses[$i % 4];

        $date = now()->subDays(rand(0, 170));
        $workOrder = new WorkOrder([
            'asset_id'     => $assetIds[array_rand($assetIds)],
            'created_by'   => $creator,
            'assigned_to'  => $techs[array_rand($techs)],
            'title'        => $title,
            'description'  => "Pekerjaan: {$title}.",
            'priority'     => $priorities[array_rand($priorities)],
            'status'       => $status,
            'due_date'     => now()->addDays(rand(-10, 14))->toDateString(),
            'completed_at' => $status === 'closed' ? $date->copy()->addDays(rand(1, 5)) : null,
        ]);
        $workOrder->created_at = $date;
        $workOrder->updated_at = $date;
        $workOrder->save();
    };
    }
}
