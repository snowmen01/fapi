<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . "/user/api.php";
require __DIR__ . "/role/api.php";
require __DIR__ . "/product/api.php";

#--Auth
Route::get('/', function () {
    return view('welcome');
});
