@extends('layouts.app')
@section('title','Edit document type')
@section('content')
<h1 class="h4 mb-3">{{ $documentType->name }}</h1>
<form method="POST" action="{{ route('admin.document-types.update',$documentType) }}" class="card border-0 shadow-sm p-4">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name',$documentType->name) }}" required></div>
    <div class="mb-3"><label class="form-label">Slug</label><input name="slug" class="form-control" value="{{ old('slug',$documentType->slug) }}"></div>
    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ old('description',$documentType->description) }}</textarea></div>
    <div class="row g-2 mb-3"><div class="col-4"><label class="form-label">Fee</label><input type="number" step="0.01" name="fee" class="form-control" value="{{ old('fee',$documentType->fee) }}" required></div>
    <div class="col-4"><label class="form-label">Processing days</label><input type="number" name="processing_days" class="form-control" value="{{ old('processing_days',$documentType->processing_days) }}" required></div>
    <div class="col-4"><label class="form-label d-block">Active</label><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active',$documentType->is_active))></div></div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
