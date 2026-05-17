@extends('layouts.app')

@section('title', 'Document Types')

@section('breadcrumb')
    <span class="text-slate-500">Document Types</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Document Configuration</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Manage available document types, processing fees, and turnaround times.</p>
            </div>
            <x-button href="{{ route('admin.document-types.create') }}" variant="primary" size="md" icon="plus" class="shadow-indigo-600/20">
                New Document Type
            </x-button>
        </div>
    </x-card>

    <!-- Table Section -->
    <x-table-wrapper title="Service Catalog" icon="file-text">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Document Service</th>
                    <th class="px-6 py-4">Processing Fee</th>
                    <th class="px-6 py-4">Turnaround</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @foreach($documentTypes as $d)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                    <i data-lucide="file-text" class="h-5 w-5"></i>
                                </div>
                                <div>
                                    <div class="font-black text-white group-hover:text-indigo-400 transition-colors">{{ $d->name }}</div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Official Service</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-emerald-500">
                                ₱{{ number_format($d->fee, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="h-3.5 w-3.5 text-slate-500"></i>
                                <span class="text-sm font-bold text-slate-300">{{ $d->processing_days }}</span>
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Working Days</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($d->is_active)
                                <x-badge type="success">Available</x-badge>
                            @else
                                <x-badge type="neutral">Inactive</x-badge>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            <a href="{{ route('admin.document-types.edit', $d) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-blue-400 hover:border-blue-400/50 hover:bg-blue-400/5 transition-all" title="Edit Service">
                                <i data-lucide="edit-2" class="h-4 w-4"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($documentTypes->hasPages())
            <x-slot:footer>
                <div class="px-2">
                    {{ $documentTypes->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-table-wrapper>
</div>
@endsection