@extends('layouts.app')
@section('title','Appointment calendar')
@section('content')
<form method="get" class="mb-3"><label class="me-2">Month</label><input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm d-inline-block" style="width:auto"></form>
<div class="card border-0 shadow-sm"><div class="card-body small">
@php $byDay = $items->groupBy(fn($i) => $i->appointment_date->format('Y-m-d')); @endphp
@foreach($byDay as $day => $list)
    <div class="fw-600 mb-1">{{ \Carbon\Carbon::parse($day)->format('D, M d') }}</div>
    <ul>@foreach($list as $i)<li>{{ $i->appointment_time }} — {{ $i->resident->full_name }} ({{ $i->status }})</li>@endforeach</ul>
@endforeach
</div></div>
@endsection
