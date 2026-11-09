@extends('layouts.app')

@section('title', 'Autentikasi Administrator - BPS Kab. Lampung Selatan')

@section('content')
<div class="min-h-screen bg-[#070d1d] flex flex-col justify-center items-center px-4 py-8 sm:py-12 relative overflow-hidden font-sans">
    
    <!-- Background Ambient Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">

        <!-- Top Row: Logo & Pill Switcher Mode Gelap / Terang -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center p-1 shadow-sm shrink-0 transition-colors"
                     :class="isDark ? 'bg-slate-800/80 border border-slate-700/80' : 'bg-slate-50 border border-slate-200/90'">
                    <img src="{{ asset('images/bps-logo.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-tight leading-none"
                        :class="isDark ? 'text-white' : 'text-slate-100'">
                        Admin BPS Portal
                    </h1>
                    <p class="text-[11px] font-medium mt-1 leading-none text-slate-400">
                        Badan Pusat Statistik · Kab. Lampung Selatan
                    </p>
                </div>
            </div>

            <!-- Pill Switcher -->
            <button type="button" 
                    @click="toggleTheme()" 
                    class="px-3 py-1.5 rounded-full backdrop-blur-md border text-[11px] font-semibold flex items-center gap-1.5 shadow-md transition transform active:scale-95"
                    :class="isDark 
                        ? 'bg-slate-800/70 hover:bg-slate-800 text-white border-slate-700' 
                        : 'bg-white/10 hover:bg-white/20 text-white border-white/20'">
                <span>Mode Gelap/Terang</span>
                <span class="text-xs" x-text="isDark ? '🌙' : '☀️'"></span>
            </button>
        </div>

        <!-- Main Login Card (Single Centered Card - Opsi B) -->
        <div class="rounded-[2.25rem] border p-7 sm:p-9 shadow-2xl transition-all duration-300"
             :class="isDark 
                ? 'bg-[#0c1427] border-slate-800 text-white shadow-sky-950/30' 
                : 'bg-white border-slate-200/90 text-slate-800 shadow-slate-900/10'">
            
            <div class="mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-2.5"
                      :class="isDark ? 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/30' : 'bg-indigo-50 text-indigo-700 border border-indigo-200'">
                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Area Administrator BPS</span>
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight"
                    :class="isDark ? 'text-white' : 'text-slate-900'">
                    Masuk Administrator
                </h2>
                <p class="text-xs mt-1.5 leading-relaxed"
                   :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                    Pengelolaan mapping spreadsheet, monitoring rincian tabel LSDA 2026, dan akun dinas.
                </p>
            </div>

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4 sm:space-y-5">
                @csrf

                <!-- Username -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider mb-1.5"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Username Administrator <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="username" 
                           value="{{ old('username') }}" 
                           required 
                           autofocus
                           placeholder="Masukkan username admin" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs transition focus:outline-none border"
                           :class="isDark 
                              ? 'bg-[#15203b] border-slate-700/80 text-white placeholder-slate-500 focus:border-sky-400' 
                              : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400 focus:border-blue-500'">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider mb-1.5"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           required 
                           placeholder="••••••••" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs transition focus:outline-none border tracking-wider"
                           :class="isDark 
                              ? 'bg-[#15203b] border-slate-700/80 text-white placeholder-slate-500 focus:border-sky-400' 
                              : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400 focus:border-blue-500'">
                </div>

                <div class="flex items-center justify-between text-xs pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-blue-500"
                               :class="isDark ? 'bg-slate-900 border-slate-700' : 'bg-white border-slate-300'">
                        <span :class="isDark ? 'text-slate-400' : 'text-slate-600'">Ingat sesi saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-3 px-4 font-bold rounded-xl text-xs sm:text-sm tracking-wide transition transform active:scale-[0.99] flex items-center justify-center gap-2"
                            :class="isDark 
                                ? 'bg-sky-500 hover:bg-sky-400 text-slate-950 shadow-lg shadow-sky-500/20' 
                                : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-lg shadow-blue-500/25'">
                        <span>Masuk ke Dashboard BPS</span>
                        <span class="text-base leading-none">→</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to Dinas Portal Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('dinas.login') }}" 
               class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white transition">
                <span>← Kembali ke Halaman Login Dinas</span>
            </a>
        </div>

    </div>
</div>
@endsection
