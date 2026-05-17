@extends('layouts.app')
@section('title', $documentType->name)
@section('content')
<h1 class="h4">{{ $documentType->name }}</h1>
<p>{{ $documentType->description }}</p>
<p>Fee: ₱{{ number_format($documentType->fee,2) }} · {{ $documentType->processing_days }} day(s) processing</p>
<a href="{{ route('admin.document-types.edit',$documentType) }}" class="btn btn-sm btn-primary">Edit</a>
@endsection
