@props(['label', 'value', 'icon', 'color' => 'indigo'])

@php
    $colors = [
        'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400',
        'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400',
        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400',
        'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400',
        'slate' => 'bg-slate-50 text-slate-600 dark:bg-slate-900/20 dark:text-slate-400',
    ];
    $colorClass = $colors[$color] ?? $colors['indigo'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm transition-all hover:shadow-md']) }}>
    <div class="flex items-center gap-4 min-w-0">
        <div class="rounded-2xl p-3 {{ $colorClass }} flex-shrink-0">
            <i data-lucide="{{ $icon }}" class="h-6 w-6"></i>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 truncate">{{ $label }}</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white leading-none mt-1 truncate">{{ $value }}</p>
        </div>
    </div>
</div>
