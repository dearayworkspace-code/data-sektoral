<!DOCTYPE html>
<html lang="id" 
      x-data="{ 
        isDark: (localStorage.getItem('theme_mode') === 'dark' || localStorage.getItem('theme_dinas') === 'dark'),
        toggleTheme() {
            this.isDark = !this.isDark;
            localStorage.setItem('theme_mode', this.isDark ? 'dark' : 'light');
            localStorage.setItem('theme_dinas', this.isDark ? 'dark' : 'light');
        }
      }" 
      :class="{ 'dark': isDark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Data Dinas - BPS Kab. Lampung Selatan')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body :class="isDark ? 'bg-[#070d1d] text-slate-100' : 'bg-slate-50 text-slate-800'"
      class="antialiased min-h-screen flex flex-col transition-colors duration-300">

    <!-- Flash Messages (Notifikasi Terpadu) -->
    <div class="fixed top-5 right-5 z-50 space-y-3 max-w-md w-full px-4 pointer-events-none">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="pointer-events-auto flex items-start gap-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300 backdrop-blur-md px-4 py-3 rounded-2xl shadow-xl transition">
                <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1 text-xs font-semibold">{{ session('success') }}</div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 text-sm">✕</button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" 
                 class="pointer-events-auto flex items-start gap-3 bg-rose-500/10 border border-rose-500/30 text-rose-800 dark:text-rose-300 backdrop-blur-md px-4 py-3 rounded-2xl shadow-xl transition">
                <svg class="w-5 h-5 text-rose-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1 text-xs font-semibold">{{ session('error') }}</div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200 text-sm">✕</button>
            </div>
        @endif

        @if (session('warning'))
            <div x-data="{ show: true }" x-show="show" 
                 class="pointer-events-auto flex items-start gap-3 bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 backdrop-blur-md px-4 py-3 rounded-2xl shadow-xl transition">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1 text-xs font-semibold">{{ session('warning') }}</div>
                <button @click="show = false" class="text-amber-500 hover:text-amber-700 dark:hover:text-amber-200 text-sm">✕</button>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div x-data="{ show: true }" x-show="show" 
                 class="pointer-events-auto bg-rose-500/10 border border-rose-500/30 text-rose-800 dark:text-rose-300 backdrop-blur-md px-4 py-3 rounded-2xl shadow-xl">
                <div class="font-bold text-xs mb-1">Terjadi kendala pengisian:</div>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    @yield('content')

    @stack('scripts')
</body>
</html>
