<?php

namespace App\Services;

use App\Models\SparePart;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkOrderService
{
    /**
     * Tutup work order dan kurangi stok spare part yang dipakai.
     *
     * @param array<int, array{spare_part_id:int, quantity:int}> $parts
     */
    public function complete(WorkOrder $workOrder, array $parts = []): WorkOrder
    {
        return DB::transaction(function () use ($workOrder, $parts) {
            // Kunci baris WO agar tidak bisa ditutup dua kali secara bersamaan
            $workOrder = WorkOrder::lockForUpdate()->findOrFail($workOrder->id);

            if ($workOrder->status === 'closed') {
                throw ValidationException::withMessages([
                    'status' => ['Work order sudah ditutup.'],
                ]);
            }

            // Gabungkan part yang dikirim ganda: [spare_part_id => total quantity]
            $needed = collect($parts)
                ->groupBy('spare_part_id')
                ->map(fn ($rows) => (int) $rows->sum('quantity'));

            // Kunci baris spare part, urut by id supaya terhindar dari deadlock
            $stock = SparePart::whereIn('id', $needed->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Validasi semua stok dulu sebelum ada yang dikurangi
            foreach ($needed as $partId => $qty) {
                $part = $stock[$partId];

                if ($part->stock_quantity < $qty) {
                    throw ValidationException::withMessages([
                        'parts' => [
                            "Stok {$part->name} tidak cukup (tersedia {$part->stock_quantity}, diminta {$qty}).",
                        ],
                    ]);
                }
            }

            foreach ($needed as $partId => $qty) {
                $stock[$partId]->decrement('stock_quantity', $qty);
                $workOrder->spareParts()->attach($partId, ['quantity_used' => $qty]);
            }

            $workOrder->update([
                'status' => 'closed',
                'completed_at' => now(),
            ]);

            return $workOrder->load(['asset', 'creator', 'technician', 'spareParts']);
        });
    }
}