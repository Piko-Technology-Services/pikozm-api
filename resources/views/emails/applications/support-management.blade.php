@extends('emails.layout')

@section('title', 'New Digital Support Request')

@section('content')
<h2>New Digital Support Request</h2>

<ul>
    <li><strong>Organization:</strong> {{ $support->organization_name }}</li>
    <li><strong>Contact Person:</strong> {{ $support->contact_person }}</li>
    <li><strong>Email:</strong> {{ $support->email }}</li>
    <li><strong>Phone:</strong> {{ $support->phone }}</li>
    <li><strong>Country:</strong> {{ $support->country }}</li>
    <li><strong>Support Needed:</strong> {{ $support->support_needs }}</li>
</ul>

<a href="{{ url('/dashboard') }}" class="button">View in Dashboard</a>
@endsection
