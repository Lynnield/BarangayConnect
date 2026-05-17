@extends('layouts.guest')
@section('title', 'Forgot password')
@section('content')
<h1 class="h4 mb-3">Reset password</h1>
<p class="text-muted small">We will email a reset link if the account exists.</p>
<form method="POST" action="{{ route('password.email') }}" class="card shadow-sm border-0 p-4">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100">Send link</button>
</form>
@endsection
