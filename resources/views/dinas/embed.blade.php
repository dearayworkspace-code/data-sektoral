@extends('layouts.app')

@section('title', 'Spreadsheet - ' . $dinas->nama_dinas)

@push('styles')
<style>
    /* Mengunci scroll body agar hanya iframe yang dapat di-scroll */
    html, body {
        height: 100%;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="h-screen w-screen flex flex-col overflow-hidden transition-colors duration-300"
     :class="isDark ? 'bg-[#070d1d]' : 'bg-slate-900'">

    <!-- Top Bar Navigasi (Sesuai instruksi: TIDAK ADA tombol Buka Tab Baru) -->
    <header class="h-16 border-b px-4 sm:px-6 flex items-center justify-between shrink-0 text-white z-20 transition-colors"
            :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-slate-900 border-slate-800'">
        
        <!-- Left: Back Button & Dinas Info -->
        <div class="flex items-center gap-3">
            <a href="{{ route('dinas.portal') }}" 
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold transition border"
               :class="isDark 
                  ? 'bg-slate-800 hover:bg-slate-700 text-slate-200 border-slate-700' 
                  : 'bg-slate-800 hover:bg-slate-700 text-slate-200 border-slate-700'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Portal</span>
            </a>

            <div class="h-4 w-px bg-slate-700"></div>

            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center p-1 shadow-sm shrink-0 bg-slate-800 border border-slate-700">
                    <img src="{{ asset('images/bps-logo.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <h1 class="text-xs sm:text-sm font-extrabold truncate max-w-xs sm:max-w-md text-white">
                        {{ $dinas->nama_dinas }}
                    </h1>
                </div>
            </div>
        </div>

        <!-- Right Side: Mode Switcher, PIC & Logout -->
        <div class="flex items-center gap-3 text-xs">
            <!-- Pill Switcher Mode Gelap / Terang -->
            <button type="button" 
                    @click="toggleTheme()" 
                    class="hidden sm:flex px-3 py-1.5 rounded-full backdrop-blur-md border text-[11px] font-semibold items-center gap-1.5 transition"
                    :class="isDark 
                        ? 'bg-slate-800/80 hover:bg-slate-800 text-white border-slate-700' 
                        : 'bg-slate-800/60 hover:bg-slate-800 text-white border-slate-700'">
                <span>Mode Gelap/Terang</span>
                <span class="text-xs" x-text="isDark ? '🌙' : '☀️'"></span>
            </button>

            <span class="hidden md:inline-block text-slate-400 text-xs">
                PIC: <span class="text-slate-200 font-semibold">{{ $dinas->pic_name ?? '-' }}</span>
            </span>

            <form action="{{ route('dinas.logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        title="Keluar dari sesi" 
                        class="px-3 py-1.5 text-xs font-semibold text-rose-300 hover:text-white hover:bg-rose-600/30 rounded-xl transition border border-rose-500/20">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Embedded Google Spreadsheet Container -->
    <main class="flex-1 w-full h-[calc(100vh-4rem)] bg-white relative">
        <iframe src="{{ $embedUrl }}" 
                class="w-full h-full border-0" 
                allow="clipboard-read; clipboard-write; fullscreen"
                loading="lazy">
        </iframe>
    </main>
</div>
@endsection
