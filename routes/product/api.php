<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::apiResource('/products', ProductController::class);

    Route::post('/products/active/{product}', [ProductController::class, 'active'])->name('products.active');
    Route::get('/products/new-product/get-category', [ProductController::class, 'category'])->name('products.category');
    Route::get('/products/new-product/get-brand', [ProductController::class, 'brand'])->name('products.brand');
});
