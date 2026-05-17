@extends('layouts.app')

@section('title', 'Edit Document Type')

@section('breadcrumb')
    <a href="{{ route('admin.document-types.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Document Types</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Edit Type</span>
@endsection

@section('content')
<div class="w-full max-w-4xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Edit Document Type</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Update the settings, fee, and availability for this document service.</p>
        </div>
        <x-button href="{{ route('admin.document-types.index') }}" variant="secondary" size="md" icon="arrow-left">
            Back to List
        </x-button>
    </div>

    <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
        <form method="POST" action="{{ route('admin.document-types.update', $documentType) }}" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                        <i data-lucide="file-text" class="h-4 w-4"></i>
                    </div>
                    <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Document Details</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Document Name</label>
                        <input type="text" name="name" value="{{ old('name', $documentType->name) }}" required
                            class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        @error('name') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">System Slug (Optional)</label>
                        <input type="text" name="slug" value="{{ old('slug', $documentType->slug) }}"
                            class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                            placeholder="auto-generated from name">
                        @error('slug') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Description</label>
                        <textarea name="description" rows="3"
                            class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-none"
                            placeholder="Briefly describe this document type...">{{ old('description', $documentType->description) }}</textarea>
                        @error('description') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="space-y-6 pt-8 border-t border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                        <i data-lucide="banknote" class="h-4 w-4"></i>
                    </div>
                    <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Logistics & Pricing</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Processing Fee (PHP)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors font-bold text-xs">₱</div>
                            <input type="number" step="0.01" name="fee" value="{{ old('fee', $documentType->fee) }}" required
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 pl-8 pr-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>
                        @error('fee') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Turnaround (Days)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-500 group-focus-within:text-indigo-500 transition-colors">
                                <i data-lucide="clock" class="h-4 w-4"></i>
                            </div>
                            <input type="number" name="processing_days" value="{{ old('processing_days', $documentType->processing_days) }}" required
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 pl-11 pr-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>
                        @error('processing_days') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Availability Status</label>
                        <div class="flex items-center gap-3 px-1 py-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $documentType->is_active))>
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-[10px] font-black text-slate-400 uppercase tracking-widest peer-checked:text-indigo-400 transition-colors">Active</span>
                            </label>
                        </div>
                        @error('is_active') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-8 border-t border-slate-800">
                <x-button type="button" variant="ghost" size="md" onclick="window.location.href='{{ route('admin.document-types.index') }}'">
                    Cancel
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="check" class="shadow-indigo-600/20">
                    Save Changes
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
