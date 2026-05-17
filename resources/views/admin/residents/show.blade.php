@extends('layouts.app')
@section('title', $resident->full_name)
@section('content')
<h1 class="h4">{{ $resident->full_name }} <small class="text-muted">{{ $resident->resident_number }}</small></h1>
<p class="small">{{ $resident->address }}</p>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm"><div class="card-header bg-white fw-600">Profile</div>
        <div class="card-body small"><p>Gender: {{ $resident->gender }} · Civil: {{ $resident->civil_status }}</p>
        <p>Birthdate: {{ $resident->birthdate->format('M d, Y') }}</p>
        <p>Contact: {{ $resident->contact_number }} · {{ $resident->email }}</p></div></div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm"><div class="card-header bg-white fw-600">Linked user</div>
        <div class="card-body small">@if($resident->user) {{ $resident->user->email }} @else <span class="text-muted">No account</span> @endif</div></div>
    </div>
</div>
<div class="card border-0 shadow-sm mt-3"><div class="card-header bg-white fw-600">Requests</div>
<table class="table table-sm mb-0"><thead><tr><th>#</th><th>Status</th></tr></thead><tbody>
@foreach($resident->documentRequests as $d)<tr><td><a href="{{ route('admin.requests.show',$d) }}">{{ $d->request_number }}</a></td><td>{{ $d->status }}</td></tr>@endforeach
</tbody></table></div>
<a href="{{ route('admin.residents.edit',$resident) }}" class="btn btn-sm btn-primary mt-2">Edit</a>
@endsection
