<?php

namespace App\Mail;

use App\Models\VolunteerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VolunteerConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public VolunteerApplication $volunteer;

    public function __construct(VolunteerApplication $volunteer)
    {
        $this->volunteer = $volunteer;
    }

    public function build()
    {
        return $this
            ->subject('Your Volunteer Application Was Received')
            ->view('emails.applications.volunteer-confirmation');
    }
}
