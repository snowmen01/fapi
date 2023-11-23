<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

require __DIR__ . "/banner/api.php";
require __DIR__ . "/brand/api.php";
require __DIR__ . "/category/api.php";
require __DIR__ . "/product/api.php";
require __DIR__ . "/role/api.php";
require __DIR__ . "/user/api.php";
require __DIR__ . "/property/api.php";

#--Auth
Route::controller(AuthController::class)
    ->prefix('/auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/refresh', 'refresh')->name('refresh');
        Route::post('/logout', 'logout')->name('logout');
        Route::put('/register', 'register')->name('register');
        Route::get('/reset-password', 'getReset')->name('getReset');
        Route::post('/reset-password', 'postReset')->name('postReset');
    });

#--NoAuth
Route::controller(BannerController::class)
    ->prefix('/banners')
    ->name('banners.')
    ->group(function () {
        Route::get('/get-all/store', 'getAllFront')->name('getAllFront');
    });
