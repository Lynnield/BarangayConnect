@extends('layouts.app')
@section('title','Notifications')
@section('content')
<div class="page-header"><h1 class="h4">Notifications</h1>
<form method="POST" action="{{ route('resident.notifications.read-all') }}">@csrf<button class="btn btn-sm btn-outline-secondary">Mark all read</button></form></div>
<ul class="list-group shadow-sm">
@foreach($notifications as $n)
<li class="list-group-item @if(!$n->read_at) bg-light @endif">
    <div class="small text-muted">{{ $n->created_at->diffForHumans() }}</div>
    @php $data = $n->data; @endphp
    <div class="fw-600">{{ $data['title'] ?? class_basename($n->type) }}</div>
    <div class="small">{{ $data['message'] ?? json_encode($data) }}</div>
    @if(!$n->read_at)<form method="POST" action="{{ route('resident.notifications.read',$n) }}" class="d-inline">@csrf<button class="btn btn-sm btn-link p-0">Mark read</button></form>@endif
    <form method="POST" action="{{ route('resident.notifications.destroy',$n) }}" class="d-inline float-end" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger p-0">Remove</button></form>
</li>
@endforeach
</ul>
{{ $notifications->links() }}
@endsection
