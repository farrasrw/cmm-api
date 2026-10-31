<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteWorkOrderRequest;
use App\Http\Requests\StoreWorkOrderRequest;
use App\Http\Requests\UpdateWorkOrderRequest;
use App\Http\Requests\UpdateWorkOrderStatusRequest;
use App\Http\Resources\WorkOrderResource;
use App\Models\WorkOrder;
use App\Services\WorkOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    private const WITH = ['asset', 'creator', 'technician', 'spareParts'];

    public function index(Request $request)
    {
        $user = $request->user();

        $orders = WorkOrder::with(['asset', 'technician'])
            // Teknisi hanya melihat tugasnya sendiri
            ->when($user->hasRole('Technician'), fn ($q) => $q->where('assigned_to', $user->id))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->priority, fn ($q, $v) => $q->where('priority', $v))
            ->when($request->asset_id, fn ($q, $v) => $q->where('asset_id', $v))
            ->when(
                $request->assigned_to && ! $user->hasRole('Technician'),
                fn ($q) => $q->where('assigned_to', $request->assigned_to)
            )
            ->when($request->search, fn ($q, $v) => $q->where('title', 'ilike', "%{$v}%"))
            ->latest()
            ->paginate(min($request->integer('per_page', 10), 100));

        return WorkOrderResource::collection($orders);
    }

    public function store(StoreWorkOrderRequest $request): JsonResponse
    {
        $workOrder = WorkOrder::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
            'status' => 'open',
        ]);

        return (new WorkOrderResource($workOrder->load(self::WITH)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, WorkOrder $workOrder): WorkOrderResource
    {
        $this->ensureOwnTask($request, $workOrder);

        return new WorkOrderResource($workOrder->load(self::WITH));
    }

    public function update(UpdateWorkOrderRequest $request, WorkOrder $workOrder): WorkOrderResource
    {
        $workOrder->update($request->validated());

        return new WorkOrderResource($workOrder->load(self::WITH));
    }

    public function updateStatus(UpdateWorkOrderStatusRequest $request, WorkOrder $workOrder): WorkOrderResource
    {
        $this->ensureOwnTask($request, $workOrder);

        if ($workOrder->status === 'closed') {
            abort(422, 'Work order yang sudah ditutup tidak bisa diubah statusnya.');
        }

        $workOrder->update(['status' => $request->validated('status')]);

        return new WorkOrderResource($workOrder->load(self::WITH));
    }

    public function complete(
        CompleteWorkOrderRequest $request,
        WorkOrder $workOrder,
        WorkOrderService $service
    ): WorkOrderResource {
        $this->ensureOwnTask($request, $workOrder);

        $closed = $service->complete($workOrder, $request->validated('parts', []));

        return new WorkOrderResource($closed);
    }

    public function destroy(WorkOrder $workOrder): JsonResponse
    {
        $workOrder->delete();

        return response()->json(null, 204);
    }

    /** Teknisi hanya boleh menyentuh work order yang ditugaskan kepadanya. */
    private function ensureOwnTask(Request $request, WorkOrder $workOrder): void
    {
        $user = $request->user();

        if ($user->hasRole('Technician') && $workOrder->assigned_to !== $user->id) {
            abort(403, 'Work order ini bukan tugas Anda.');
        }
    }
}