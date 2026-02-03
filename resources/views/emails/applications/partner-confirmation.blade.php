@extends('emails.layout')

@section('title', 'Thank You for Partnering with Piko Digital Impact')

@section('content')
<h2>Thank You for Your Partnership Interest</h2>

<p>Dear {{ $partner->contact_person }},</p>

<p>
Thank you for reaching out to partner with <strong>Piko Digital Impact</strong>.
We have successfully received your partnership application.
</p>

<p>
Our team will review your submission and contact you shortly to explore
collaboration opportunities.
</p>

<ul>
    <li><strong>Name:</strong> {{ $partner->name }}</li>
    <li><strong>Contact Person:</strong> {{ $partner->contact_person }}</li>
    <li><strong>Partnership Type:</strong> {{ $partner->partnership_type }}</li>
    <li><strong>Organization Type:</strong> {{ $partner->organization_type }}</li>
    <li><strong>Country:</strong> {{ $partner->country }}</li>
</ul>

<p>
We appreciate your interest in driving digital transformation for social good.
</p>

<p class="footer">
Warm regards,<br>
<strong>Piko Digital Impact Team</strong>
</p>
@endsection
