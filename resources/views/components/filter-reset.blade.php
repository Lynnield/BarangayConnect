@props([
    'route',
    'active' => false,
])

<x-button
    type="button"
    variant="ghost"
    size="md"
    icon="rotate-ccw"
    onclick="window.location.href='{{ $route }}'"
    {{ $attributes->merge([
        'class' => 'shrink-0 ' . ($active ? '' : 'invisible pointer-events-none'),
        'aria-hidden' => $active ? 'false' : 'true',
        'tabindex' => $active ? '0' : '-1',
    ]) }}
/>
