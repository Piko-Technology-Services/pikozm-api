<?php

namespace App\Mail;

use App\Models\PartnerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPartnerApplicationMail extends Mailable
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
            ->subject('New Partnership Application Received')
            ->view('emails.applications.partner-management');
    }
}
