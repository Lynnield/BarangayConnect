@props(['title' => null, 'footer' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden']) }}>
    @if($title)
        <div class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ $title }}</h3>
        </div>
    @endif

    <div @class(['px-6 py-4' => $padding])>
        {{ $slot }}
    </div>

    @if($footer)
        <div class="bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 border-t border-slate-100 dark:border-slate-800">
            {{ $footer }}
        </div>
    @endif
</div>
