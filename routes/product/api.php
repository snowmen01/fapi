<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::apiResource('/products', ProductController::class);

    Route::post('/products/{product}/add-gallery', [ProductController::class, 'productGalleries'])->name('products.productGalleries');
    Route::get('/products/public-store/all-product', [ProductController::class, 'getlistProducts'])->name('products.getlistProducts');
    Route::get('/products/public-store/{slug}/product', [ProductController::class, 'getProductDetails'])->name('products.getProductDetails');
    Route::get('/products/public-store/{slug}/findWithProperty', [ProductController::class, 'getProductByProperties'])->name('products.getProductByProperties');
    Route::post('/products/active/{product}', [ProductController::class, 'active'])->name('products.active');
    Route::get('/products/new-product/get-category', [ProductController::class, 'category'])->name('products.category');
    Route::get('/products/new-product/get-brand', [ProductController::class, 'brand'])->name('products.brand');
    Route::get('/products/new-product/get-option', [ProductController::class, 'option'])->name('products.option');
});
