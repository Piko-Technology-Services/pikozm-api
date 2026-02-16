<?php

namespace App\Mail;

use App\Models\ClientPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public ClientPayment $payment;

    public function __construct(ClientPayment $payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        return $this
            ->subject('Payment Received – ' . $this->payment->reference)
            ->view('emails.pikopay.thank-you')->with([
                'payment' => $this->payment,
            ]);
    }
}