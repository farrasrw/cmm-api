<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SparePart;

class SparePartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $parts = [
        ['SP-001', 'Seal Pompa',        20, 5,  'pcs'],
        ['SP-002', 'Bearing 6205',      12, 4,  'pcs'],
        ['SP-003', 'Oli Pelumas',       30, 10, 'liter'],
        ['SP-004', 'Probe pH',           3, 2,  'pcs'],
        ['SP-005', 'Media Karbon Aktif', 8, 3,  'karung'],
    ];

    foreach ($parts as [$no, $name, $stock, $min, $unit]) {
        SparePart::updateOrCreate(
            ['part_number' => $no],
            ['name' => $name, 'stock_quantity' => $stock, 'min_stock_level' => $min, 'unit' => $unit]
        );
    }
}
}
