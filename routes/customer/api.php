<?php

use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::apiResource('/customers', CustomerController::class);
});
