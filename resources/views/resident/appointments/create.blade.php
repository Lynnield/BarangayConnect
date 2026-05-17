@extends('layouts.app')
@section('title','Book appointment')
@section('content')
<h1 class="h4 mb-3">Pick a slot</h1>
@if(isset($documentRequest))
<p class="small text-muted">For request <strong>{{ $documentRequest->request_number }}</strong></p>
@endif
<form id="apptform" method="POST" action="{{ route('resident.appointments.store') }}" class="card border-0 shadow-sm p-4">@csrf
@if(isset($documentRequest))<input type="hidden" name="document_request_id" value="{{ $documentRequest->id }}">@endif
    <div class="mb-3"><label class="form-label">Date</label><input type="date" name="slot_date" id="slotDate" class="form-control" min="{{ today()->format('Y-m-d') }}" required></div>
    <div class="mb-3"><label class="form-label">Time</label><select name="slot_time" id="slotTime" class="form-select" required><option value="">Choose date first</option></select></div>
    <button class="btn btn-primary">Confirm</button>
</form>
@push('scripts')
<script>
document.getElementById('slotDate')?.addEventListener('change', function() {
    const sel = document.getElementById('slotTime');
    sel.innerHTML = '<option>Loading…</option>';
    fetch(`{{ route('resident.appointment-slots') }}?date=${this.value}`)
        .then(r => r.json()).then(rows => {
            sel.innerHTML = '<option value="">Select time</option>';
            rows.forEach(row => {
                if (!row.available) return;
                const o = document.createElement('option');
                o.value = row.time;
                o.textContent = row.time;
                sel.appendChild(o);
            });
        });
});
</script>
@endpush
@endsection
