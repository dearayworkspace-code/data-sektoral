@extends('layouts.app')

@section('title', 'Autentikasi Administrator - BPS Kab. Lampung Selatan')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 sm:py-12 relative overflow-hidden font-sans transition-colors duration-200"
     :class="isDark ? 'bg-[#090d16] text-slate-100' : 'bg-[#f8fafc] text-slate-900'">
    
    <!-- Precision Background Grid -->
    @include('partials.animated-background')

    <div class="w-full max-w-[420px] relative z-10" x-data="{ showPassword: false }">

        <!-- Unified Single Card (Header, Dark/Light Mode, & Form Login Terintegrasi) -->
        <div class="rounded-2xl border shadow-xl overflow-hidden transition-all duration-200"
             :class="isDark 
                ? 'bg-[#0d1424] border-slate-800 text-white shadow-black/50' 
                : 'bg-white border-slate-200/90 text-slate-900 shadow-slate-200/60'">
            
            <!-- 1. Card Header: Branding, Judul Dashboard & Dark/Light Switcher -->
            <div class="px-6 py-4 border-b flex items-center justify-between transition-colors"
                 :class="isDark ? 'border-slate-800/80 bg-[#131b2e]/50' : 'border-slate-100 bg-slate-50/80'">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center p-1.5 shadow-xs shrink-0 transition-colors border"
                         :class="isDark ? 'bg-slate-900 border-slate-700/80' : 'bg-white border-slate-200'">
                        <img src="{{ asset('images/bps-logo.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-xs font-bold tracking-tight leading-none"
                            :class="isDark ? 'text-white' : 'text-slate-900'">
                            Dashboard Data Dinas / Instansi
                        </h1>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 leading-none">
                            BPS Kabupaten Lampung Selatan
                        </p>
                    </div>
                </div>

                <!-- Theme Switcher Button (Vector SVG) -->
                <button type="button" 
                        @click="toggleTheme()" 
                        class="w-8 h-8 rounded-lg border flex items-center justify-center shadow-xs transition btn-tactile"
                        :class="isDark 
                            ? 'bg-[#0f172a] hover:bg-slate-800 text-slate-200 border-slate-700 hover:text-white' 
                            : 'bg-white hover:bg-slate-100 text-slate-600 border-slate-200 hover:text-slate-900'"
                        title="Beralih mode tema"
                        aria-label="Beralih mode tema">
                    <svg x-show="!isDark" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="isDark" class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
            </div>

            <!-- 2. Card Body: Form Login Administrator -->
            <div class="p-6 sm:p-7">
                <div class="mb-5">
                    <h2 class="text-lg font-bold tracking-tight"
                        :class="isDark ? 'text-white' : 'text-slate-900'">
                        Masuk Administrator
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                        Silakan masukkan kredensial akun administrator Anda.
                    </p>
                </div>

                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-semibold mb-1"
                               :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Username administrator <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   autofocus
                                   placeholder="Masukkan username" 
                                   class="w-full pl-9 pr-3 py-2.5 rounded-lg text-xs transition border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                                   :class="isDark 
                                      ? 'bg-[#131b2e] border-slate-700 text-white placeholder-slate-500 focus:border-blue-500' 
                                      : 'bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-blue-600'">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-semibold mb-1"
                               :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Kata sandi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required 
                                   placeholder="Masukkan kata sandi" 
                                   class="w-full pl-9 pr-10 py-2.5 rounded-lg text-xs transition border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none tracking-normal"
                                   :class="isDark 
                                      ? 'bg-[#131b2e] border-slate-700 text-white placeholder-slate-500 focus:border-blue-500' 
                                      : 'bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-blue-600'">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            
                            <!-- Toggle Password Visibility (Eye Icon Button) -->
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute right-2.5 top-2.5 p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition"
                                    :title="showPassword ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-blue-500"
                                   :class="isDark ? 'bg-slate-900 border-slate-700' : 'bg-white border-slate-300'">
                            <span :class="isDark ? 'text-slate-400' : 'text-slate-600'">Ingat sesi saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-1.5">
                        <button type="submit" 
                                class="w-full py-2.5 px-4 font-semibold rounded-lg text-xs transition btn-tactile flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white shadow-xs focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none">
                            <span>Masuk Administrator</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. Card Footer: Link Kembali ke Portal Dinas -->
            <div class="px-6 py-3.5 border-t text-center transition-colors"
                 :class="isDark ? 'border-slate-800 bg-[#131b2e]/30' : 'border-slate-100 bg-slate-50/70'">
                <a href="{{ route('dinas.login') }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-medium transition text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Beralih ke Login Dinas / Instansi</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection