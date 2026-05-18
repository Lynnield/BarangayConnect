@extends('layouts.app')

@section('title','Edit resident')

@section('breadcrumb')
    <a href="{{ route('admin.residents.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Residents</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <a href="{{ route('admin.residents.show', $resident) }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Profile</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Edit Record</span>
@endsection

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-8 animate-in fade-in duration-700">
    <x-card class="bg-slate-900/50 border-slate-800">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between p-6">
            <div>
                <h1 class="text-2xl font-black text-white">Edit Resident</h1>
                <p class="mt-2 text-sm text-slate-400">Update profile details for {{ $resident->full_name }}.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <x-button href="{{ route('admin.residents.show', $resident) }}" variant="secondary" size="md" icon="chevron-left">
                    Back to Profile
                </x-button>
            </div>
        </div>
    </x-card>

    <x-card class="bg-slate-900/50 border-slate-800">
        <form method="POST" action="{{ route('admin.residents.update',$resident) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">First name</label>
                    <input name="first_name" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('first_name',$resident->first_name) }}" required>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Middle name</label>
                    <input name="middle_name" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('middle_name',$resident->middle_name) }}">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Last name</label>
                    <input name="last_name" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('last_name',$resident->last_name) }}" required>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Suffix</label>
                    <input name="suffix" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('suffix',$resident->suffix) }}">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Gender</label>
                    <select name="gender" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10">
                        @foreach(['male','female','other'] as $g)
                            <option value="{{ $g }}" @selected(old('gender',$resident->gender) === $g)>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Birthdate</label>
                    <input type="date" name="birthdate" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('birthdate',$resident->birthdate?->format('Y-m-d')) }}" required>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Civil status</label>
                    <select name="civil_status" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10">
                        @foreach(['single','married','widowed','separated','divorced'] as $c)
                            <option value="{{ $c }}" @selected(old('civil_status',$resident->civil_status) === $c)>{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">House number</label>
                    <input name="house_number" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('house_number',$resident->house_number) }}">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Street</label>
                    <input name="street" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('street',$resident->street) }}">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Purok</label>
                    <input name="purok" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('purok',$resident->purok) }}">
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Barangay</label>
                    <input name="barangay" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('barangay',$resident->barangay) }}">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">City</label>
                    <input name="city" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('city',$resident->city) }}">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Province</label>
                    <input name="province" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('province',$resident->province) }}">
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Postal code</label>
                    <input name="postal_code" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('postal_code',$resident->postal_code) }}">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Contact number</label>
                    <input name="contact_number" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('contact_number',$resident->contact_number) }}">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Email</label>
                    <input name="email" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('email',$resident->email) }}">
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Occupation</label>
                    <input name="occupation" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('occupation',$resident->occupation) }}">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Valid ID type</label>
                    <select name="valid_id_type" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10">
                        <option value="">Select ID type</option>
                        @foreach(\App\Models\Resident::VALID_ID_TYPES as $type)
                            <option value="{{ $type }}" @selected(old('valid_id_type', $resident->valid_id_type) === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Valid ID number</label>
                    <input name="valid_id_number" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10" value="{{ old('valid_id_number',$resident->valid_id_number) }}">
                </div>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Address</label>
                <textarea name="address" rows="4" class="w-full rounded-3xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10">{{ old('address',$resident->address) }}</textarea>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <x-button type="submit" variant="primary" size="md" icon="save">
                    Save Changes
                </x-button>
                <x-button href="{{ route('admin.residents.show', $resident) }}" variant="secondary" size="md">
                    Cancel
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
