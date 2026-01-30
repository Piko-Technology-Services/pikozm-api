<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DigitalImpactController;


Route::get('/', [HealthController::class, 'index']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Route::prefix('digital-impact')->group(function () {
//     Route::post('/support', [DigitalImpactController::class, 'donate']);
// });

Route::post('/support/initiate', [DigitalImpactController::class, 'initiateDonation']);
Route::post('/support/processing', [DigitalImpactController::class, 'markProcessing']);
Route::post('/webhooks/lenco', [DigitalImpactController::class, 'handleLencoWebhook']);



