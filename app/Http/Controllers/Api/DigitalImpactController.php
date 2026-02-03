<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Support\Facades\DB;
use App\Mail\DonationThankYouMail;
use App\Mail\DonationReceivedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\PartnerApplication;
use App\Models\VolunteerApplication;
use App\Models\SupportRequest;
use App\Mail\NewPartnerApplicationMail;
use App\Mail\PartnerConfirmationMail;
use App\Mail\NewVolunteerApplicationMail;
use App\Mail\VolunteerConfirmationMail;
use App\Mail\NewSupportRequestMail;
use App\Mail\SupportConfirmationMail;



class DigitalImpactController extends Controller
{

    private function sendDonationEmails(Donation $donation): void
    {
        // Send thank you email to donor
        Mail::to($donation->email)
            ->send(new DonationThankYouMail($donation));

        // Send notification to management
        $managementEmails = config('mail.management_emails', []);

        if (! empty($managementEmails)) {
            Mail::to($managementEmails)
                ->send(new DonationReceivedMail($donation));
        }
    }
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

        //    $this->sendDonationEmails($donation);

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

                // ✅ SEND EMAILS
                $this->sendDonationEmails($donation);
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


    public function getAllDonations(Request $request)
    {
        try {
            $donations = Donation::query()
                ->latest()
                ->paginate($request->get('per_page', 20));

            // Optional dashboard stats
            $stats = [
                'total_donations' => Donation::count(),
                'total_amount' => Donation::where('status', 'paid')->sum('amount'),
                'paid' => Donation::where('status', 'paid')->count(),
                'pending' => Donation::whereIn('status', ['pending', 'processing'])->count(),
                'failed' => Donation::where('status', 'failed')->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Donations fetched successfully',
                'data' => [
                    'donations' => $donations,
                    'stats' => $stats,
                ],
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch donations',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

     /* ---------------- PARTNER ---------------- */
public function storePartner(Request $request)
{
    try {
        $data = $request->validate([
            'name' => 'required|string',
            'contact_person' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'country' => 'required|string',
            'partnership_type' => 'required|string',
            'organization_type' => 'required|string',
            'message' => 'nullable|string',
        ]);

        $partner = PartnerApplication::create($data);

        // Management alert
        Mail::to(config('mail.management_emails'))
            ->send(new NewPartnerApplicationMail($partner));

        // Client confirmation
        Mail::to($partner->email)
            ->send(new PartnerConfirmationMail($partner));

        return response()->json([
            'success' => true,
            'message' => 'Partnership application submitted successfully',
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
            'message' => 'Unable to submit partnership application',
            'error' => config('app.debug') ? $e->getMessage() : 'Server error',
        ], 500);
    }
}

    /* ---------------- VOLUNTEER ---------------- */
public function storeVolunteer(Request $request)
{
    try {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'country' => 'required|string',
            'skills' => 'required|string',
            'availability' => 'required|string',
            'nrc' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:4096',
            'message' => 'nullable|string',
        ]);

        $nrcPath = $request->file('nrc')->store('volunteers/nrcs', 'public');
        $resumePath = $request->file('resume')->store('volunteers/resumes', 'public');

        $volunteer = VolunteerApplication::create([
            ...$data,
            'nrc_path' => $nrcPath,
            'resume_path' => $resumePath,
        ]);

        Mail::to(config('mail.management_emails'))
            ->send(new NewVolunteerApplicationMail($volunteer));

        Mail::to($volunteer->email)
            ->send(new VolunteerConfirmationMail($volunteer));

        return response()->json([
            'success' => true,
            'message' => 'Volunteer application submitted successfully',
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
            'message' => 'Unable to submit volunteer application',
            'error' => config('app.debug') ? $e->getMessage() : 'Server error',
        ], 500);
    }
}


    /* ---------------- DIGITAL SUPPORT ---------------- */
public function storeSupportRequest(Request $request)
{
    try {
        $data = $request->validate([
            'organization_name' => 'required|string',
            'contact_person' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'country' => 'required|string',
            'organization_type' => 'required|string',
            'support_needs' => 'required|string',
            'website' => 'nullable|url',
            'message' => 'nullable|string',
        ]);

        $support = SupportRequest::create($data);

        Mail::to(config('mail.management_emails'))
            ->send(new NewSupportRequestMail($support));

        Mail::to($support->email)
            ->send(new SupportConfirmationMail($support));

        return response()->json([
            'success' => true,
            'message' => 'Support request submitted successfully',
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
            'message' => 'Unable to submit support request',
            'error' => config('app.debug') ? $e->getMessage() : 'Server error',
        ], 500);
    }
}


/* ---------------- Fetch All Partners ---------------- */
public function getAllPartners()
{
    try {
        $partners = PartnerApplication::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $partners,
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Unable to fetch partners',
            'error' => config('app.debug') ? $e->getMessage() : 'Server error',
        ], 500);
    }
}

/* ---------------- Fetch All Volunteers ---------------- */
public function getAllVolunteers()
{
    try {
        $volunteers = VolunteerApplication::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $volunteers,
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Unable to fetch volunteers',
            'error' => config('app.debug') ? $e->getMessage() : 'Server error',
        ], 500);
    }
}

/* ---------------- Fetch All Support Requests ---------------- */
public function getAllSupportRequests()
{
    try {
        $requests = SupportRequest::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Unable to fetch support requests',
            'error' => config('app.debug') ? $e->getMessage() : 'Server error',
        ], 500);
    }
}


public function dashboardOverview()
{
    try {
        // Totals
        $donationsCount = Donation::count();
        $supportCount   = SupportRequest::count();
        $partnerCount   = PartnerApplication::count();
        $volunteerCount = VolunteerApplication::count();

        // Recent activity (latest 5 combined)
        $recentDonations = Donation::latest()->take(5)->get()->map(fn ($d) => [
            'type'  => 'donation',
            'title' => 'New Donation Received',
            'meta'  => "{$d->currency} {$d->amount} from {$d->name}",
            'date'  => $d->created_at,
        ]);

        $recentPartners = PartnerApplication::latest()->take(5)->get()->map(fn ($p) => [
            'type'  => 'partner',
            'title' => 'Partnership Application Submitted',
            'meta'  => $p->organization_type,
            'date'  => $p->created_at,
        ]);

        $recentVolunteers = VolunteerApplication::latest()->take(5)->get()->map(fn ($v) => [
            'type'  => 'volunteer',
            'title' => 'Volunteer Application',
            'meta'  => $v->skills,
            'date'  => $v->created_at,
        ]);

        $recentSupport = SupportRequest::latest()->take(5)->get()->map(fn ($s) => [
            'type'  => 'support',
            'title' => 'New Support Request',
            'meta'  => $s->support_needs,
            'date'  => $s->created_at,
        ]);

        // Merge + sort
        $recentActivity = collect()
            ->merge($recentDonations)
            ->merge($recentPartners)
            ->merge($recentVolunteers)
            ->merge($recentSupport)
            ->sortByDesc('date')
            ->take(5)
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'donations'   => $donationsCount,
                    'support'     => $supportCount,
                    'partners'    => $partnerCount,
                    'volunteers'  => $volunteerCount,
                ],
                'recent_activity' => $recentActivity,
            ],
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load dashboard overview',
            'error'   => config('app.debug') ? $e->getMessage() : 'Server error',
        ], 500);
    }
}


}
