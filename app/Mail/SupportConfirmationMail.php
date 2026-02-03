<?php

namespace App\Mail;

use App\Models\SupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public SupportRequest $support;

    public function __construct(SupportRequest $support)
    {
        $this->support = $support;
    }

    public function build()
    {
        return $this
            ->subject('We Received Your Support Request')
            ->view('emails.applications.support-confirmation');
    }
}
