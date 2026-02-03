@extends('emails.layout')

@section('title', 'Digital Support Request Received')

@section('content')
<h2>Your Support Request Has Been Received</h2>

<p>Dear {{ $support->contact_person }},</p>

<p>
Thank you for submitting a digital support request to
<strong>Piko Digital Impact</strong>.
</p>

<p>
Our team will assess your request and contact you regarding the next steps.
</p>

<ul>
    <li><strong>Organization:</strong> {{ $support->organization_name }}</li>
    <li><strong>Country:</strong> {{ $support->country }}</li>
</ul>

<p>
We are committed to empowering organizations through technology for social good.
</p>

<p class="footer">
Kind regards,<br>
<strong>Piko Digital Impact Team</strong>
</p>
@endsection
