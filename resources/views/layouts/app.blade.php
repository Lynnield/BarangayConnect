<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ \App\Models\SystemSetting::get('barangay_name', 'Barangay San Jose') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/validation.js'])

    @stack('styles')
    <script>
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.classList.toggle('dark', savedTheme !== 'light');
    </script>
</head>
<body class="bg-slate-900 font-sans text-slate-100 antialiased h-screen w-screen overflow-hidden">

    <!-- Viewport Shield -->
    <div class="flex h-screen w-screen overflow-hidden bg-slate-900 text-slate-100">
        
        @include('layouts.partials.sidebar')

        <!-- Main Column Canvas Manager -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-900 overflow-hidden">
            
            <!-- Header Top Navigation -->
            @include('layouts.partials.topbar')

            <!-- Independent Scrolling Main Viewport -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-10 w-full scrollbar-thin scrollbar-thumb-slate-800">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden lg:hidden"></div>

    <script nonce="{{ $cspNonce }}">
        // Initialize Lucide Icons
        lucide.createIcons();

        // Sidebar toggle for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay?.classList.toggle('hidden');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar?.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            });
        }

        // Dropdown toggle logic
        document.addEventListener('click', (e) => {
            const toggle = e.target.closest('[data-toggle="dropdown"]');
            if (toggle) {
                const targetId = toggle.getAttribute('data-target');
                const menu = document.getElementById(targetId);
                menu?.classList.toggle('hidden');
            } else if (!e.target.closest('[role="menu"]')) {
                document.querySelectorAll('[id$="-dropdown"]').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
