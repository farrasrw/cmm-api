<?php

use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

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