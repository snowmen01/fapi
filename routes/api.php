<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

require __DIR__ . "/user/api.php";
require __DIR__ . "/role/api.php";
require __DIR__ . "/product/api.php";
require __DIR__ . "/category/api.php";

#--Auth
Route::controller(AuthController::class)
    ->prefix('/auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/refresh', 'refresh')->name('refresh');
        Route::post('/logout', 'logout')->name('logout');
        Route::put('/register', 'register')->name('register');
    });
