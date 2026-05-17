@extends('layouts.app')
@section('title', $appointment->appointment_number)
@section('content')
<h1 class="h4">{{ $appointment->appointment_number }}</h1>
<p>{{ $appointment->appointment_date->format('l, M d, Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
<p>Resident: {{ $appointment->resident->full_name }}</p>
<p>Status: {{ $appointment->status }}</p>
<a href="{{ route('admin.appointments.edit',$appointment) }}" class="btn btn-sm btn-primary">Edit</a>
@endsection
