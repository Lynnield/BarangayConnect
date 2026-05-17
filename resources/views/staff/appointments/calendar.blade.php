@extends('layouts.app')
@section('title','Calendar')
@section('content')
<form method="get" class="mb-3"><input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm d-inline-block" style="width:auto"></form>
@php $byDay = $items->groupBy(fn($i) => $i->appointment_date->format('Y-m-d')); @endphp
@foreach($byDay as $day => $list)
    <div class="fw-600">{{ \Carbon\Carbon::parse($day)->format('D M d') }}</div>
    <ul>@foreach($list as $i)<li>{{ $i->appointment_time }} — {{ $i->resident->full_name }}</li>@endforeach</ul>
@endforeach
@endsection
