@props(['variant' => 'primary', 'size' => 'md', 'icon' => null])

@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20 hover:bg-indigo-500 hover:shadow-indigo-500/40',
        'secondary' => 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white',
        'danger' => 'bg-rose-600 text-white shadow-lg shadow-rose-900/20 hover:bg-rose-500 hover:shadow-rose-500/40',
        'ghost' => 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white',
        'outline' => 'bg-transparent text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-400 dark:hover:text-slate-900',
    ];

    $sizes = [
        'xs' => 'px-2 py-1 text-[10px] uppercase tracking-widest',
        'sm' => 'px-3 py-1.5 text-xs uppercase tracking-wider',
        'md' => 'px-5 py-2.5 text-sm uppercase tracking-wider',
        'lg' => 'px-8 py-4 text-base uppercase tracking-widest',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $class = "inline-flex items-center justify-center gap-2 rounded-2xl font-black transition-all duration-300 hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 disabled:pointer-events-none $variantClass $sizeClass";
    $href = $attributes->get('href');
@endphp

@if($href)
    <a {{ $attributes->merge(['class' => $class]) }}>
        @if($icon)
            <i data-lucide="{{ $icon }}" class="@if($size == 'xs' || $size == 'sm') h-3.5 w-3.5 @else h-4.5 w-4.5 @endif"></i>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button {{ $attributes->merge(['class' => $class]) }}>
        @if($icon)
            <i data-lucide="{{ $icon }}" class="@if($size == 'xs' || $size == 'sm') h-3.5 w-3.5 @else h-4.5 w-4.5 @endif"></i>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
