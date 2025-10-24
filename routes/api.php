<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AcidLovingCropController;
use App\Http\Controllers\Api\LandscapeController;

Route::get('/test', function () {
    return response()->json(['message' => 'Web route working!']);
});

Route::prefix('acid-loving-crop')->group(function () {
    Route::get('/', [AcidLovingCropController::class, 'index']);   // List all crops
    Route::post('/', [AcidLovingCropController::class, 'store']);  // Create new crop(s)
    // Route::get('/{id}', [AcidLovingCropController::class, 'show']);       // Show single crop
    // Route::put('/{id}', [AcidLovingCropController::class, 'update']);     // Update crop
    // Route::delete('/{id}', [AcidLovingCropController::class, 'destroy']); // Delete crop
});


Route::prefix('landscape')->group(function () {
    Route::get('/', [LandscapeController::class, 'index']);   // List all crops
    Route::post('/', [LandscapeController::class, 'store']);  // Create new crop(s)
    // Route::get('/{id}', [AcidLovingCropController::class, 'show']);       // Show single crop
    // Route::put('/{id}', [AcidLovingCropController::class, 'update']);     // Update crop
    // Route::delete('/{id}', [AcidLovingCropController::class, 'destroy']); // Delete crop
});