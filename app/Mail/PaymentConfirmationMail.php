<?php

namespace App\Mail;

use App\Models\ClientPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable
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
            ->from('no-reply@pikozm.com', 'PikoPay') // empty name
            ->subject('New Payment Received – ' . $this->payment->purpose)
            ->view('emails.pikopay.received')->with([
                'payment' => $this->payment,
            ]);
    }
}