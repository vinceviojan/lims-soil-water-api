<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FertRightController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/fert', [FertRightController::class, 'index']);