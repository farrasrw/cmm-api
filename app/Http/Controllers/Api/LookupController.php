<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    public function technicians(): JsonResponse
    {
        return response()->json(
            User::whereHas('role', fn ($q) => $q->where('name', 'Technician'))
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function spareParts(): JsonResponse
    {
        return response()->json(
            SparePart::orderBy('name')
                ->get(['id', 'part_number', 'name', 'stock_quantity', 'unit'])
        );
    }
}