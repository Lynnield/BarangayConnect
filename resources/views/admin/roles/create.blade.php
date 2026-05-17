@extends('layouts.app')
@section('title','New role')
@section('content')
<h1 class="h4 mb-3">New role</h1>
<form method="POST" action="{{ route('admin.roles.store') }}" class="card border-0 shadow-sm p-4">@csrf
    <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Slug</label><input name="slug" class="form-control" required placeholder="staff"></div>
    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
    <button class="btn btn-primary">Save</button>
</form>
@endsection
