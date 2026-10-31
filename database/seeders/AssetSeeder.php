<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $assets = [
        ['PMP-001', 'Pompa Intake 01',   'Pompa',  'Intake',       'active'],
        ['PMP-002', 'Pompa Intake 02',   'Pompa',  'Intake',       'active'],
        ['FLT-001', 'Filter Cepat 01',   'Filter', 'Filtrasi',     'under_maintenance'],
        ['FLT-002', 'Filter Karbon',     'Filter', 'Filtrasi',     'active'],
        ['SNS-001', 'Sensor pH Outlet',  'Sensor', 'Reservoir',    'broken'],
        ['PMP-003', 'Pompa Distribusi',  'Pompa',  'Distribusi',   'active'],
        ['DOS-001', 'Dosing Pump Kaporit','Dosing', 'Chemical Room','active'],
    ];

    foreach ($assets as [$code, $name, $cat, $loc, $status]) {
        Asset::updateOrCreate(
            ['asset_code' => $code],
            [
                'name' => $name,
                'category' => $cat,
                'location' => $loc,
                'status' => $status,
                'purchase_date' => now()->subMonths(rand(6, 48))->toDateString(),
            ]
        );
    }
}
}
