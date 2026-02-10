<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmationMail;

class ClientPaymentController extends Controller
{
    /**
     * Initiate payment
     */
    public function initiatePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email'     => 'nullable|email|max:255',
                'amount'    => 'required|numeric|min:1',
                'purpose'   => 'required|string|max:255',
                'currency'  => 'nullable|string|max:5',
            ]);

            $reference = 'PAY-' . Str::uuid();

            $payment = ClientPayment::create([
                'full_name' => $validated['full_name'],
                'email'     => $validated['email'],
                'amount'    => $validated['amount'],
                'purpose'   => $validated['purpose'],
                'currency'  => $validated['currency'] ?? 'ZMW',
                'reference' => $reference,
                'status'    => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment initialized',
                'data' => [
                    'reference'  => $reference,
                    'public_key'=> config('services.lenco.public_key'),
                    'payment'   => $payment,
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to initiate payment',
                'error'   => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

    /**
     * Mark as processing (frontend callback)
     */
    public function markProcessing(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $payment = ClientPayment::where('reference', $request->reference)->first();

        if (! $payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment reference not found',
            ], 404);
        }

        $payment->update(['status' => 'processing']);

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as processing',
        ]);
    }

    /**
     * Lenco webhook handler
     */
    public function handleLencoWebhook(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Lenco-Signature');

        $apiToken = config('services.lenco.secret_key');
        $webhookHashKey = hash('sha256', $apiToken);

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

        $reference = $event['data']['reference'] ?? null;

        $payment = ClientPayment::where('reference', $reference)->first();

        if (! $payment) {
            return response()->json(['status' => 'ignored'], 200);
        }

        if ($event['event'] === 'collection.successful') {
            $payment->update([
                'status' => 'paid',
                'payment_response' => $event,
            ]);

            if ($payment->email) {
                Mail::to($payment->email)
                    ->send(new PaymentConfirmationMail($payment));
            }
        }

        if ($event['event'] === 'collection.failed') {
            $payment->update([
                'status' => 'failed',
                'payment_response' => $event,
            ]);

            if ($payment->email) {
                Mail::to($payment->email)
                    ->send(new PaymentConfirmationMail($payment));
            }
        }

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Fetch all payments (dashboard)
     */
    public function getAllPayments(Request $request)
    {
        $payments = ClientPayment::latest()
            ->paginate($request->get('per_page', 20));

        $stats = [
            'total'   => ClientPayment::count(),
            'paid'    => ClientPayment::where('status', 'paid')->count(),
            'pending' => ClientPayment::whereIn('status', ['pending','processing'])->count(),
            'failed'  => ClientPayment::where('status', 'failed')->count(),
            'amount'  => ClientPayment::where('status', 'paid')->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'payments' => $payments,
                'stats'    => $stats,
            ],
        ]);
    }
}