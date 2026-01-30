<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;

class DigitalImpactController extends Controller
{
    // 1. Initiate donation
    public function initiateDonation(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'focus_area' => 'required|string',
                'amount' => 'required|numeric|min:1',
                'currency' => 'required|string|max:5',
                'message' => 'nullable|string',
            ]);

            $reference = 'DIGIMP-' . Str::uuid();

            $donation = Donation::create([
                ...$validated,
                'reference' => $reference,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Donation initialized',
                'data' => [
                    'reference' => $reference,
                    'public_key' => config('services.lenco.public_key'),
                    'donation' => $donation,
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to initiate donation',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

public function markProcessing(Request $request)
{
    try {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $donation = Donation::where('reference', $request->reference)->first();

        if (! $donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation reference not found',
            ], 404);
        }

        $donation->update([
            'status' => 'processing',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Donation marked as processing',
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Unable to mark donation as processing',
            'error' => config('app.debug') ? $e->getMessage() : 'Server error',
        ], 500);
    }
}


public function handleLencoWebhook(Request $request)
{
    $payload = $request->getContent();
    $signature = $request->header('X-Lenco-Signature');

    // Generate webhook hash key
    $apiToken = config('services.lenco.secret_key');
    $webhookHashKey = hash('sha256', $apiToken);

    // Generate local signature
    $computedSignature = hash_hmac(
        'sha512',
        $payload,
        $webhookHashKey
    );

    if (! hash_equals($computedSignature, $signature)) {
        Log::warning('Invalid Lenco webhook signature');
        return response()->json(['status' => 'invalid'], 401);
    }

    $event = json_decode($payload, true);

    // Handle collection events
    if ($event['event'] === 'collection.successful') {
        $reference = $event['data']['reference'] ?? null;

        $donation = Donation::where('reference', $reference)->first();

        if ($donation && $donation->status !== 'paid') {
            $donation->update([
                'status' => 'paid',
                'payment_response' => $event,
            ]);
        }
    }

    if ($event['event'] === 'collection.failed') {
        $reference = $event['data']['reference'] ?? null;

        $donation = Donation::where('reference', $reference)->first();

        if ($donation) {
            $donation->update([
                'status' => 'failed',
                'payment_response' => $event,
            ]);
        }
    }

    return response()->json(['status' => 'ok'], 200);
}


}
