<?php

use Illuminate\Support\Facades\Route;

#--Auth
Route::get('/', function () {
    return redirect('/docs/api');
});
