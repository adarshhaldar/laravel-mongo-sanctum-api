<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', 'logout');
        Route::get('logout-from-all-device', 'logoutAll');
    });
});

Route::middleware('auth:sanctum')->prefix('vehicle')->controller(VehicleController::class)->group(function () {
    Route::get('list', 'list');
    Route::get('detail/{_id}', 'detail');
    Route::post('create', 'create');
    Route::post('update', 'update');
});
