<?php

namespace App\Mail;

use App\Models\SupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSupportRequestMail extends Mailable
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
            ->subject('New Digital Support Request Received')
            ->view('emails.applications.support-management');
    }
}
