@extends('emails.layout')

@section('title', 'New Partnership Application')

@section('content')
<h2>New Partnership Application</h2>

<p>A new partnership request has been submitted.</p>

<ul>
    <li><strong>Name:</strong> {{ $partner->name }}</li>
    <li><strong>Email:</strong> {{ $partner->email }}</li>
    <li><strong>Phone:</strong> {{ $partner->phone }}</li>
    <li><strong>Country:</strong> {{ $partner->country }}</li>
    <li><strong>Partnership Type:</strong> {{ $partner->partnership_type }}</li>
    <li><strong>Organization Type:</strong> {{ $partner->organization_type }}</li>
</ul>

<a href="{{ url('/dashboard') }}" class="button">View in Dashboard</a>
@endsection
