@extends('layouts.app')
@section('title', $appointment->appointment_number)
@section('content')
<h1 class="h4">{{ $appointment->appointment_number }}</h1>
<p>{{ $appointment->appointment_date->format('l, M d, Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
<p>Status: {{ $appointment->status }}</p>
@if($appointment->status !== 'cancelled')
<form method="POST" action="{{ route('resident.appointments.cancel',$appointment) }}" class="d-inline" onsubmit="return confirm('Cancel appointment?')">@csrf<button class="btn btn-sm btn-outline-danger">Cancel</button></form>
<form method="POST" action="{{ route('resident.appointments.reschedule',$appointment) }}" class="row g-2 mt-3">@csrf
    <div class="col-auto"><input type="date" name="slot_date" class="form-control form-control-sm" required></div>
    <div class="col-auto"><input type="time" name="slot_time" class="form-control form-control-sm" required></div>
    <div class="col-auto"><button class="btn btn-sm btn-outline-secondary">Reschedule</button></div>
</form>
@endif
@endsection
