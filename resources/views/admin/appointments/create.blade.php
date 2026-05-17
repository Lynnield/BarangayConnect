@extends('layouts.app')
@section('title','New appointment')
@section('content')
<h1 class="h4 mb-3">Schedule appointment</h1>
<form method="POST" action="{{ route('admin.appointments.store') }}" class="card border-0 shadow-sm p-4">@csrf
    <div class="mb-3"><label class="form-label">Resident</label>
        <select name="resident_id" class="form-select" required>@foreach($residents as $res)<option value="{{ $res->id }}">{{ $res->full_name }}</option>@endforeach</select></div>
    <div class="row g-2 mb-3"><div class="col-6"><label class="form-label">Date</label><input type="date" name="appointment_date" class="form-control" required></div>
    <div class="col-6"><label class="form-label">Time</label><input type="time" name="appointment_time" class="form-control" required></div></div>
    <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['scheduled','confirmed'] as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach</select></div>
    <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
    <input type="hidden" name="document_request_id" value="">
    <button class="btn btn-primary">Save</button>
</form>
@endsection
