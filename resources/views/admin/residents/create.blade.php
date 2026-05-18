@extends('layouts.app')
@section('title','Add resident')
@section('content')
<h1 class="h4 mb-3">New resident record</h1>
<form method="POST" action="{{ route('admin.residents.store') }}" class="card border-0 shadow-sm p-4">@csrf
<div class="row g-3">
    <div class="col-md-3"><label class="form-label">First name</label><input name="first_name" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Middle name</label><input name="middle_name" class="form-control"></div>
    <div class="col-md-3"><label class="form-label">Last name</label><input name="last_name" class="form-control" required></div>
    <div class="col-md-2"><label class="form-label">Suffix</label><input name="suffix" class="form-control"></div>
    <div class="col-md-3"><label class="form-label">Gender</label>
        <select name="gender" class="form-select"><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
    <div class="col-md-3"><label class="form-label">Birthdate</label><input type="date" name="birthdate" class="form-control" required></div>
    <div class="col-md-4"><label class="form-label">Civil status</label>
        <select name="civil_status" class="form-select">
            @foreach(['single','married','widowed','separated','divorced'] as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-3"><label class="form-label">House no.</label><input name="house_number" class="form-control"></div>
    <div class="col-md-3"><label class="form-label">Street</label><input name="street" class="form-control"></div>
    <div class="col-md-3"><label class="form-label">Purok</label><input name="purok" class="form-control"></div>
    <div class="col-md-3"><label class="form-label">Barangay</label><input name="barangay" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">City</label><input name="city" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Province</label><input name="province" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Postal code</label><input name="postal_code" class="form-control"></div>
    <div class="col-12"><label class="form-label">Address fallback</label><textarea name="address" class="form-control"></textarea></div>
    <div class="col-md-4"><label class="form-label">Contact</label><input name="contact_number" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Occupation</label><input name="occupation" class="form-control"></div>
    <div class="col-md-6">
        <label class="form-label">Valid ID type</label>
        <select name="valid_id_type" class="form-select">
            <option value="">Select ID type</option>
            @foreach(\App\Models\Resident::VALID_ID_TYPES as $type)
                <option value="{{ $type }}" @selected(old('valid_id_type') === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6"><label class="form-label">Valid ID number</label><input name="valid_id_number" class="form-control"></div>
</div>
<button class="btn btn-primary mt-3">Save</button>
</form>
@endsection
