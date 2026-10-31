<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::query()
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                      ->orWhere('asset_code', 'ilike', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->orderBy('asset_code')
            ->paginate($request->integer('per_page', 10));

        return AssetResource::collection($assets);
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = Asset::create($request->validated());

        return (new AssetResource($asset))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Asset $asset): AssetResource
    {
        return new AssetResource($asset->loadCount('workOrders'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset): AssetResource
    {
        $asset->update($request->validated());

        return new AssetResource($asset);
    }

    public function destroy(Asset $asset): JsonResponse
    {
        if ($asset->workOrders()->exists()) {
            return response()->json([
                'message' => 'Aset tidak bisa dihapus karena masih memiliki work order.',
            ], 409);
        }

        $asset->delete();

        return response()->json(null, 204);
    }
}