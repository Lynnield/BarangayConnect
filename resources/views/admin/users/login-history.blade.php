@extends('layouts.app')
@section('title', 'Login history')
@section('content')
<h1 class="h4 mb-3">{{ $user->name }} — login history</h1>
<div class="table-card">
    <table class="table table-sm mb-0"><thead><tr><th>When</th><th>IP</th><th>Device</th><th>Result</th></tr></thead>
    <tbody>
        @foreach($histories as $h)
            <tr>
                <td>{{ $h->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $h->ip_address }}</td>
                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($h->device_info,40) }}</td>
                <td>{{ $h->success ? 'Success' : ($h->failure_reason ?? '') }}</td>
            </tr>
        @endforeach
    </tbody></table>
</div>
<div class="mt-2">{{ $histories->links() }}</div>
@endsection
