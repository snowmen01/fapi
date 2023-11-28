<?php

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::apiResource('/orders', OrderController::class);
});
