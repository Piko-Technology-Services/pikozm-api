@extends('emails.layout')

@section('title', 'New Volunteer Application')

@section('content')
<h2>New Volunteer Application</h2>

<ul>
    <li><strong>Name:</strong> {{ $volunteer->name }}</li>
    <li><strong>Email:</strong> {{ $volunteer->email }}</li>
    <li><strong>Phone:</strong> {{ $volunteer->phone }}</li>
    <li><strong>Country:</strong> {{ $volunteer->country }}</li>
    <li><strong>Skills:</strong> {{ $volunteer->skills }}</li>
    <li><strong>Availability:</strong> {{ $volunteer->availability }}</li>
</ul>

<a href="{{ url('/dashboard') }}" class="button">View in Dashboard</a>
@endsection
