<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FertRightController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/landing', function () {
    return view('landing_fert');
});

Route::post('/fert', [FertRightController::class, 'generate']);