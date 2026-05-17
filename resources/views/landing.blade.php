<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\SystemSetting::get('barangay_name', 'Barangay Connect') }} — Modern e-Services</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(51, 65, 85, 0.5);
        }
        .text-gradient {
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-[#020617] text-slate-300 antialiased selection:bg-indigo-500/30 selection:text-indigo-200">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-[100] border-b border-slate-800/50 bg-slate-950/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                    <i data-lucide="shield-check" class="h-6 w-6"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-white font-black tracking-tight leading-none">{{ \App\Models\SystemSetting::get('barangay_name', 'Barangay Connect') }}</span>
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em] mt-1">Digital Government</span>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-white transition-colors">Features</a>
                <a href="#contact" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-white transition-colors">Contact</a>
            </div>

            <div class="flex items-center gap-4">
                @guest
                    <a href="{{ route('login') }}" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-white transition-colors hidden sm:block">Log In</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-xs font-black uppercase tracking-widest text-slate-950 hover:bg-indigo-50 transition-all active:scale-95">
                        Get Started
                    </a>
                @else
                    <a href="{{ route(auth()->user()->role?->slug . '.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 border border-slate-800 px-5 py-2.5 text-xs font-black uppercase tracking-widest text-white hover:bg-slate-800 transition-all">
                        Dashboard
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </a>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="relative pt-32 pb-20 overflow-hidden">
        <!-- Background Orbs -->
        <div class="absolute top-0 left-1/4 -translate-x-1/2 w-[500px] h-[500px] bg-indigo-600/20 blur-[120px] rounded-full -z-10"></div>
        <div class="absolute bottom-0 right-1/4 translate-x-1/2 w-[400px] h-[400px] bg-purple-600/10 blur-[100px] rounded-full -z-10"></div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                <!-- Hero Content -->
                <div class="lg:col-span-7 space-y-8">
                    <div class="inline-flex items-center gap-2 rounded-full bg-indigo-500/10 border border-indigo-500/20 px-4 py-2 text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] animate-fade-in">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                        </span>
                        Next-Gen Public Service
                    </div>

                    <h1 class="text-5xl md:text-7xl font-black text-white leading-[1.05] tracking-tight animate-slide-up">
                        Barangay services, <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">reimagined for you.</span>
                    </h1>

                    <p class="text-lg text-slate-400 leading-relaxed max-w-2xl animate-fade-in delay-200">
                        The official digital portal for Barangay San Jose. Request documents, schedule appointments, and stay connected with your community—all in one secure, modern platform.
                    </p>

                    <div class="flex flex-wrap gap-4 pt-4 animate-fade-in delay-300">
                        @guest
                            <a href="{{ route('register') }}" class="group relative inline-flex items-center gap-3 rounded-2xl bg-indigo-600 px-8 py-4 text-sm font-black uppercase tracking-widest text-white shadow-2xl shadow-indigo-600/20 hover:bg-indigo-500 transition-all hover:-translate-y-1">
                                Create Account
                                <i data-lucide="user-plus" class="h-4 w-4 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-3 rounded-2xl bg-slate-900 border border-slate-800 px-8 py-4 text-sm font-black uppercase tracking-widest text-white hover:bg-slate-800 transition-all hover:-translate-y-1">
                                Resident Portal
                            </a>
                        @else
                            <a href="{{ route(auth()->user()->role?->slug . '.dashboard') }}" class="group relative inline-flex items-center gap-3 rounded-2xl bg-indigo-600 px-8 py-4 text-sm font-black uppercase tracking-widest text-white shadow-2xl shadow-indigo-600/20 hover:bg-indigo-500 transition-all hover:-translate-y-1">
                                Go to Dashboard
                                <i data-lucide="layout-dashboard" class="h-4 w-4 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @endguest
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-8 pt-12 border-t border-slate-800/50 max-w-lg">
                        <div>
                            <div class="text-2xl font-black text-white">100%</div>
                            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1">Digital Processing</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white">24/7</div>
                            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1">Access Portal</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white">< 48h</div>
                            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1">Avg. Turnaround</div>
                        </div>
                    </div>
                </div>

                <!-- Visual Element -->
                <div class="lg:col-span-5 relative hidden lg:block">
                    <div class="absolute inset-0 bg-indigo-500/10 blur-[100px] rounded-full"></div>
                    <div class="glass-card rounded-[3rem] p-8 shadow-2xl relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-6">
                            <div class="h-12 w-12 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/20">
                                <i data-lucide="sparkles" class="h-6 w-6"></i>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <h3 class="text-xl font-black text-white uppercase tracking-tight">Available Services</h3>
                            <div class="space-y-4">
                                @foreach([
                                    ['Clearance', 'file-text', 'Official Barangay Clearance'],
                                    ['Indigency', 'heart', 'Certificate of Indigency'],
                                    ['Business', 'briefcase', 'Barangay Business Permit'],
                                    ['Residency', 'home', 'Proof of Residency']
                                ] as $service)
                                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-900/50 border border-slate-800/50 hover:border-indigo-500/30 transition-colors cursor-default group/item">
                                        <div class="h-10 w-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 group-hover/item:bg-indigo-500 group-hover/item:text-white transition-all">
                                            <i data-lucide="{{ $service[1] }}" class="h-5 w-5"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs font-black text-white uppercase tracking-widest">{{ $service[0] }}</div>
                                            <div class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $service[2] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-slate-950/50 relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-xs font-black text-indigo-500 uppercase tracking-[0.3em] mb-4">Core Capabilities</h2>
                <h3 class="text-3xl md:text-5xl font-black text-white tracking-tight">Everything you need, <br/> digitally accessible.</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['Document Requests', 'files', 'Apply for official certificates and permits with digital document attachments and real-time tracking.'],
                    ['Smart Appointments', 'calendar', 'Book verified pickup slots to avoid long queues and ensure your documents are ready when you arrive.'],
                    ['Instant Alerts', 'bell-ring', 'Stay informed with automated status updates via your dashboard and system notifications.']
                ] as $feature)
                    <div class="glass-card p-8 rounded-[2rem] hover:-translate-y-2 transition-all group">
                        <div class="h-14 w-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 border border-indigo-500/20 mb-8 group-hover:scale-110 transition-transform">
                            <i data-lucide="{{ $feature[1] }}" class="h-7 w-7"></i>
                        </div>
                        <h4 class="text-lg font-black text-white uppercase tracking-tight mb-4">{{ $feature[0] }}</h4>
                        <p class="text-sm text-slate-400 leading-relaxed font-medium">
                            {{ $feature[2] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="glass-card rounded-[3rem] p-8 md:p-16 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-12 opacity-10">
                    <i data-lucide="map-pin" class="h-64 w-64 text-white rotate-12"></i>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 relative z-10">
                    <div>
                        <h2 class="text-3xl font-black text-white tracking-tight mb-6">Connect with us.</h2>
                        <p class="text-slate-400 text-lg leading-relaxed mb-10 max-w-md">
                            Our team is here to assist you with any questions regarding our digital services and community programs.
                        </p>
                        
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-indigo-400">
                                    <i data-lucide="phone" class="h-6 w-6"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Phone Support</div>
                                    <div class="text-sm font-black text-white mt-1">{{ \App\Models\SystemSetting::get('contact_phone', '888-0000') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-indigo-400">
                                    <i data-lucide="mail" class="h-6 w-6"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Email Address</div>
                                    <div class="text-sm font-black text-white mt-1">support@barangaysanjose.gov</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-950/50 rounded-3xl p-8 border border-slate-800">
                        <h4 class="text-xs font-black text-white uppercase tracking-widest mb-6">Service Hours</h4>
                        <div class="space-y-4">
                            @foreach([
                                ['Monday - Friday', '8:00 AM - 5:00 PM'],
                                ['Saturday', '8:00 AM - 12:00 PM'],
                                ['Sunday', 'Closed']
                            ] as $time)
                                <div class="flex items-center justify-between py-3 border-b border-slate-800/50 last:border-0">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $time[0] }}</span>
                                    <span class="text-xs font-black text-white uppercase tracking-widest">{{ $time[1] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8 pt-8 border-t border-slate-800">
                            <p class="text-[10px] text-slate-500 font-medium italic leading-relaxed text-center">
                                Emergency services are available 24/7. Please contact our local hotline for urgent assistance.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 border-t border-slate-800/50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-indigo-400">
                        <i data-lucide="shield-check" class="h-4 w-4"></i>
                    </div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                        &copy; {{ date('Y') }} {{ \App\Models\SystemSetting::get('barangay_name', 'Barangay Connect') }}. All rights reserved.
                    </span>
                </div>
                
                <div class="flex items-center gap-6">
                    <a href="#" class="text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-widest transition-colors">Privacy Policy</a>
                    <a href="#" class="text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-widest transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>