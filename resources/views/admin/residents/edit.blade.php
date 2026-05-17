@extends('layouts.app')
@section('title','Edit resident')
@section('content')
<h1 class="h4 mb-3">Edit {{ $resident->full_name }}</h1>
<form method="POST" action="{{ route('admin.residents.update',$resident) }}" class="card border-0 shadow-sm p-4">@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-3"><label class="form-label">First name</label><input name="first_name" class="form-control" value="{{ old('first_name',$resident->first_name) }}" required></div>
    <div class="col-md-3"><label class="form-label">Middle name</label><input name="middle_name" class="form-control" value="{{ old('middle_name',$resident->middle_name) }}"></div>
    <div class="col-md-3"><label class="form-label">Last name</label><input name="last_name" class="form-control" value="{{ old('last_name',$resident->last_name) }}" required></div>
    <div class="col-md-2"><label class="form-label">Suffix</label><input name="suffix" class="form-control" value="{{ old('suffix',$resident->suffix) }}"></div>
    <div class="col-md-3"><label class="form-label">Gender</label>
        <select name="gender" class="form-select">@foreach(['male','female','other'] as $g)<option value="{{ $g }}" @selected($resident->gender==$g)>{{ $g }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Birthdate</label><input type="date" name="birthdate" class="form-control" value="{{ old('birthdate',$resident->birthdate->format('Y-m-d')) }}" required></div>
    <div class="col-md-4"><label class="form-label">Civil status</label>
        <select name="civil_status" class="form-select">@foreach(['single','married','widowed','separated','divorced'] as $c)<option value="{{ $c }}" @selected($resident->civil_status==$c)>{{ $c }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">House no.</label><input name="house_number" class="form-control" value="{{ old('house_number',$resident->house_number) }}"></div>
    <div class="col-md-3"><label class="form-label">Street</label><input name="street" class="form-control" value="{{ old('street',$resident->street) }}"></div>
    <div class="col-md-3"><label class="form-label">Purok</label><input name="purok" class="form-control" value="{{ old('purok',$resident->purok) }}"></div>
    <div class="col-md-3"><label class="form-label">Barangay</label><input name="barangay" class="form-control" value="{{ old('barangay',$resident->barangay) }}"></div>
    <div class="col-md-4"><label class="form-label">City</label><input name="city" class="form-control" value="{{ old('city',$resident->city) }}"></div>
    <div class="col-md-4"><label class="form-label">Province</label><input name="province" class="form-control" value="{{ old('province',$resident->province) }}"></div>
    <div class="col-md-4"><label class="form-label">Postal code</label><input name="postal_code" class="form-control" value="{{ old('postal_code',$resident->postal_code) }}"></div>
    <div class="col-12"><label class="form-label">Address fallback</label><textarea name="address" class="form-control">{{ old('address',$resident->address) }}</textarea></div>
    <div class="col-md-4"><label class="form-label">Contact</label><input name="contact_number" class="form-control" value="{{ old('contact_number',$resident->contact_number) }}"></div>
    <div class="col-md-4"><label class="form-label">Email</label><input name="email" class="form-control" value="{{ old('email',$resident->email) }}"></div>
    <div class="col-md-4"><label class="form-label">Occupation</label><input name="occupation" class="form-control" value="{{ old('occupation',$resident->occupation) }}"></div>
    <div class="col-md-6"><label class="form-label">Valid ID type</label><input name="valid_id_type" class="form-control" value="{{ old('valid_id_type',$resident->valid_id_type) }}"></div>
    <div class="col-md-6"><label class="form-label">Valid ID number</label><input name="valid_id_number" class="form-control" value="{{ old('valid_id_number',$resident->valid_id_number) }}"></div>
</div>
<button class="btn btn-primary mt-3">Update</button>
</form>
@endsection
