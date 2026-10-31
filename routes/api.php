<?php

use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WorkOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LookupController;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Semua role yang login
    Route::get('/assets', [AssetController::class, 'index']);
    Route::get('/assets/{asset}', [AssetController::class, 'show']);
    Route::get('/spare-parts', [LookupController::class, 'spareParts']);   // <-- di luar grup role

    Route::get('/work-orders', [WorkOrderController::class, 'index']);
    Route::get('/work-orders/{workOrder}', [WorkOrderController::class, 'show']);
    Route::patch('/work-orders/{workOrder}/status', [WorkOrderController::class, 'updateStatus']);
    Route::post('/work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete']);

    // Hanya Admin dan Manager
    Route::middleware('role:Admin,Manager')->group(function () {
        Route::get('/dashboard', DashboardController::class);
        Route::get('/technicians', [LookupController::class, 'technicians']);

        Route::post('/assets', [AssetController::class, 'store']);
        Route::put('/assets/{asset}', [AssetController::class, 'update']);
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy']);

        Route::post('/work-orders', [WorkOrderController::class, 'store']);
        Route::put('/work-orders/{workOrder}', [WorkOrderController::class, 'update']);
    });

    Route::delete('/work-orders/{workOrder}', [WorkOrderController::class, 'destroy'])
        ->middleware('role:Admin');
});