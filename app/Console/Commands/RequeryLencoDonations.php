<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Donation;

class RequeryLencoDonations extends Command
{
    protected $signature = 'lenco:requery';

    protected $description = 'Requery Lenco donations to update their status';

    public function handle()
    {
        $donations = Donation::whereIn('status', ['pending', 'processing'])
            ->where('created_at', '<', now()->subMinutes(10))
            ->get();

        foreach ($donations as $donation) {
            $response = Http::withToken(config('services.lenco.secret_key'))
                ->get("https://api.lenco.co/collections/status/{$donation->reference}");

            if ($response->successful()) {
                $status = $response['data']['status'] ?? null;

                if ($status === 'successful') {
                    $donation->update([
                        'status' => 'paid',
                        'payment_response' => $response->json(),
                    ]);
                }

                if ($status === 'failed') {
                    $donation->update([
                        'status' => 'failed',
                        'payment_response' => $response->json(),
                    ]);
                }
            }
        }

        $this->info('Lenco donations re-query completed.');
        return Command::SUCCESS;
    }
}
