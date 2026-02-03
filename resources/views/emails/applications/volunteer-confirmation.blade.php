@extends('emails.layout')

@section('title', 'Volunteer Application Received')

@section('content')
<h2>Thank You for Volunteering!</h2>

<p>Dear {{ $volunteer->name }},</p>

<p>
Thank you for applying to be part of the <strong>Piko Digital Impact</strong>
volunteer team.
</p>

<p>
We have received your application and our team will review it carefully.
If shortlisted, we will contact you via email.
</p>

<ul>
    <li><strong>Skills:</strong> {{ $volunteer->skills }}</li>
    <li><strong>Availability:</strong> {{ ucfirst($volunteer->availability) }}</li>
</ul>

<p>
We truly appreciate your willingness to contribute to meaningful digital impact.
</p>

<p class="footer">
With gratitude,<br>
<strong>Piko Digital Impact Team</strong>
</p>
@endsection
