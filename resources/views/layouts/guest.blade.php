<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — {{ \App\Models\SystemSetting::get('barangay_name', 'Barangay Connect') }}</title>
    <!-- Favicon -->
    @php
        $logoPath = \App\Models\SystemSetting::get('logo_path');
    @endphp
    @if($logoPath && Storage::disk('public')->exists($logoPath))
        <link rel="icon" href="{{ asset('storage/' . $logoPath) }}" type="image/png">
    @else
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @endif
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS (CDN Fallback for immediate fix) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script nonce="{{ $cspNonce }}">
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/validation.js'])
</head>
<body class="bg-slate-900 font-sans text-slate-100 antialiased min-h-screen">
    <div class="min-h-screen flex flex-col w-full relative overflow-hidden">
        <!-- Background Orbs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-600/20 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-600/20 rounded-full blur-[120px] pointer-events-none"></div>

        <!-- Modern Header -->
        <header class="h-20 w-full bg-slate-900/50 backdrop-blur-xl border-b border-slate-800 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto h-full w-full flex items-center justify-between px-6 lg:px-10">
                <a href="{{ route('home') }}" class="flex items-center gap-3 transition-all hover:scale-[1.02] group">
                    <x-barangay-logo size="lg" class="ring-1 ring-white/10" />
                    <div class="flex flex-col">
                        <span class="font-black text-white tracking-tight leading-none text-xl">{{ \App\Models\SystemSetting::get('barangay_name', 'Barangay Connect') }}</span>
                        <span class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em] mt-1.5">Official e-Services</span>
                    </div>
                </a>
                
                <div class="flex items-center gap-6">
                    @guest
                        <a href="{{ route('login') }}" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-white transition-colors hidden sm:block">Log in</a>
                        <x-button href="{{ route('register') }}" variant="primary" size="sm" icon="user-plus">Register</x-button>
                    @else
                        <x-button href="{{ route(auth()->user()->role?->slug . '.dashboard') }}" variant="secondary" size="sm" icon="layout-dashboard">Dashboard</x-button>
                    @endguest
                </div>
            </div>
        </header>

        <!-- Centered Content Area -->
        <main class="flex-1 w-full relative flex flex-col items-center justify-center py-16 px-6 z-10">
            <div class="w-full @yield('container_width', 'max-w-md')">
                @if(session('status'))
                    <div class="mb-8 rounded-2xl border border-blue-500/20 bg-blue-500/10 p-4 text-sm font-bold text-blue-400 shadow-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                        <i data-lucide="info" class="h-5 w-5"></i>
                        {{ session('status') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-8 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-5 text-sm font-bold text-rose-400 shadow-xl animate-in fade-in slide-in-from-top-4">
                        <div class="flex items-center gap-3 mb-2">
                            <i data-lucide="alert-circle" class="h-5 w-5"></i>
                            <span>Please fix the following errors:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 opacity-90">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>

        <!-- Centered Footer -->
        <footer class="py-10 w-full border-t border-slate-800 bg-slate-900/50 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest text-center md:text-left">
                    &copy; {{ date('Y') }} {{ \App\Models\SystemSetting::get('barangay_name', 'Barangay') }} Connect. All rights reserved.
                </p>
                <div class="flex justify-center items-center gap-8 text-[10px] font-black text-slate-600 uppercase tracking-[0.2em]">
                    <a href="#" class="hover:text-indigo-500 transition-colors">Privacy</a>
                    <a href="#" class="hover:text-indigo-500 transition-colors">Terms</a>
                    <a href="#" class="hover:text-indigo-500 transition-colors">Contact</a>
                </div>
            </div>
        </footer>
    </div>

    <script nonce="{{ $cspNonce }}">
        // Initialize Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>
