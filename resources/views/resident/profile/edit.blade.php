@extends('layouts.app')

@section('title', 'Edit Profile')

@section('breadcrumb')
    <a href="{{ route('resident.profile.show') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Profile</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Update Records</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Update Profile</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Keep your account and community records accurate and up-to-date.</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 border border-indigo-500/20">
                <i data-lucide="user-cog" class="h-6 w-6"></i>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form Column -->
        <div class="lg:col-span-2 space-y-8">
            <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
                <form method="POST" action="{{ route('resident.profile.update') }}" class="p-8 space-y-10">
                    @csrf
                    @method('PUT')
                    
                    <!-- Account Settings -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                                <i data-lucide="settings" class="h-4 w-4"></i>
                            </div>
                            <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Account Preferences</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Display Name</label>
                                <input name="name" type="text" value="{{ old('name', $user->name) }}" required
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Primary Phone</label>
                                <input name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                    placeholder="+63 900 000 0000">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Mailing Address</label>
                                <textarea name="address" rows="2"
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-none">{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-slate-800"></div>

                    <!-- Resident Record -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                <i data-lucide="contact-2" class="h-4 w-4"></i>
                            </div>
                            <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Community Record</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Full Legal Name</label>
                                <input name="full_name" type="text" value="{{ old('full_name', $resident?->full_name) }}" required
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Gender Identity</label>
                                <div class="relative">
                                    <select name="gender" required
                                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                        @foreach(['male', 'female', 'other'] as $g)
                                            <option value="{{ $g }}" @selected(old('gender', $resident?->gender) == $g)>{{ ucfirst($g) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                                        <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Date of Birth</label>
                                <input type="date" name="birthdate" value="{{ old('birthdate', $resident?->birthdate?->format('Y-m-d')) }}" required
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Civil Status</label>
                                <div class="relative">
                                    <select name="civil_status" required
                                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                        @foreach(['single', 'married', 'widowed', 'separated', 'divorced'] as $c)
                                            <option value="{{ $c }}" @selected(old('civil_status', $resident?->civil_status) == $c)>{{ ucfirst($c) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                                        <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Occupation</label>
                                <input name="occupation" type="text" value="{{ old('occupation', $resident?->occupation) }}"
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Barangay Residence Address</label>
                                <textarea name="res_address" rows="2" required
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-none">{{ old('res_address', $resident?->address) }}</textarea>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Valid ID Type</label>
                                <input name="valid_id_type" type="text" value="{{ old('valid_id_type', $resident?->valid_id_type) }}"
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                    placeholder="e.g. PhilID, Passport">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">ID Serial Number</label>
                                <input name="valid_id_number" type="text" value="{{ old('valid_id_number', $resident?->valid_id_number) }}"
                                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                    placeholder="Enter serial number">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <x-button type="button" variant="ghost" size="md" onclick="window.location.href='{{ route('resident.profile.show') }}'">
                            Discard
                        </x-button>
                        <x-button type="submit" variant="primary" size="md" icon="check" class="shadow-indigo-600/20">
                            Save Changes
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Sidebar Column -->
        <div class="lg:col-span-1 space-y-8">
            <x-card title="Profile Photography" icon="camera" class="bg-slate-900/50 border-slate-800 text-center">
                <div class="mx-auto mb-6 h-32 w-32 relative group">
                    <div class="absolute inset-0 rounded-full bg-indigo-500 blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                    <img src="{{ auth()->user()->avatar_url }}" class="relative h-full w-full rounded-full border-4 border-slate-800 object-cover shadow-2xl">
                </div>
                
                <form method="POST" action="{{ route('resident.profile.avatar') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="relative group">
                        <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*" onchange="this.form.submit()">
                        <label for="avatar" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-700 bg-slate-800/50 px-4 py-3 text-xs font-black text-slate-300 shadow-sm hover:bg-slate-800 hover:text-white hover:border-indigo-500/50 transition-all cursor-pointer">
                            <i data-lucide="upload-cloud" class="h-4 w-4 text-indigo-400"></i>
                            Upload New Photo
                        </label>
                    </div>
                    <p class="text-[10px] text-slate-500 font-medium italic">High resolution JPG or PNG. Max 2MB.</p>
                </form>
            </x-card>

            <x-card class="bg-indigo-600/5 border-indigo-500/20">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 shrink-0">
                        <i data-lucide="help-circle" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest">Need Assistance?</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                            If you encounter issues updating your legal information, please visit the Barangay Hall for manual verification.
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection