@props(['type' => 'neutral'])

@php
    $classes = [
        'neutral' => 'bg-slate-500/10 text-slate-500 border-slate-500/20',
        'primary' => 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20',
        'success' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
        'warning' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
        'danger' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
        'info' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
    ][$type] ?? 'bg-slate-500/10 text-slate-500 border-slate-500/20';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-tight $classes shadow-sm"]) }}>
    {{ $slot }}
</span>
