<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')->prefix('api/v1')->group(function () {
    // crud işlemleri için tüm api kaynak rotalarını doğrudan resource ile tanımlar
    Route::apiResource('locations', LocationController::class);

    // locations kaynağına rota listesi endpointi ekler
    Route::post('locations/route-list', [LocationController::class, 'getRouteList']);
});
