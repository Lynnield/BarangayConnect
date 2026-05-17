@extends('layouts.app')
@section('title', $role->name)
@section('content')
<h1 class="h4">{{ $role->name }}</h1>
<p class="text-muted"><code>{{ $role->slug }}</code></p>
<p>{{ $role->description }}</p>
<h2 class="h6">Permissions</h2>
<ul>@foreach($role->permissions as $p)<li>{{ $p->name }}</li>@endforeach</ul>
<a href="{{ route('admin.roles.edit',$role) }}" class="btn btn-sm btn-primary">Edit</a>
@endsection
