<footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 w-full">
    <div class="px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-slate-500 font-medium">
                &copy; {{ now()->year }} {{ \App\Models\SystemSetting::get('barangay_name', 'Barangay') }}. All rights reserved.
            </p>
            <div class="flex items-center gap-6 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <a href="#" class="hover:text-indigo-600 transition-colors">Privacy</a>
                <a href="#" class="hover:text-indigo-600 transition-colors">Terms</a>
                <a href="#" class="hover:text-indigo-600 transition-colors">Support</a>
            </div>
        </div>
    </div>
</footer>
