@extends('layouts.app')

@section('title', 'Portal Data Dinas - ' . $dinas->nama_dinas)

@section('content')
<div class="min-h-screen flex flex-col font-sans transition-colors duration-200"
     :class="isDark ? 'bg-[#090d16] text-slate-100' : 'bg-[#f8fafc] text-slate-900'"
     x-data="{ 
        openUserMenu: false,
        modalProfile: false,
        modalCatatan: false,
        activeTabel: { id: '', nama: '', catatan: '' },
        searchTabel: '',
        filterStatus: 'all',
        openCatatanModal(item) {
            this.activeTabel = { ...item };
            this.modalCatatan = true;
        }
     }">

    <!-- Top Navigation Bar -->
    <header class="border-b sticky top-0 z-30 shadow-xs transition-colors duration-200"
            :class="isDark ? 'bg-[#0d1424] border-slate-800 text-white' : 'bg-white border-slate-200/90 text-slate-900'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Left: Brand Logo & Title -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center p-1.5 shadow-xs shrink-0 transition-colors"
                     :class="isDark ? 'bg-[#0f172a] border border-slate-800' : 'bg-white border border-slate-200'">
                    <img src="{{ asset('images/bps-logo.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-xs font-bold tracking-tight leading-none"
                        :class="isDark ? 'text-white' : 'text-slate-900'">
                        Portal Data Dinas
                    </h1>
                    <span class="text-[11px] font-medium"
                          :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                        BPS Kab. Lampung Selatan
                    </span>
                </div>
            </div>

            <!-- Right: Single Unified Profile & Account Menu Trigger -->
            <div class="relative" @click.outside="openUserMenu = false">
                <!-- Single Icon Trigger Button (Simple, No Detail Text) -->
                <button type="button" 
                        @click="openUserMenu = !openUserMenu" 
                        class="w-9 h-9 rounded-lg flex items-center justify-center border transition btn-tactile"
                        :class="isDark 
                            ? (openUserMenu ? 'bg-slate-800 border-slate-700 text-white ring-2 ring-blue-500/30' : 'bg-[#131b2e] hover:bg-slate-800 border-slate-800 text-slate-300 hover:text-white') 
                            : (openUserMenu ? 'bg-slate-100 border-slate-300 text-slate-900 ring-2 ring-blue-500/20' : 'bg-slate-50 hover:bg-slate-100 border-slate-200/90 text-slate-700 hover:text-slate-900')"
                        title="{{ $dinas->pic_name ?? 'Menu Profil & Pengaturan' }}"
                        aria-label="Menu Profil & Pengaturan"
                        :aria-expanded="openUserMenu ? 'true' : 'false'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </button>

                <!-- Dropdown Menu Panel (Simple & Clean) -->
                <div x-show="openUserMenu" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                     class="absolute right-0 mt-2 w-64 rounded-xl border shadow-xl py-1 z-50 text-xs transition-colors"
                     :class="isDark ? 'bg-[#0d1424] border-slate-800 text-white shadow-black/60' : 'bg-white border-slate-200 text-slate-800 shadow-slate-300/50'"
                     style="display: none;">
                    
                    <!-- 1. Header Informasi Profil PIC Ringkas (Hover untuk nama lengkap) -->
                    <div class="px-3.5 py-2.5 border-b group/instansi cursor-default transition-all duration-200" 
                         :class="isDark ? 'border-slate-800 bg-[#131b2e]/40' : 'border-slate-100 bg-slate-50/60'">
                        <div class="font-semibold text-xs truncate" 
                             :class="isDark ? 'text-white' : 'text-slate-900'"
                             title="{{ $dinas->pic_name ?? 'PIC Dinas' }}">
                            {{ $dinas->pic_name ?? 'PIC Dinas' }}
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5 truncate group-hover/instansi:whitespace-normal group-hover/instansi:break-words leading-relaxed transition-all duration-200"
                             title="{{ $dinas->nama_dinas }}">
                            {{ $dinas->nama_dinas }}
                        </div>
                    </div>

                    <!-- 2. Menu Items (Hanya Ikon & Aksi Utama Tanpa Detail Tulisan) -->
                    <div class="p-1 space-y-0.5">
                        <!-- Profil & PIN -->
                        <button type="button" 
                                @click="openUserMenu = false; modalProfile = true" 
                                class="w-full px-3 py-2 flex items-center gap-2.5 rounded-lg text-xs font-medium transition text-left"
                                :class="isDark ? 'hover:bg-slate-800 text-slate-200' : 'hover:bg-slate-100 text-slate-700'">
                            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Profil & PIN</span>
                        </button>

                        <!-- Beralih Mode Gelap / Terang -->
                        <button type="button" 
                                @click="toggleTheme()" 
                                class="w-full px-3 py-2 flex items-center justify-between rounded-lg text-xs font-medium transition text-left"
                                :class="isDark ? 'hover:bg-slate-800 text-slate-200' : 'hover:bg-slate-100 text-slate-700'">
                            <div class="flex items-center gap-2.5">
                                <svg x-show="!isDark" class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <svg x-show="isDark" class="w-4 h-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                <span x-text="isDark ? 'Mode Gelap' : 'Mode Terang'"></span>
                            </div>
                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded border"
                                  :class="isDark ? 'bg-slate-800 text-sky-300 border-slate-700' : 'bg-slate-100 text-amber-700 border-slate-200'"
                                  x-text="isDark ? 'Gelap' : 'Terang'">
                            </span>
                        </button>

                        <div class="border-t my-1" :class="isDark ? 'border-slate-800' : 'border-slate-100'"></div>

                        <!-- Keluar Sesi -->
                        <form action="{{ route('dinas.logout') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-3 py-2 flex items-center gap-2.5 rounded-lg text-xs font-medium transition text-left"
                                    :class="isDark ? 'hover:bg-rose-950/30 text-rose-400' : 'hover:bg-rose-50 text-rose-600'">
                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- ==================== 1. WELCOME BANNER & METADATA DINAS ==================== -->
        <div class="rounded-2xl p-6 sm:p-7 text-white shadow-lg relative overflow-hidden border border-blue-900/60 bg-gradient-to-br from-blue-950 via-[#0d1a3a] to-[#090d16]">
            <div class="relative z-10 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-medium">
                        Instansi / OPD Kabupaten Lampung Selatan
                    </span>
                    @if ($dinas->shortlink)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-white/10 text-slate-200 text-xs font-mono border border-white/10">
                            {{ $dinas->shortlink }}
                        </span>
                    @endif
                    @if ($dinas->petugas_bps)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-medium">
                            Pembina BPS: <strong>{{ $dinas->petugas_bps }}</strong>
                        </span>
                    @endif
                </div>

                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">{{ $dinas->nama_dinas }}</h2>
                <p class="text-slate-300 text-xs sm:text-sm mt-1.5 leading-relaxed">
                    Portal pengumpulan dan sinkronisasi data publikasi <strong>Lampung Selatan Dalam Angka (LSDA 2026)</strong>. Silakan akses lembar kerja melalui tombol di bawah, ikuti panduan pengisian, dan konfirmasikan setiap tabel yang selesai diinput.
                </p>
            </div>
        </div>


        <!-- ==================== 3. SECTION UTAMA: PETUNJUK (KIRI) & SPREADSHEET (KANAN) ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

            <!-- Card Kiri: Petunjuk Pengisian Spreadsheet (Simple, Cepat Dipahami, 4 Langkah) -->
            <div class="rounded-xl p-5 sm:p-6 border shadow-xs flex flex-col justify-between h-full transition-colors"
                 :class="isDark ? 'bg-[#0d1424] border-slate-800' : 'bg-white border-slate-200/90'">
                <div>
                    <!-- Header Bar Petunjuk -->
                    <div class="flex items-center justify-between pb-3.5 border-b mb-4"
                         :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center"
                                 :class="isDark ? 'bg-blue-500/20 text-sky-400' : 'bg-blue-50 text-blue-600'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider"
                                :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                                Petunjuk Pengisian Data
                            </h3>
                        </div>
                        <span class="text-[11px] font-medium"
                              :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                            4 Tahap Singkat
                        </span>
                    </div>

                    <!-- 4 Langkah Simple & Mudah Dipahami -->
                    <div class="space-y-2.5">
                        
                        <!-- Langkah 1 -->
                        <div class="flex items-center gap-3 p-3 rounded-lg border transition-colors"
                             :class="isDark ? 'bg-[#131b2e]/60 border-slate-800' : 'bg-slate-50/80 border-slate-200/80'">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                1
                            </span>
                            <div class="flex-1 min-w-0 leading-tight">
                                <div class="text-xs font-semibold" :class="isDark ? 'text-white' : 'text-slate-900'">
                                    Buka Google Spreadsheet
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Klik tombol biru <strong>Buka Google Spreadsheet</strong> di sebelah kanan.
                                </p>
                            </div>
                        </div>

                        <!-- Langkah 2 -->
                        <div class="flex items-center gap-3 p-3 rounded-lg border transition-colors"
                             :class="isDark ? 'bg-[#131b2e]/60 border-slate-800' : 'bg-slate-50/80 border-slate-200/80'">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                2
                            </span>
                            <div class="flex-1 min-w-0 leading-tight">
                                <div class="text-xs font-semibold" :class="isDark ? 'text-white' : 'text-slate-900'">
                                    Isi Data pada Sheet Terkait
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Pilih sheet sesuai nomor tabel dan lengkapi data statistik yang diminta.
                                </p>
                            </div>
                        </div>

                        <!-- Langkah 3 -->
                        <div class="flex items-center gap-3 p-3 rounded-lg border transition-colors"
                             :class="isDark ? 'bg-[#131b2e]/60 border-slate-800' : 'bg-slate-50/80 border-slate-200/80'">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                3
                            </span>
                            <div class="flex-1 min-w-0 leading-tight">
                                <div class="text-xs font-semibold" :class="isDark ? 'text-white' : 'text-slate-900'">
                                    Konfirmasi "Tandai Selesai"
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Kembali ke portal ini, lalu klik <strong>Tandai Selesai</strong> pada tabel di bawah.
                                </p>
                            </div>
                        </div>

                        <!-- Langkah 4 -->
                        <div class="flex items-center gap-3 p-3 rounded-lg border transition-colors"
                             :class="isDark ? 'bg-[#131b2e]/60 border-slate-800' : 'bg-slate-50/80 border-slate-200/80'">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                4
                            </span>
                            <div class="flex-1 min-w-0 leading-tight">
                                <div class="text-xs font-semibold" :class="isDark ? 'text-white' : 'text-slate-900'">
                                    Verifikasi Pembina BPS
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    BPS memeriksa isian data hingga status berubah menjadi <strong>Diterima BPS</strong>.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer Petunjuk: Kontak Pembina -->
                <div class="mt-4 pt-3 border-t flex items-center justify-between text-[11px]"
                     :class="isDark ? 'border-slate-800 text-slate-400' : 'border-slate-100 text-slate-500'">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Koordinasi teknis:</span>
                    </span>
                    @if ($dinas->petugas_bps)
                        <span class="font-medium" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Pembina BPS · {{ $dinas->petugas_bps }}
                        </span>
                    @else
                        <span>Tim BPS Kab. Lampung Selatan</span>
                    @endif
                </div>
            </div>

            <!-- Card Kanan: Akses Google Spreadsheet & Rekap Progres Data -->
            <div class="rounded-xl p-5 sm:p-6 border shadow-xs flex flex-col justify-between h-full transition-colors"
                 :class="isDark ? 'bg-[#0d1424] border-slate-800' : 'bg-white border-slate-200/90'">
                <div>
                    <!-- Header Bar Card -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-medium text-slate-400">
                            Lembar kerja terintegrasi
                        </span>
                        @if (!empty($dinas->spreadsheet_url))
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium border"
                                  :class="isDark ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-emerald-50 text-emerald-700 border-emerald-200'">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Siap Diakses
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium border"
                                  :class="isDark ? 'bg-amber-500/15 text-amber-400 border-amber-500/30' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Mapping BPS
                            </span>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold tracking-tight"
                        :class="isDark ? 'text-white' : 'text-slate-900'">
                        Google Spreadsheet Instansi
                    </h3>
                    <p class="text-xs mt-1.5 leading-relaxed"
                       :class="isDark ? 'text-slate-400' : 'text-slate-600'">
                        Lembar kerja resmi memuat seluruh tabel statistik sektoral instansi <strong>{{ $dinas->nama_dinas }}</strong>. Klik tombol di bawah untuk membukanya langsung secara aman di dalam halaman portal ini.
                    </p>

                    <!-- 3 Kotak Ringkasan Statistik -->
                    <div class="mt-5 grid grid-cols-3 gap-2.5 sm:gap-3">
                        <div class="p-3 rounded-lg border transition-colors"
                             :class="isDark ? 'bg-[#131b2e] border-slate-800' : 'bg-slate-50 border-slate-200/90'">
                            <span class="text-[11px] font-medium text-slate-400">Ditugaskan</span>
                            <div class="text-base sm:text-lg font-bold mt-1 stat-nums"
                                 :class="isDark ? 'text-white' : 'text-slate-900'">
                                {{ $stats['total_tabel'] }} Tabel
                            </div>
                        </div>
                        <div class="p-3 rounded-lg border transition-colors"
                             :class="isDark ? 'bg-emerald-950/20 border-emerald-500/25' : 'bg-emerald-50/70 border-emerald-200/80'">
                            <span class="text-[11px] font-medium text-emerald-500">Telah Diisi</span>
                            <div class="text-base sm:text-lg font-bold mt-1 text-emerald-500 stat-nums">
                                {{ $stats['sudah_diisi'] }} Selesai
                            </div>
                        </div>
                        <div class="p-3 rounded-lg border transition-colors"
                             :class="isDark ? 'bg-blue-950/20 border-blue-500/25' : 'bg-blue-50/70 border-blue-200/80'">
                            <span class="text-[11px] font-medium text-blue-500">Verifikasi</span>
                            <div class="text-base sm:text-lg font-bold mt-1 stat-nums"
                                 :class="isDark ? 'text-sky-400' : 'text-blue-700'">
                                {{ $stats['data_masuk'] }} Diterima
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Akses Spreadsheet -->
                <div class="mt-6 pt-5 border-t"
                     :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                    @if (!empty($dinas->spreadsheet_url))
                        <a href="{{ route('dinas.embed') }}" 
                           class="w-full inline-flex items-center justify-center gap-2 py-3 px-5 font-semibold text-xs sm:text-sm rounded-lg shadow-sm transition btn-tactile bg-blue-600 hover:bg-blue-700 text-white focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka Google Spreadsheet</span>
                        </a>
                        <p class="text-center text-[11px] text-slate-400 mt-2">
                            Lembar kerja terbuka langsung di browser tanpa perlu berpindah tab atau aplikasi.
                        </p>
                    @else
                        <button disabled 
                                class="w-full py-3 px-5 font-medium text-xs sm:text-sm rounded-lg cursor-not-allowed"
                                :class="isDark ? 'bg-slate-800 text-slate-500' : 'bg-slate-100 text-slate-400'">
                            Spreadsheet Belum Tersedia
                        </button>
                        <p class="text-center text-xs text-amber-500 mt-2">
                            Mohon tunggu atau hubungi Admin BPS untuk memasang tautan lembar kerja instansi Anda.
                        </p>
                    @endif
                </div>
            </div>

        </div>

        <!-- ==================== 4. DAFTAR RINCIAN TABEL DATA LSDA 2026 ==================== -->
        <div class="rounded-xl border shadow-xs p-5 sm:p-6 space-y-5 transition-colors"
             :class="isDark ? 'bg-[#0d1424] border-slate-800' : 'bg-white border-slate-200/90'">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <div>
                    <h3 class="text-base font-bold flex items-center gap-2"
                        :class="isDark ? 'text-white' : 'text-slate-900'">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Daftar Rincian Tabel Data (LSDA 2026)</span>
                    </h3>
                    <p class="text-xs mt-1"
                       :class="isDark ? 'text-slate-400' : 'text-slate-600'">
                        Daftar tabel statistik sektoral yang ditugaskan kepada instansi Anda. Silakan tandai tombol status setelah Anda selesai menginput data ke Google Spreadsheet.
                    </p>
                </div>

                <!-- Search & Status Filter Controls -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" 
                               x-model="searchTabel" 
                               placeholder="Cari tabel, subjek, catatan..." 
                               class="text-xs rounded-lg pl-8 pr-3 py-2 w-48 sm:w-64 border transition focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                               :class="isDark 
                                  ? 'bg-[#131b2e] border-slate-700 text-white placeholder-slate-500 focus:border-blue-500' 
                                  : 'bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-blue-600'">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <select x-model="filterStatus" 
                            class="text-xs rounded-lg px-3 py-2 border transition focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                            :class="isDark 
                               ? 'bg-[#131b2e] border-slate-700 text-white focus:border-blue-500' 
                               : 'bg-white border-slate-300 text-slate-700 focus:border-blue-600'">
                        <option value="all">Semua Status</option>
                        <option value="sudah">Telah Ditandai Selesai</option>
                        <option value="belum">Belum Selesai Diisi</option>
                        <option value="diterima">Telah Diterima BPS</option>
                        <option value="catatan_dinas">Ada Catatan Dinas</option>
                    </select>
                </div>
            </div>

            <!-- Tabel Data List -->
            <div class="overflow-x-auto rounded-lg border"
                 :class="isDark ? 'border-slate-800' : 'border-slate-200'">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b font-semibold text-[11px]"
                            :class="isDark ? 'bg-[#131b2e] border-slate-800 text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'">
                            <th class="py-2.5 px-3 text-center w-12">No</th>
                            <th class="py-2.5 px-3 w-28">No. Tabel</th>
                            <th class="py-2.5 px-4 min-w-[200px]">Nama Tabel & Subjek</th>
                            <th class="py-2.5 px-4 w-60 min-w-[210px]">Catatan Dinas</th>
                            <th class="py-2.5 px-4 w-52 min-w-[180px]">Catatan / Revisi BPS</th>
                            <th class="py-2.5 px-4 text-center w-36">Verifikasi BPS</th>
                            <th class="py-2.5 px-4 text-center w-40">Aksi Pengisian Dinas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y transition-colors"
                           :class="isDark ? 'divide-slate-800 text-slate-200' : 'divide-slate-100 text-slate-700'">
                        @forelse ($tabels as $idx => $tabel)
                            <tr class="transition"
                                :class="isDark ? 'hover:bg-slate-800/30' : 'hover:bg-slate-50/80'"
                                x-show="(
                                    (filterStatus === 'all') ||
                                    (filterStatus === 'sudah' && '{{ $tabel->status_pengisian }}' === 'sudah_diisi') ||
                                    (filterStatus === 'belum' && '{{ $tabel->status_pengisian }}' !== 'sudah_diisi') ||
                                    (filterStatus === 'diterima' && {{ $tabel->data_masuk ? 'true' : 'false' }}) ||
                                    (filterStatus === 'catatan_dinas' && {{ !empty($tabel->catatan_dinas) ? 'true' : 'false' }})
                                ) && (
                                    !searchTabel || 
                                    '{{ strtolower(addslashes($tabel->nama_tabel)) }}'.includes(searchTabel.toLowerCase()) || 
                                    '{{ strtolower(addslashes($tabel->no_tabel ?? '')) }}'.includes(searchTabel.toLowerCase()) ||
                                    '{{ strtolower(addslashes($tabel->subjek_web ?? '')) }}'.includes(searchTabel.toLowerCase()) ||
                                    '{{ strtolower(addslashes($tabel->catatan_dinas ?? '')) }}'.includes(searchTabel.toLowerCase()) ||
                                    '{{ strtolower(addslashes($tabel->catatan_bps ?? '')) }}'.includes(searchTabel.toLowerCase())
                                )">
                                
                                <td class="py-3 px-3 text-center text-slate-400 font-medium stat-nums">
                                    {{ $idx + 1 }}
                                </td>

                                <td class="py-3 px-3">
                                    <div class="font-semibold font-mono stat-nums" :class="isDark ? 'text-white' : 'text-slate-900'">
                                        {{ $tabel->no_tabel ?? '-' }}
                                    </div>
                                    @if ($tabel->no_tabel_lsda2025)
                                        <div class="text-[10px] text-slate-400 stat-nums">2025: {{ $tabel->no_tabel_lsda2025 }}</div>
                                    @endif
                                </td>

                                <td class="py-3 px-4">
                                    <div class="font-medium leading-snug" :class="isDark ? 'text-white' : 'text-slate-900'">
                                        {{ $tabel->nama_tabel }}
                                    </div>
                                    @if ($tabel->subjek_web)
                                        <div class="mt-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border"
                                                  :class="isDark ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-blue-50 text-blue-700 border-blue-200'">
                                                {{ $tabel->subjek_web }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Catatan Dinas (Prominent & Clear) -->
                                <td class="py-3 px-4">
                                    @if ($tabel->catatan_dinas)
                                        <div @click="openCatatanModal({{ json_encode([
                                                 'id' => $tabel->id,
                                                 'nama' => $tabel->nama_tabel,
                                                 'catatan' => $tabel->catatan_dinas ?? ''
                                             ]) }})"
                                             title="Klik untuk mengubah catatan dinas"
                                             class="group cursor-pointer p-2.5 rounded-lg border transition hover:shadow-xs relative"
                                             :class="isDark 
                                                ? 'bg-blue-950/25 border-blue-800/50 hover:border-blue-600 text-slate-200' 
                                                : 'bg-blue-50/70 border-blue-200 hover:border-blue-300 text-slate-800'">
                                            <div class="flex items-center justify-between gap-1.5 mb-1">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                                    </svg>
                                                    Catatan Dinas
                                                </span>
                                                <span class="text-[10px] text-blue-600 dark:text-blue-400 opacity-60 group-hover:opacity-100 flex items-center gap-0.5 font-medium transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    <span>Ubah</span>
                                                </span>
                                            </div>
                                            <p class="text-xs font-normal leading-relaxed break-words line-clamp-3" title="{{ $tabel->catatan_dinas }}">
                                                {{ $tabel->catatan_dinas }}
                                            </p>
                                        </div>
                                    @else
                                        <button type="button"
                                                @click="openCatatanModal({{ json_encode([
                                                    'id' => $tabel->id,
                                                    'nama' => $tabel->nama_tabel,
                                                    'catatan' => ''
                                                ]) }})"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-dashed transition"
                                                :class="isDark 
                                                   ? 'border-slate-800 text-slate-400 hover:text-blue-400 hover:border-slate-700 hover:bg-slate-800/40' 
                                                   : 'border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50/40'"
                                                title="Tambah catatan untuk tabel ini">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span>Tambah Catatan</span>
                                        </button>
                                    @endif
                                </td>

                                <!-- Catatan/Revisi BPS -->
                                <td class="py-3 px-4">
                                    @if ($tabel->catatan_bps)
                                        <div class="p-2.5 rounded-lg border text-xs"
                                             :class="isDark ? 'bg-amber-950/20 border-amber-500/30 text-amber-200' : 'bg-amber-50 border-amber-200 text-amber-900'">
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                                    <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                    Catatan BPS
                                                </span>
                                            </div>
                                            <p class="text-xs font-normal leading-relaxed break-words line-clamp-3" title="{{ $tabel->catatan_bps }}">
                                                {{ $tabel->catatan_bps }}
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>

                                <!-- Status Verifikasi BPS (Data Masuk) -->
                                <td class="py-3 px-4 text-center">
                                    @if ($tabel->data_masuk)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium border"
                                              :class="isDark ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-emerald-50 text-emerald-700 border-emerald-200'">
                                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Diterima BPS</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium border"
                                              :class="isDark ? 'bg-slate-800 text-slate-400 border-slate-700' : 'bg-slate-100 text-slate-500 border-slate-200'">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Belum Diterima</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Tombol Aksi Pengisian Dinas (Interaktif) -->
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Form Toggle Status Selesai Diisi -->
                                        <form action="{{ route('dinas.tabel.toggle_status', $tabel->id) }}" method="POST">
                                            @csrf
                                            @if ($tabel->status_pengisian === 'sudah_diisi')
                                                <button type="submit" 
                                                        title="Klik untuk membatalkan tanda selesai"
                                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-rose-600 text-white rounded-lg text-xs font-medium shadow-xs transition btn-tactile flex items-center gap-1.5 group">
                                                    <svg class="w-3.5 h-3.5 group-hover:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span class="group-hover:hidden">Selesai</span>
                                                    <svg class="w-3.5 h-3.5 hidden group-hover:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span class="hidden group-hover:inline">Batalkan</span>
                                                </button>
                                            @else
                                                <button type="submit" 
                                                        title="Klik untuk menandai tabel ini telah Anda isi di spreadsheet"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-medium border transition btn-tactile flex items-center gap-1.5"
                                                        :class="isDark 
                                                           ? 'bg-[#131b2e] hover:bg-blue-600 hover:text-white text-slate-300 border-slate-700' 
                                                           : 'bg-white hover:bg-blue-600 hover:text-white text-slate-700 border-slate-200'">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    <span>Tandai Selesai</span>
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Tombol Tambah/Edit Catatan Mandiri -->
                                        <button type="button" 
                                                @click="openCatatanModal({{ json_encode([
                                                    'id' => $tabel->id,
                                                    'nama' => $tabel->nama_tabel,
                                                    'catatan' => $tabel->catatan_dinas ?? ''
                                                ]) }})"
                                                title="Tambah atau ubah catatan dinas untuk tabel ini"
                                                class="p-1.5 rounded-lg transition btn-tactile"
                                                :class="isDark ? 'text-slate-400 hover:text-white hover:bg-slate-800' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-100'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Belum ada daftar rincian tabel yang ditugaskan ke instansi Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- ==================== MODAL CATATAN MANDIRI TABEL ==================== -->
    <div x-show="modalCatatan" 
         x-transition 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.outside="modalCatatan = false" 
             class="rounded-xl shadow-xl max-w-md w-full p-6 border transition-colors"
             :class="isDark ? 'bg-[#0d1424] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-900'">
            <div class="flex items-center justify-between pb-3 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <h3 class="text-sm font-bold">Catatan Konfirmasi Tabel</h3>
                <button @click="modalCatatan = false" class="text-slate-400 hover:text-slate-200 btn-tactile">✕</button>
            </div>

            <form :action="'{{ url('portal/tabel') }}/' + activeTabel.id + '/catatan'" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <div class="text-xs text-slate-400 mb-1">Tabel:</div>
                    <div class="text-xs font-medium p-2.5 rounded-lg border leading-relaxed"
                         :class="isDark ? 'bg-[#131b2e] border-slate-700 text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                         x-text="activeTabel.nama"></div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Catatan dari Instansi Anda
                    </label>
                    <textarea name="catatan_dinas" 
                              rows="3" 
                              x-model="activeTabel.catatan" 
                              placeholder="Misal: Data tahun 2025 sudah diinput lengkap di sheet 3, atau menunggu data bidang X..." 
                              class="w-full px-3 py-2 rounded-lg text-xs border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                              :class="isDark ? 'bg-[#131b2e] border-slate-700 text-white focus:border-blue-500' : 'bg-white border-slate-300 text-slate-900 focus:border-blue-600'"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Catatan ini akan tersimpan dan dapat dibaca oleh tim BPS.</p>
                </div>

                <div class="pt-2 flex gap-2">
                    <button type="button" @click="modalCatatan = false" 
                            class="w-1/2 py-2 px-3 border text-xs font-medium rounded-lg btn-tactile"
                            :class="isDark ? 'border-slate-700 text-slate-300 hover:bg-slate-800' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2 px-3 font-semibold text-xs rounded-lg shadow-xs btn-tactile bg-blue-600 hover:bg-blue-700 text-white">
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL UPDATE PROFIL PIC & GANTI PIN ==================== -->
    <div x-show="modalProfile" 
         x-transition 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.outside="modalProfile = false" 
             class="rounded-xl shadow-xl max-w-md w-full p-6 border transition-colors"
             :class="isDark ? 'bg-[#0d1424] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-900'">
            <div class="flex items-center justify-between pb-3 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <div>
                    <h3 class="text-sm font-bold">Profil Penanggung Jawab (PIC) & PIN</h3>
                    <p class="text-[11px] text-slate-400">Perbarui identitas kontak atau ganti PIN akses dinas</p>
                </div>
                <button @click="modalProfile = false" class="text-slate-400 hover:text-slate-200 btn-tactile">✕</button>
            </div>

            <form action="{{ route('dinas.profile.update') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nama lengkap penanggung jawab (PIC) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="pic_name" value="{{ $dinas->pic_name }}" required 
                           placeholder="Contoh: Budi Santoso, S.Kom"
                           class="w-full px-3 py-2 rounded-lg text-xs border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                           :class="isDark ? 'bg-[#131b2e] border-slate-700 text-white focus:border-blue-500' : 'bg-white border-slate-300 text-slate-900 focus:border-blue-600'">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nomor WhatsApp / HP aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="pic_phone" value="{{ $dinas->pic_phone }}" required 
                           placeholder="Contoh: 081234567890"
                           class="w-full px-3 py-2 rounded-lg text-xs border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                           :class="isDark ? 'bg-[#131b2e] border-slate-700 text-white focus:border-blue-500' : 'bg-white border-slate-300 text-slate-900 focus:border-blue-600'">
                </div>

                <div class="pt-3 border-t"
                     :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                    <div class="text-xs font-semibold mb-2"
                         :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Ingin ganti PIN? (Kosongkan jika tidak ingin mengubah)
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-medium text-slate-400">PIN lama saat ini</label>
                            <input type="password" name="current_pin" placeholder="Masukkan PIN saat ini" 
                                   class="w-full px-3 py-2 rounded-lg text-xs border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                                   :class="isDark ? 'bg-[#131b2e] border-slate-700 text-white focus:border-blue-500' : 'bg-white border-slate-300 text-slate-900 focus:border-blue-600'">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-400">PIN baru (minimal 4 digit)</label>
                            <input type="password" name="new_pin" placeholder="Minimal 4 digit" 
                                   class="w-full px-3 py-2 rounded-lg text-xs border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                                   :class="isDark ? 'bg-[#131b2e] border-slate-700 text-white focus:border-blue-500' : 'bg-white border-slate-300 text-slate-900 focus:border-blue-600'">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-400">Konfirmasi PIN baru</label>
                            <input type="password" name="new_pin_confirmation" placeholder="Ketik ulang PIN baru" 
                                   class="w-full px-3 py-2 rounded-lg text-xs border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                                   :class="isDark ? 'bg-[#131b2e] border-slate-700 text-white focus:border-blue-500' : 'bg-white border-slate-300 text-slate-900 focus:border-blue-600'">
                        </div>
                    </div>
                </div>

                <div class="pt-3 flex gap-2">
                    <button type="button" @click="modalProfile = false" 
                            class="w-1/2 py-2 px-3 border text-xs font-medium rounded-lg btn-tactile"
                            :class="isDark ? 'border-slate-700 text-slate-300 hover:bg-slate-800' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2 px-3 font-semibold text-xs rounded-lg shadow-xs btn-tactile bg-blue-600 hover:bg-blue-700 text-white">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
