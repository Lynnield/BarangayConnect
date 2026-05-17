@extends('layouts.app')
@section('title','Appointment slots')
@section('content')
<div class="row g-4"><div class="col-lg-5">
<h2 class="h6">Add slot</h2>
<form method="POST" action="{{ route('admin.appointments.slots.store') }}" class="card border-0 shadow-sm p-3">@csrf
    <div class="mb-2"><label class="form-label small">Date</label><input type="date" name="slot_date" class="form-control" required></div>
    <div class="mb-2"><label class="form-label small">Time</label><input type="time" name="slot_time" class="form-control" required></div>
    <div class="mb-2"><label class="form-label small">Max appointments</label><input type="number" name="max_appointments" class="form-control" value="5" min="1"></div>
    <button class="btn btn-primary btn-sm w-100">Save slot</button>
</form>
</div>
<div class="col-lg-7">
<h2 class="h6">Existing slots</h2>
<div class="table-card"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Time</th><th>Max</th></tr></thead><tbody>
@foreach($slots as $s)<tr><td>{{ $s->slot_date->format('M d, Y') }}</td><td>{{ \Carbon\Carbon::parse($s->slot_time)->format('H:i') }}</td><td>{{ $s->max_appointments }}</td></tr>@endforeach
</tbody></table></div>
{{ $slots->links() }}
</div></div>
@endsection
