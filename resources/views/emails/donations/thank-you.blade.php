<p>Dear {{ $donation->name }},</p>

<p>
Thank you for your generous donation of
<strong>{{ $donation->currency }} {{ number_format($donation->amount) }}</strong>.
</p>

<p>
Your support towards <strong>{{ $donation->focus_area }}</strong>
is making a real difference.
</p>

@if($donation->message)
<p><em>Your message:</em> “{{ $donation->message }}”</p>
@endif

<p>
Reference: {{ $donation->reference }}
</p>

<p>
Warm regards,<br>
<strong>Piko Digital Impact Team</strong>
</p>
