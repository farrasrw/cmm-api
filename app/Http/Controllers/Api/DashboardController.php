<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\SparePart;
use App\Models\WorkOrder;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        // 6 bulan terakhir, termasuk bulan ini
        $months = collect(range(5, 0))
            ->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        $perMonth = WorkOrder::where('created_at', '>=', $months->first())
            ->selectRaw("to_char(created_at, 'YYYY-MM') as ym, count(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $workOrderStatus = WorkOrder::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $assetStatus = Asset::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'assets' => [
                'total' => Asset::count(),
                'active' => (int) ($assetStatus['active'] ?? 0),
                'under_maintenance' => (int) ($assetStatus['under_maintenance'] ?? 0),
                'broken' => (int) ($assetStatus['broken'] ?? 0),
            ],
            'work_orders' => [
                'total' => WorkOrder::count(),
                'open' => (int) ($workOrderStatus['open'] ?? 0),
                'in_progress' => (int) ($workOrderStatus['in_progress'] ?? 0),
                'pending_parts' => (int) ($workOrderStatus['pending_parts'] ?? 0),
                'closed' => (int) ($workOrderStatus['closed'] ?? 0),
                'overdue' => WorkOrder::where('status', '!=', 'closed')
                    ->whereDate('due_date', '<', today())
                    ->count(),
            ],
            'low_stock_parts' => SparePart::whereColumn('stock_quantity', '<=', 'min_stock_level')
                ->orderBy('stock_quantity')
                ->get(['id', 'part_number', 'name', 'stock_quantity', 'min_stock_level', 'unit']),
            'monthly_work_orders' => $months->map(fn ($m) => [
                'month' => $m->format('Y-m'),
                'label' => $m->format('M Y'),
                'total' => (int) ($perMonth[$m->format('Y-m')] ?? 0),
            ])->values(),
            'recent_work_orders' => WorkOrder::with('asset:id,asset_code,name')
                ->latest()
                ->limit(5)
                ->get(['id', 'asset_id', 'title', 'priority', 'status', 'due_date']),
        ]);
    }
}