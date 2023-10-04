<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::apiResource('/products', ProductController::class);
});
