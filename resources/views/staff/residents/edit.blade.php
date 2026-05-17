@extends('layouts.app')
@section('title','Edit resident')
@section('content')
<form method="POST" action="{{ route('staff.residents.update',$resident) }}" class="card border-0 shadow-sm p-4">@csrf @method('PUT')
    <div class="row g-2 mb-3">
        <div class="col-md-3"><label class="form-label">First name</label><input name="first_name" class="form-control" value="{{ old('first_name',$resident->first_name) }}" required></div>
        <div class="col-md-3"><label class="form-label">Middle name</label><input name="middle_name" class="form-control" value="{{ old('middle_name',$resident->middle_name) }}"></div>
        <div class="col-md-3"><label class="form-label">Last name</label><input name="last_name" class="form-control" value="{{ old('last_name',$resident->last_name) }}" required></div>
        <div class="col-md-3"><label class="form-label">Suffix</label><input name="suffix" class="form-control" value="{{ old('suffix',$resident->suffix) }}"></div>
    </div>
    <div class="row g-2 mb-3"><div class="col-4"><select name="gender" class="form-select">@foreach(['male','female','other'] as $g)<option value="{{ $g }}" @selected($resident->gender==$g)>{{ $g }}</option>@endforeach</select></div>
    <div class="col-4"><input type="date" name="birthdate" class="form-control" value="{{ $resident->birthdate->format('Y-m-d') }}" required></div>
    <div class="col-4"><select name="civil_status" class="form-select">@foreach(['single','married','widowed','separated','divorced'] as $c)<option value="{{ $c }}" @selected($resident->civil_status==$c)>{{ $c }}</option>@endforeach</select></div></div>
    <div class="row g-2 mb-3">
        <div class="col-md-3"><input name="house_number" class="form-control" value="{{ old('house_number',$resident->house_number) }}" placeholder="House no."></div>
        <div class="col-md-3"><input name="street" class="form-control" value="{{ old('street',$resident->street) }}" placeholder="Street"></div>
        <div class="col-md-3"><input name="purok" class="form-control" value="{{ old('purok',$resident->purok) }}" placeholder="Purok"></div>
        <div class="col-md-3"><input name="barangay" class="form-control" value="{{ old('barangay',$resident->barangay) }}" placeholder="Barangay"></div>
        <div class="col-md-4"><input name="city" class="form-control" value="{{ old('city',$resident->city) }}" placeholder="City"></div>
        <div class="col-md-4"><input name="province" class="form-control" value="{{ old('province',$resident->province) }}" placeholder="Province"></div>
        <div class="col-md-4"><input name="postal_code" class="form-control" value="{{ old('postal_code',$resident->postal_code) }}" placeholder="Postal code"></div>
    </div>
    <div class="mb-3"><textarea name="address" class="form-control">{{ $resident->address }}</textarea></div>
    <div class="row g-2 mb-3"><div class="col-6"><input name="contact_number" class="form-control" value="{{ $resident->contact_number }}"></div>
    <div class="col-6"><input name="email" class="form-control" value="{{ $resident->email }}"></div></div>
    <div class="mb-3"><input name="occupation" class="form-control" value="{{ $resident->occupation }}"></div>
    <button class="btn btn-primary">Save</button>
</form>
@endsection
