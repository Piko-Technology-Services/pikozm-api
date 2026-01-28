<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class HealthController extends Controller
{
    public function index()
    {
        return response()->json([
            'api' => [
                'status' => 'online',
                'version' => '1.0.0',
                'timestamp' => now()->toDateTimeString(),
            ],

            'services' => [
                'database' => $this->checkDatabase(),
                'email'    => $this->checkMail(),
                'payments' => $this->checkPayments(),
            ],
        ]);
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return 'connected';
        } catch (\Exception $e) {
            return 'down';
        }
    }

    private function checkMail()
    {
        try {
            Mail::raw('Health check', function () {});
            return 'configured';
        } catch (\Exception $e) {
            return 'down';
        }
    }

    private function checkPayments()
    {
        try {
            
            Http::get('https://pay.lenco.co/');
            return 'reachable';
        } catch (\Exception $e) {
            return 'down';
        }
    }
}
