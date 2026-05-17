@extends('layouts.app')

@section('title', 'New Request')

@section('breadcrumb')
<a href="{{ route('resident.requests.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Requests</a>
<i data-lucide="chevron-right" class="h-4 w-4 text-slate-700"></i>
<span class="text-slate-400 font-medium">New Request</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-500">
    <!-- Form Header -->
    <div class="bg-slate-950 border border-slate-800 rounded-3xl py-8 px-8 shadow-2xl relative overflow-hidden mb-6">
        <div class="relative z-10">
            <h1 class="text-3xl font-black text-white tracking-tight">Submit New Request</h1>
            <p class="text-slate-500 mt-2 font-medium max-w-md leading-relaxed">Fill out the official form below to request a barangay document for processing.</p>
        </div>
        <i data-lucide="file-text" class="absolute right-0 bottom-0 h-32 w-32 text-white opacity-10 -rotate-12 pointer-events-none"></i>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Main Form Column -->
        <div class="xl:col-span-2">
            <form method="POST" action="{{ route('resident.requests.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="bg-slate-950 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
                    <div class="p-8 space-y-8">
                        <!-- Document Selection -->
                        <div class="space-y-0">
                            <label class="mb-2 block text-xs font-bold tracking-wide text-slate-400 uppercase tracking-[0.2em] ml-1">Document Type</label>
                            <div class="relative group">
                                <select name="document_type_id" class="block w-full rounded-2xl border border-slate-800 bg-slate-900/50 py-4 px-6 text-sm text-white focus:border-indigo-500 focus:bg-slate-900 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none shadow-inner" required>
                                    <option value="" disabled selected>Select a document type...</option>
                                    @foreach($documentTypes as $t)
                                        <option value="{{ $t->id }}" @selected(old('document_type_id') == $t->id)>
                                            {{ $t->name }} — ₱{{ number_format($t->fee, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-slate-600">
                                    <i data-lucide="chevron-down" class="h-5 w-5"></i>
                                </div>
                            </div>
                            <p class="mt-2 text-[10px] text-slate-600 font-bold uppercase tracking-widest ml-1">Processing fees are calculated automatically upon selection.</p>
                        </div>

                        <!-- Purpose -->
                        <div class="space-y-0">
                            <label class="mb-2 block text-xs font-bold tracking-wide text-slate-400 uppercase tracking-[0.2em] ml-1">Purpose of Request</label>
                            <div class="relative group">
                                <textarea name="purpose" rows="5" 
                                    class="block w-full rounded-2xl border border-slate-800 bg-slate-900/50 p-6 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-900 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner resize-none" 
                                    placeholder="Please state why you need this document (e.g., Job Requirement, Scholarship Application, etc.)" required>{{ old('purpose') }}</textarea>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="space-y-4">
                            <label class="mb-2 block text-xs font-bold tracking-wide text-slate-400 uppercase tracking-[0.2em] ml-1">Supporting Attachments (Optional)</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-slate-800 border-dashed rounded-3xl cursor-pointer bg-slate-900/30 hover:bg-slate-900/50 hover:border-indigo-500/50 transition-all group shadow-inner">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <div class="h-12 w-12 rounded-2xl bg-slate-800 flex items-center justify-center text-slate-500 group-hover:text-indigo-400 group-hover:bg-indigo-500/10 transition-all mb-4">
                                            <i data-lucide="upload-cloud" class="h-6 w-6"></i>
                                        </div>
                                        <p class="mb-2 text-sm text-slate-400 font-bold"><span class="text-indigo-400">Click to upload</span> or drag and drop</p>
                                        <p class="text-[10px] text-slate-600 uppercase font-black tracking-[0.2em]">PDF, JPG, PNG (MAX. 5MB)</p>
                                    </div>
                                </label>
                                <input id="dropzone-file" type="file" name="attachments[]" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                            </div>
                            <div id="file-list" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4"></div>
                        </div>
                    </div>

                    <!-- Form Footer Actions -->
                    <div class="bg-slate-900/50 px-8 py-6 border-t border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3 text-slate-500">
                            <i data-lucide="shield-check" class="h-5 w-5 text-emerald-500"></i>
                            <span class="text-xs font-bold uppercase tracking-widest">End-to-End Secure</span>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-10 py-4 text-sm font-black text-white shadow-xl shadow-indigo-900/40 hover:bg-indigo-500 hover:-translate-y-0.5 transition-all active:scale-95 uppercase tracking-widest">
                            Send Request
                            <i data-lucide="send" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar Info Column -->
        <div class="space-y-8">
            <!-- Guidelines Card -->
            <div class="bg-slate-950 border border-slate-800 rounded-3xl p-8 shadow-2xl">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i data-lucide="info" class="h-4 w-4 text-indigo-500"></i>
                    Important Notice
                </h3>
                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <div class="h-6 w-6 rounded-full bg-indigo-500/10 text-indigo-500 flex items-center justify-center shrink-0 text-[10px] font-black italic">1</div>
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Ensure all information provided is accurate and matches your registered resident profile.</p>
                    </li>
                    <li class="flex gap-4">
                        <div class="h-6 w-6 rounded-full bg-indigo-500/10 text-indigo-500 flex items-center justify-center shrink-0 text-[10px] font-black italic">2</div>
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Standard processing time is 1-3 working days. You will receive a notification once ready.</p>
                    </li>
                    <li class="flex gap-4">
                        <div class="h-6 w-6 rounded-full bg-indigo-500/10 text-indigo-500 flex items-center justify-center shrink-0 text-[10px] font-black italic">3</div>
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Please bring your reference number and valid ID when picking up documents.</p>
                    </li>
                </ul>
            </div>

            <!-- Support Card -->
            <div class="bg-indigo-600/5 border border-indigo-500/20 rounded-3xl p-8 text-center space-y-4">
                <div class="h-14 w-14 rounded-full bg-indigo-500/10 text-indigo-500 flex items-center justify-center mx-auto">
                    <i data-lucide="help-circle" class="h-7 w-7"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-white uppercase tracking-widest">Need Help?</h4>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Contact the barangay office at<br/><span class="text-indigo-400 font-bold">(02) 8123-4567</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('dropzone-file');
        const fileList = document.getElementById('file-list');

        if (fileInput && fileList) {
            fileInput.addEventListener('change', function() {
                fileList.innerHTML = '';
                
                if (this.files && this.files.length > 0) {
                    Array.from(this.files).forEach(file => {
                        const item = document.createElement('div');
                        item.className = 'flex items-center justify-between rounded-xl bg-slate-900 border border-slate-800 px-4 py-3 text-[10px] text-slate-400 shadow-inner group transition-all hover:border-indigo-500/30 animate-in fade-in zoom-in-95 duration-300';
                        
                        // Format file size
                        const size = file.size > 1024 * 1024 
                            ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' 
                            : (file.size / 1024).toFixed(2) + ' KB';

                        item.innerHTML = `
                            <div class="flex items-center gap-3">
                                <i data-lucide="file" class="h-3.5 w-3.5 text-indigo-400"></i>
                                <span class="font-black uppercase tracking-tight truncate max-w-[140px]">${file.name}</span>
                            </div>
                            <span class="text-slate-600 font-black italic">${size}</span>
                        `;
                        fileList.appendChild(item);
                    });
                    
                    // Re-initialize icons for the new elements
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
