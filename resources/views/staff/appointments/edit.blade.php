@extends('layouts.app')
@section('title','Edit appointment')
@section('content')
<h1 class="h4 mb-3">{{ $appointment->appointment_number }}</h1>
<form method="POST" action="{{ route('staff.appointments.update',$appointment) }}" class="card border-0 shadow-sm p-4">@csrf @method('PUT')
    <div class="mb-3"><select name="resident_id" class="form-select" required>@foreach($residents as $r)<option value="{{ $r->id }}" @selected($appointment->resident_id==$r->id)>{{ $r->full_name }}</option>@endforeach</select></div>
    <div class="row g-2 mb-3"><div class="col-6"><input type="date" name="appointment_date" class="form-control" value="{{ $appointment->appointment_date->format('Y-m-d') }}" required></div>
    <div class="col-6"><input type="time" name="appointment_time" class="form-control" value="{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}" required></div></div>
    <div class="mb-3"><select name="status" class="form-select">@foreach(['scheduled','confirmed','rescheduled','completed','cancelled'] as $s)<option value="{{ $s }}" @selected($appointment->status==$s)>{{ $s }}</option>@endforeach</select></div>
    <div class="mb-3"><textarea name="notes" class="form-control" rows="2">{{ $appointment->notes }}</textarea></div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
