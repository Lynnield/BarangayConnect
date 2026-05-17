@extends('layouts.app')
@section('title','Edit role')
@section('content')
<h1 class="h4 mb-3">Edit {{ $role->name }}</h1>
<form method="POST" action="{{ route('admin.roles.update',$role) }}" class="card border-0 shadow-sm p-4 mb-4">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name',$role->name) }}" required></div>
    <div class="mb-3"><label class="form-label">Slug</label><input name="slug" class="form-control" value="{{ old('slug',$role->slug) }}" required></div>
    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ old('description',$role->description) }}</textarea></div>
    <button class="btn btn-primary">Update role</button>
</form>
<h2 class="h6">Permissions</h2>
<form method="POST" action="{{ route('admin.roles.permissions',$role) }}">@csrf
    <div class="border rounded p-3 mb-2" style="max-height:360px;overflow:auto;">
        @foreach($permissions as $module => $perms)
            <div class="fw-600 small text-muted mb-1">{{ $module }}</div>
            <div class="row g-1 mb-3">
            @foreach($perms as $p)
                <div class="col-md-4"><label class="small d-flex gap-2"><input type="checkbox" name="permission_ids[]" value="{{ $p->id }}" @checked($role->permissions->contains($p->id))> {{ $p->name }}</label></div>
            @endforeach
            </div>
        @endforeach
    </div>
    <button class="btn btn-secondary btn-sm">Save permissions</button>
</form>
@endsection
