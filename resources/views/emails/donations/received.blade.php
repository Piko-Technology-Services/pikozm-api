<p>A new donation has been received.</p>

<ul>
    <li><strong>Donor:</strong> {{ $donation->name }} ({{ $donation->email }})</li>
    <li><strong>Amount:</strong> {{ $donation->currency }} {{ number_format($donation->amount) }}</li>
    <li><strong>Focus Area:</strong> {{ $donation->focus_area }}</li>
    <li><strong>Reference:</strong> {{ $donation->reference }}</li>
</ul>

<p>
Login to the dashboard for more details.
</p>
