@props(['title' => null, 'icon' => null, 'action' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'rounded-3xl border border-slate-800 bg-slate-900/50 backdrop-blur-sm shadow-sm overflow-hidden']) }}>
    @if($title || $action)
        <div class="border-b border-slate-800 bg-slate-900/50 px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-800 text-slate-400">
                        <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
                    </div>
                @endif
                @if($title)
                    <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">{{ $title }}</h3>
                @endif
            </div>
            @if($action)
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif
    
    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-slate-800">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="bg-slate-900/50 px-6 py-4 border-t border-slate-800">
            {{ $footer }}
        </div>
    @endif
</div>
