<?php

namespace App\Mail;

use App\Models\PartnerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnerConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public PartnerApplication $partner;

    public function __construct(PartnerApplication $partner)
    {
        $this->partner = $partner;
    }

    public function build()
    {
        return $this
            ->subject('We Received Your Partnership Application')
            ->view('emails.applications.partner-confirmation');
    }
}
