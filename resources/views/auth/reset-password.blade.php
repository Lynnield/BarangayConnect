@extends('layouts.guest')
@section('title', 'New password')
@section('content')
<h1 class="h4 mb-3">Set new password</h1>
<form method="POST" action="{{ route('password.update') }}" class="card shadow-sm border-0 p-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', request('email')) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">New password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirm password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100">Update password</button>
</form>
@endsection
