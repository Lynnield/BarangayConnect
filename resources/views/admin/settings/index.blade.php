@extends('layouts.app')

@section('title', 'System Settings')

@section('breadcrumb')
    <span class="text-slate-500">Settings</span>
@endsection

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">System Configuration</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Fine-tune platform behavior, localization, and branding assets.</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 border border-indigo-500/20">
                <i data-lucide="settings-2" class="h-6 w-6"></i>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach($health as $label => $value)
            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                <p class="text-[9px] font-black uppercase tracking-widest text-slate-500">{{ str_replace('_', ' ', $label) }}</p>
                <p class="mt-2 text-sm font-black text-white break-words">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Settings -->
        <div class="lg:col-span-2">
            <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
                <form method="POST" action="{{ route('admin.settings.update') }}" class="p-8 space-y-10">
                    @csrf
                    @foreach($settings as $group => $rows)
                        <div class="space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                                    <i data-lucide="layers" class="h-4 w-4"></i>
                                </div>
                                <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">{{ $group }}</h2>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                @foreach($rows as $s)
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">
                                            {{ str_replace('_',' ', $s->setting_key) }}
                                        </label>
                                        @if($s->description)
                                            <p class="text-[10px] text-slate-500 ml-1">{{ $s->description }}</p>
                                        @endif

                                        @if($s->setting_type === 'boolean')
                                            <select name="settings[{{ $s->setting_key }}]"
                                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                                                <option value="1" @selected((string) $s->setting_value === '1')>Enabled</option>
                                                <option value="0" @selected((string) $s->setting_value === '0')>Disabled</option>
                                            </select>
                                        @elseif($s->setting_type === 'text' || $s->setting_type === 'json')
                                            <textarea name="settings[{{ $s->setting_key }}]" rows="4"
                                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">{{ $s->setting_value }}</textarea>
                                        @else
                                            <input type="{{ $s->setting_type === 'integer' ? 'number' : ($s->setting_type === 'time' ? 'time' : 'text') }}" name="settings[{{ $s->setting_key }}]" value="{{ $s->setting_type === 'secret' && $s->setting_value ? '********' : $s->setting_value }}"
                                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="h-px bg-slate-800"></div>
                        @endif
                    @endforeach

                    <div class="pt-4 flex justify-end">
                        <x-button type="submit" variant="primary" size="md" icon="save" class="shadow-indigo-600/20">
                            Save Changes
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Branding & Assets -->
        <div class="lg:col-span-1 space-y-8">
            <x-card title="Platform Branding" icon="palette" class="bg-slate-900/50 border-slate-800">
                <form method="POST" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="h-32 w-full rounded-2xl bg-slate-800/50 border border-slate-700 border-dashed flex flex-col items-center justify-center text-slate-500 group relative overflow-hidden">
                            <i data-lucide="image-plus" class="h-8 w-8 mb-2"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Logo Upload</span>
                            <input type="file" name="logo" accept="image/*" required
                                class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                        <p class="text-[10px] text-slate-500 font-medium italic text-center">Recommended: PNG or SVG with transparent background.</p>
                    </div>

                    <x-button type="submit" variant="secondary" size="sm" icon="upload" class="w-full">
                        Upload Asset
                    </x-button>
                </form>
            </x-card>

            <x-card class="bg-indigo-600/5 border-indigo-500/20">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 shrink-0">
                        <i data-lucide="help-circle" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest">Need Help?</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">System settings affect global application behavior. Ensure values are correct before saving.</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
