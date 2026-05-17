@props([
    'size' => 'md',
    'alt' => null,
])

@php
    $sizes = [
        'sm' => 'h-8 w-8',
        'md' => 'h-10 w-10',
        'lg' => 'h-12 w-12',
        'xl' => 'h-16 w-16',
        '2xl' => 'h-24 w-24',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $logoPath = \App\Models\SystemSetting::get('logo_path');
    $barangayName = \App\Models\SystemSetting::get('barangay_name', 'Barangay Connect');
    $altText = $alt ?? ($barangayName . ' logo');
    $baseClass = $sizeClass . ' rounded-xl object-cover shadow-lg shrink-0';

    $logoUrl = null;
    if ($logoPath) {
        if (preg_match('/^https?:\/\//', $logoPath)) {
            $logoUrl = $logoPath;
        } else {
            $publicPath = preg_replace('#^/?storage/#', 'storage/', $logoPath);
            $storagePath = preg_replace('#^/?storage/#', '', $logoPath);

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)) {
                $logoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($storagePath);
            } elseif (file_exists(public_path($publicPath))) {
                $logoUrl = asset($publicPath);
            } elseif (file_exists(public_path($logoPath))) {
                $logoUrl = asset($logoPath);
            }
        }
    }
@endphp

@if($logoUrl)
    <img
        src="{{ $logoUrl }}"
        alt="{{ $altText }}"
        {{ $attributes->merge(['class' => $baseClass]) }}
    >
@else
    <img
        src="{{ asset('images/barangay-logo.svg') }}"
        alt="{{ $altText }}"
        {{ $attributes->merge(['class' => $sizeClass . ' rounded-xl object-contain shrink-0']) }}
    >
@endif
