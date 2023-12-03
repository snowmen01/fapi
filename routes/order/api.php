<?php

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::apiResource('/orders', OrderController::class);

    Route::post('/orders/status-order/{code}', [OrderController::class, 'status'])->name('orders.status');
    Route::post('/orders/status-payment/{code}', [OrderController::class, 'payment'])->name('orders.payment');
});
