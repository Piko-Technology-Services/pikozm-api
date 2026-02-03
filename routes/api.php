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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard/support', [DigitalImpactController::class, 'getAllDonations']);
});

use App\Http\Controllers\Api\ImpactFormsController;

Route::prefix('digital-impact')->group(function () {
    Route::post('/partner', [DigitalImpactController::class, 'storePartner']);
    Route::post('/volunteer', [DigitalImpactController::class, 'storeVolunteer']);
    Route::post('/support-request', [DigitalImpactController::class, 'storeSupportRequest']);
});



Route::get('/test-webhook-signature', function () {
    // Use the secret from .env (Lenco dashboard secret)
    $secret = env('LENCO_SECRET_KEY');

    // Example payload to simulate a successful donation
    $payload = json_encode([
        "event" => "collection.successful",
        "data" => [
            "reference" => "DIGIMP-b3c67038-3165-4398-86ce-0fcf02a91b53",  // Change this to a valid reference from your DB
            "amount" => 1,
            "currency" => "ZMW"
        ]
    ]);

    // Compute the signature just like Lenco would
    $webhookHashKey = hash('sha256', $secret);
    $signature = hash_hmac('sha512', $payload, $webhookHashKey);

    // Return both the payload and the signature
    return response()->json([
        'signature' => $signature,
        'payload' => json_decode($payload)
    ]);
});

