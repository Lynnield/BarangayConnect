@extends('layouts.app')

@section('title', 'Import Residents')
@section('breadcrumb', 'Import')

@section('content')
<div class="max-w-screen-md mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Import Residents</h1>
            <p class="text-sm text-slate-500">Upload CSV or Excel files, preview validation, then import clean rows.</p>
        </div>
        <a href="{{ route('admin.residents.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 flex items-center gap-1 transition-colors">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Back to list
        </a>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="info" class="h-4 w-4 text-indigo-500"></i>
                Import Instructions
            </h2>
            <div class="mt-4 space-y-4">
                <p class="text-sm text-slate-600 leading-relaxed">
                    Please ensure your file follows the correct format. Preview validates the first pass without creating records.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.residents.import-template') }}" class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition-all">
                        <i data-lucide="download-cloud" class="h-4 w-4"></i>
                        Download CSV Template
                    </a>
                    <a href="{{ route('admin.residents.import-template', ['format' => 'xlsx']) }}" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition-all">
                        <i data-lucide="file-spreadsheet" class="h-4 w-4"></i>
                        Download Excel Template
                    </a>
                </div>
            </div>
        </div>

        @if(session('import_preview'))
            @php($preview = session('import_preview'))
            <div class="p-8 border-b border-slate-100 bg-white">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Valid Rows</p>
                        <p class="mt-1 text-2xl font-black text-emerald-900">{{ $preview['imported_count'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-rose-700">Rows With Errors</p>
                        <p class="mt-1 text-2xl font-black text-rose-900">{{ $preview['failed_count'] }}</p>
                    </div>
                </div>

                @if(!empty($preview['preview']))
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="min-w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 uppercase tracking-widest">
                                <tr>
                                    <th class="px-4 py-3">Row</th>
                                    <th class="px-4 py-3">Full Name</th>
                                    <th class="px-4 py-3">Gender</th>
                                    <th class="px-4 py-3">Birthdate</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($preview['preview'] as $row)
                                    <tr>
                                        <td class="px-4 py-3 font-bold text-slate-500">{{ $row['row'] }}</td>
                                        <td class="px-4 py-3 font-bold text-slate-900">{{ $row['full_name'] }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $row['gender'] }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $row['birthdate'] }}</td>
                                        <td class="px-4 py-3 text-emerald-700 font-black uppercase">{{ $row['status'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        <form id="importForm" method="POST" action="{{ route('admin.residents.import') }}" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            
            <div class="space-y-4">
                <label class="block text-sm font-bold text-slate-700">Choose CSV or Excel File</label>
                <div class="relative group">
                    <input id="importFile" type="file" name="file" accept=".csv,.txt,.xlsx" required
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-slate-900 file:text-white hover:file:bg-slate-800 file:transition-all cursor-pointer border border-slate-200 rounded-2xl p-2 bg-slate-50/50 group-hover:bg-white group-hover:border-indigo-300 transition-all">
                </div>
                <p class="text-xs text-slate-400">Maximum file size: 5MB. CSV, TXT, and XLSX files are allowed.</p>
                <div id="clientPreview" class="hidden rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600"></div>
                <div id="importProgressWrap" class="hidden h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div id="importProgress" class="h-full w-0 bg-indigo-600 transition-all"></div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-3">Required Headers:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(['full_name', 'gender', 'birthdate', 'civil_status', 'address'] as $h)
                        <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-[10px] font-mono font-bold text-slate-600">{{ $h }}</span>
                    @endforeach
                </div>
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mt-6 mb-3">Optional Headers:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(['contact_number', 'email', 'occupation'] as $h)
                        <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-[10px] font-mono font-bold text-slate-400">{{ $h }}</span>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <button type="submit" formaction="{{ route('admin.residents.import-preview') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-8 py-3 text-sm font-bold text-slate-700 hover:border-indigo-300 hover:text-indigo-700 transition-all">
                    <i data-lucide="scan-search" class="h-4 w-4"></i>
                    Preview Validation
                </button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-500 transition-all">
                    <i data-lucide="upload-cloud" class="h-4 w-4"></i>
                    Start Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const fileInput = document.getElementById('importFile');
const preview = document.getElementById('clientPreview');
const form = document.getElementById('importForm');
const progressWrap = document.getElementById('importProgressWrap');
const progress = document.getElementById('importProgress');

fileInput?.addEventListener('change', () => {
    const file = fileInput.files?.[0];
    if (!file) return;

    preview.classList.remove('hidden');
    preview.textContent = `${file.name} selected. Server validation supports CSV, TXT, and XLSX.`;

    if (file.name.toLowerCase().endsWith('.csv') || file.name.toLowerCase().endsWith('.txt')) {
        const reader = new FileReader();
        reader.onload = () => {
            const lines = String(reader.result).split(/\r?\n/).filter(Boolean);
            preview.textContent = `${file.name}: ${Math.max(lines.length - 1, 0)} data row(s) detected. Header: ${lines[0] || 'missing'}`;
        };
        reader.readAsText(file.slice(0, 8192));
    }
});

form?.addEventListener('submit', () => {
    progressWrap.classList.remove('hidden');
    progress.style.width = '35%';
    setTimeout(() => progress.style.width = '75%', 250);
});
</script>
@endpush
