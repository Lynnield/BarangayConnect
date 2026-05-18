@props([
    'options' => [],
    'default' => 'created_at',
    'defaultDirection' => 'desc',
    'inline' => false,
])

@php
    $sort = request('sort', $default);
    $direction = in_array(request('direction'), ['asc', 'desc'], true) ? request('direction') : $defaultDirection;
    $directionId = 'list-sort-direction-' . md5(json_encode($options) . $default);
    $fieldClass = 'block w-full rounded-2xl border border-slate-700 bg-slate-800/50 text-xs text-white shadow-inner transition-all outline-none appearance-none focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10';
@endphp

@if(! $inline)
    <form method="GET" class="inline-flex">
        @foreach(request()->except(['sort', 'direction', 'page']) as $key => $value)
            @if(is_array($value))
                @foreach($value as $item)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                @endforeach
            @elseif($value !== null && $value !== '')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
@endif

@php
    $wrapperClass = $inline ? 'w-full' : 'min-w-[10rem]';
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }}>
    <div class="flex items-center gap-2 w-full">
        <input type="hidden" name="direction" id="{{ $directionId }}" value="{{ $direction }}">

        <button
            type="button"
            title="{{ $direction === 'asc' ? 'Ascending' : 'Descending' }}"
            onclick="(function(){const el=document.getElementById('{{ $directionId }}');el.value=el.value==='asc'?'desc':'asc';{{ $inline ? 'el.form.submit()' : 'el.closest(\'form\').submit()' }};})();"
            class="inline-flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-2xl border border-slate-700 bg-slate-800/50 text-slate-400 shadow-inner transition-all hover:text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
        >
            <i data-lucide="{{ $direction === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="h-4 w-4"></i>
        </button>

        <div class="relative group min-w-[8.5rem] flex-1">
            <select
                name="sort"
                onchange="this.form.submit()"
                class="{{ $fieldClass }} py-3 pl-4 pr-10"
            >
                @foreach($options as $value => $optionLabel)
                    <option value="{{ $value }}" @selected($sort === $value)>{{ $optionLabel }}</option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                <i data-lucide="chevron-down" class="h-4 w-4"></i>
            </div>
        </div>
    </div>
</div>

@if(! $inline)
    </form>
@endif
