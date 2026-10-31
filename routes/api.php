<?php

use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->group(function () {
    // ... route auth dan asset yang sudah ada ...

    // Work order: semua role (teknisi dibatasi ke tugasnya sendiri di controller)
    Route::get('/work-orders', [WorkOrderController::class, 'index']);
    Route::get('/work-orders/{workOrder}', [WorkOrderController::class, 'show']);
    Route::patch('/work-orders/{workOrder}/status', [WorkOrderController::class, 'updateStatus']);
    Route::post('/work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete']);

    Route::middleware('role:Admin,Manager')->group(function () {
        // ... route asset yang sudah ada ...
        Route::post('/work-orders', [WorkOrderController::class, 'store']);
        Route::put('/work-orders/{workOrder}', [WorkOrderController::class, 'update']);
    });

    Route::delete('/work-orders/{workOrder}', [WorkOrderController::class, 'destroy'])
        ->middleware('role:Admin');
    });

    // Semua role yang login boleh melihat
    Route::get('/assets', [AssetController::class, 'index']);
    Route::get('/assets/{asset}', [AssetController::class, 'show']);

    // Hanya Admin dan Manager yang boleh mengubah
    Route::middleware('role:Admin,Manager')->group(function () {
        Route::post('/assets', [AssetController::class, 'store']);
        Route::put('/assets/{asset}', [AssetController::class, 'update']);
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy']);
    });
});