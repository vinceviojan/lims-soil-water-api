<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AcidLovingCropController;
use App\Http\Controllers\Api\CropsContoller;
use App\Http\Controllers\Api\CropsFertRightController;
use App\Http\Controllers\Api\FertRightResultController;
use App\Http\Controllers\Api\LandscapeController;
use App\Http\Controllers\Api\MaturityController;
use App\Http\Controllers\Api\MslRstController;

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

Route::prefix('crops')->group(function () {
    Route::get('/', [CropsContoller::class, 'index']);   // List all crops
    Route::post('/', [CropsContoller::class, 'store']);  // Create new crop(s)
    // Route::get('/{id}', [AcidLovingCropController::class, 'show']);       // Show single crop
    // Route::put('/{id}', [AcidLovingCropController::class, 'update']);     // Update crop
    // Route::delete('/{id}', [AcidLovingCropController::class, 'destroy']); // Delete crop
});


Route::prefix('crops-fert-right')->group(function () {
    Route::get('/', [CropsFertRightController::class, 'index']);   // List all crops
    Route::post('/', [CropsFertRightController::class, 'store']);  // Create new crop(s)
    // Route::get('/{id}', [AcidLovingCropController::class, 'show']);       // Show single crop
    // Route::put('/{id}', [AcidLovingCropController::class, 'update']);     // Update crop
    // Route::delete('/{id}', [AcidLovingCropController::class, 'destroy']); // Delete crop
});

Route::prefix('msl-rst')->group(function () {
    Route::get('/', [MslRstController::class, 'index']);   
    Route::post('/', [MslRstController::class, 'store']);  
    Route::get('/getYearList', [MslRstController::class, 'getYearList']);
    Route::get('/getProvince', [MslRstController::class, 'getProvince']);
    Route::get('/getMunicipality', [MslRstController::class, 'getMunicipality']);
    // Route::get('/{id}', [AcidLovingCropController::class, 'show']);       
    // Route::put('/{id}', [AcidLovingCropController::class, 'update']);     
    // Route::delete('/{id}', [AcidLovingCropController::class, 'destroy']); 
});

Route::prefix('fert-right')->group(function () {
    // Route::get('/', [MslRstController::class, 'index']);   // List all crops
    Route::post('/', [FertRightResultController::class, 'show']);  
    Route::get('/getVariety', [FertRightResultController::class, 'getVariety']);
    Route::get('/getLandscape', [FertRightResultController::class, 'getLandscape']);
    Route::get('/getAge', [FertRightResultController::class, 'getAge']);
    Route::post('/getFertRightResult', [FertRightResultController::class, 'getFertRightResult']);
    Route::get('/getSoilType', [FertRightResultController::class, 'getSoilType']);
    Route::get('/getCroppingSeason', [FertRightResultController::class, 'getCroppingSeason']);
    // Route::get('/{id}', [AcidLovingCropController::class, 'show']);       
    // Route::put('/{id}', [AcidLovingCropController::class, 'update']);     
    // Route::delete('/{id}', [AcidLovingCropController::class, 'destroy']); 
});

Route::prefix('maturity')->group(function () {
    Route::get('/', [MaturityController::class, 'index']);  
    Route::post('/', [MaturityController::class, 'store']);  
    // Route::get('/{id}', [AcidLovingCropController::class, 'show']);       
    // Route::put('/{id}', [AcidLovingCropController::class, 'update']);     
    // Route::delete('/{id}', [AcidLovingCropController::class, 'destroy']); 
});