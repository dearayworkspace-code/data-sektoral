@extends('layouts.app')

@section('title', 'Dashboard Admin BPS - Mapping Data Dinas & LSDA 2026')

@section('content')
<div class="min-h-screen flex flex-col font-sans transition-colors duration-300"
     :class="isDark ? 'bg-[#070d1d] text-slate-100' : 'bg-slate-100/90 text-slate-800'"
     x-data="{
        modalAdd: false,
        modalImport: false,
        modalEdit: false,
        modalReset: false,
        modalTables: false,
        editData: { id: '', nama_dinas: '', spreadsheet_url: '', shortlink: '', petugas_bps: '', evaluasi: '', pic_name: '', pic_phone: '' },
        resetData: { id: '', nama_dinas: '' },
        viewingDinas: { id: '', nama_dinas: '', tables: [] },
        openEdit(item) {
            this.editData = { ...item };
            this.modalEdit = true;
        },
        openReset(item) {
            this.resetData = { id: item.id, nama_dinas: item.nama_dinas };
            this.modalReset = true;
        },
        openTables(item) {
            this.viewingDinas = {
                id: item.id,
                nama_dinas: item.nama_dinas,
                tables: item.tabels || []
            };
            this.modalTables = true;
        }
     }">

    <!-- Top Navigation Bar -->
    <header class="border-b sticky top-0 z-30 shadow-md transition-colors duration-300"
            :class="isDark ? 'bg-[#0c1427] border-slate-800 text-white' : 'bg-slate-900 border-slate-800 text-white'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center p-1 shadow-xs shrink-0 transition-colors"
                     :class="isDark ? 'bg-slate-800 border border-slate-700' : 'bg-slate-800 border border-slate-700'">
                    <img src="{{ asset('images/bps-logo.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-tight leading-none text-white">
                        Admin BPS - Data Dinas & LSDA 2026
                    </h1>
                    <span class="text-[11px] font-medium text-slate-400">Portal Pengelolaan, Mapping Spreadsheet & Monitoring Tabel</span>
                </div>
            </div>

            <div class="flex items-center gap-3 sm:gap-4 text-xs">
                <!-- Pill Switcher Mode Gelap / Terang -->
                <button type="button" 
                        @click="toggleTheme()" 
                        class="px-3 py-1.5 rounded-full border text-[11px] font-semibold flex items-center gap-1.5 shadow-sm transition transform active:scale-95 bg-slate-800/80 hover:bg-slate-700 text-white border-slate-700">
                    <span>Mode Gelap/Terang</span>
                    <span class="text-xs" x-text="isDark ? '🌙' : '☀️'"></span>
                </button>

                <span class="text-slate-300 hidden md:inline">
                    Hai, <strong class="text-white">{{ Auth::guard('admin')->user()->name }}</strong>
                </span>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-3 py-1.5 bg-slate-800 hover:bg-rose-900/60 hover:text-rose-200 text-slate-300 rounded-xl border border-slate-700 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- Stat Cards Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Dinas -->
            <div class="rounded-[2rem] p-5 sm:p-6 border shadow-xs transition-colors"
                 :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-white border-slate-200/80'">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Dinas</span>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                         :class="isDark ? 'bg-blue-500/15 text-blue-400' : 'bg-blue-50 text-blue-600'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold mt-2" :class="isDark ? 'text-white' : 'text-slate-800'">
                    {{ $stats['total'] }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Instansi terdaftar</div>
            </div>

            <!-- Sudah Isi PIC -->
            <div class="rounded-[2rem] p-5 sm:p-6 border shadow-xs transition-colors"
                 :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-white border-slate-200/80'">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-500">Sudah Aktif / PIC</span>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                         :class="isDark ? 'bg-emerald-500/15 text-emerald-400' : 'bg-emerald-50 text-emerald-600'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-emerald-500 mt-2">
                    {{ $stats['sudah_isi'] }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">PIC lengkap & terhubung</div>
            </div>

            <!-- Total Tabel LSDA 2026 -->
            <div class="rounded-[2rem] p-5 sm:p-6 border shadow-xs transition-colors"
                 :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-white border-slate-200/80'">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Tabel LSDA</span>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                         :class="isDark ? 'bg-indigo-500/15 text-indigo-400' : 'bg-indigo-50 text-indigo-600'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold mt-2" :class="isDark ? 'text-sky-400' : 'text-indigo-600'">
                    {{ $stats['total_tabel'] }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Tabel statistik sektoral</div>
            </div>

            <!-- Tabel Data Masuk BPS -->
            <div class="rounded-[2rem] p-5 sm:p-6 border shadow-xs transition-colors"
                 :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-white border-slate-200/80'">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-500">Tabel Diterima BPS</span>
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                         :class="isDark ? 'bg-teal-500/15 text-teal-400' : 'bg-teal-50 text-teal-600'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-teal-500 mt-2">
                    {{ $stats['tabel_data_masuk'] }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Status verifikasi: True</div>
            </div>
        </div>

        <!-- Action Bar: Search, Filters, and Action Buttons -->
        <div class="rounded-[2rem] p-4 sm:p-5 border shadow-xs flex flex-col md:flex-row items-center justify-between gap-4 transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-white border-slate-200/80'">
            
            <!-- Search & Filter Form -->
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto flex-1">
                <div class="relative flex-1 min-w-[240px]">
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Cari dinas, pembina BPS, shortlink, PIC..." 
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl focus:outline-none border transition"
                           :class="isDark 
                              ? 'bg-[#15203b] border-slate-700 text-white placeholder-slate-500 focus:border-sky-400' 
                              : 'bg-slate-50 border-slate-300 text-slate-800 placeholder-slate-400 focus:border-blue-500'">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <select name="status" 
                        onchange="this.form.submit()"
                        class="text-xs rounded-xl px-3 py-2 focus:outline-none border transition"
                        :class="isDark 
                           ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' 
                           : 'bg-slate-50 border-slate-300 text-slate-700 focus:border-blue-500'">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Sudah Aktif / PIC Lengkap</option>
                    <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Lengkapi PIC</option>
                    <option value="tanpa_link" {{ request('status') === 'tanpa_link' ? 'selected' : '' }}>Belum Ada Link Spreadsheet</option>
                    <option value="ada_evaluasi" {{ request('status') === 'ada_evaluasi' ? 'selected' : '' }}>Ada Catatan Evaluasi</option>
                </select>

                <button type="submit" 
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition"
                        :class="isDark ? 'bg-sky-500 hover:bg-sky-400 text-slate-950' : 'bg-slate-800 hover:bg-slate-700 text-white'">
                    Cari
                </button>
                @if (request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.dashboard') }}" class="text-xs text-rose-500 hover:underline px-1">Reset</a>
                @endif
            </form>

            <!-- Buttons: Add, Import, Template, Export -->
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
                <button @click="modalAdd = true" 
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Dinas</span>
                </button>

                <button @click="modalImport = true" 
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import LSDA 2026</span>
                </button>

                <a href="{{ route('admin.dinas.template') }}" 
                   class="px-3 py-2 text-xs font-bold rounded-xl border transition flex items-center gap-1.5"
                   :class="isDark 
                      ? 'bg-[#15203b] hover:bg-[#1a284a] text-slate-200 border-slate-700' 
                      : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'"
                   title="Unduh format template Excel/CSV resmi sesuai sheet LSDA2026">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Template LSDA2026</span>
                </a>

                <a href="{{ route('admin.dinas.export') }}" 
                   class="px-3 py-2 text-xs font-bold rounded-xl border transition flex items-center gap-1.5"
                   :class="isDark 
                      ? 'bg-[#15203b] hover:bg-[#1a284a] text-slate-200 border-slate-700' 
                      : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'"
                   title="Ekspor seluruh rekap data dinas, tabel, dan PIC ke CSV">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Ekspor CSV</span>
                </a>
            </div>
        </div>

        <!-- Dinas Data Table -->
        <div class="rounded-[2rem] border shadow-xs overflow-hidden transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-white border-slate-200/80'">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b uppercase font-bold tracking-wider"
                            :class="isDark ? 'bg-[#15203b] border-slate-800 text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-600'">
                            <th class="py-3.5 px-3 w-10 text-center">No</th>
                            <th class="py-3.5 px-4 min-w-[220px]">Nama Dinas / Instansi</th>
                            <th class="py-3.5 px-3 w-28">Pembina BPS</th>
                            <th class="py-3.5 px-4 min-w-[180px]">Link Spreadsheet</th>
                            <th class="py-3.5 px-3 text-center w-28">Tabel LSDA</th>
                            <th class="py-3.5 px-4 w-28">PIN Akses</th>
                            <th class="py-3.5 px-4 min-w-[160px]">Kontak PIC</th>
                            <th class="py-3.5 px-3 text-center w-24">Status</th>
                            <th class="py-3.5 px-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y transition-colors"
                           :class="isDark ? 'divide-slate-800 text-slate-200' : 'divide-slate-100 text-slate-700'">
                        @forelse ($dinasList as $index => $item)
                            <tr class="transition"
                                :class="isDark ? 'hover:bg-slate-800/40' : 'hover:bg-slate-50/80'"
                                x-data="{ showPin: false }">
                                <td class="py-3.5 px-3 text-center text-slate-400 font-medium">
                                    {{ $dinasList->firstItem() + $index }}
                                </td>

                                <!-- Nama Dinas & Shortlink -->
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold leading-snug"
                                         :class="isDark ? 'text-white' : 'text-slate-900'">
                                        {{ $item->nama_dinas }}
                                    </div>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        @if ($item->shortlink)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono border"
                                                  :class="isDark ? 'bg-slate-800 text-slate-300 border-slate-700' : 'bg-slate-100 text-slate-600 border-slate-200'">
                                                {{ $item->shortlink }}
                                            </span>
                                        @endif
                                        @if ($item->evaluasi)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border"
                                                  :class="isDark ? 'bg-amber-950/30 text-amber-300 border-amber-500/30' : 'bg-amber-50 text-amber-700 border-amber-200'"
                                                  title="{{ $item->evaluasi }}">
                                                Ada Evaluasi
                                            </span>
                                        @endif
                                    </div>
                                    @if ($item->last_login_at)
                                        <div class="text-[10px] text-slate-400 mt-0.5">Login: {{ $item->last_login_at->format('d/m/Y H:i') }}</div>
                                    @else
                                        <div class="text-[10px] text-amber-500 mt-0.5">Belum login</div>
                                    @endif
                                </td>

                                <!-- Pembina BPS -->
                                <td class="py-3.5 px-3 font-bold"
                                    :class="isDark ? 'text-slate-200' : 'text-slate-700'">
                                    {{ $item->petugas_bps ?? '-' }}
                                </td>

                                <!-- Link Spreadsheet -->
                                <td class="py-3.5 px-4">
                                    @if (!empty($item->spreadsheet_url))
                                        <div class="flex items-center gap-1.5 text-blue-500">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                            <a href="{{ $item->spreadsheet_url }}" target="_blank" class="max-w-[170px] truncate text-[11px] font-medium hover:underline" title="{{ $item->spreadsheet_url }}">
                                                {{ $item->spreadsheet_url }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-rose-500 italic text-[11px] font-medium">Belum dimapping</span>
                                    @endif
                                </td>

                                <!-- Rincian Tabel LSDA (Tombol Modal) -->
                                <td class="py-3.5 px-3 text-center">
                                    @php
                                        $tCount = $item->tabels->count();
                                        $masukCount = $item->tabels->where('data_masuk', true)->count();
                                    @endphp
                                    <button type="button" 
                                            @click="openTables({{ json_encode($item) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl font-bold text-[11px] border transition"
                                            :class="isDark 
                                               ? 'bg-blue-950/40 hover:bg-blue-900/50 text-sky-400 border-blue-800/50' 
                                               : 'bg-blue-50 hover:bg-blue-100 text-blue-700 border-blue-200'"
                                            title="Klik untuk melihat dan memverifikasi rincian tabel">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <span>{{ $tCount }} Tabel</span>
                                        <span class="text-[10px] font-normal" :class="isDark ? 'text-sky-300' : 'text-blue-500'">({{ $masukCount }})</span>
                                    </button>
                                </td>

                                <!-- Kolom PIN -->
                                <td class="py-3.5 px-4">
                                    <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-xl border"
                                         :class="isDark ? 'bg-[#15203b] border-slate-700' : 'bg-slate-100 border-slate-200'">
                                        <span class="font-mono font-bold tracking-wider text-xs" 
                                              x-text="showPin ? '{{ $item->plain_pin }}' : '••••••'">
                                        </span>
                                        <button type="button" 
                                                @click="showPin = !showPin" 
                                                class="text-slate-400 hover:text-white transition"
                                                :title="showPin ? 'Sembunyikan PIN' : 'Lihat PIN asli'">
                                            <svg x-show="!showPin" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showPin" class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </td>

                                <!-- Kontak PIC -->
                                <td class="py-3.5 px-4">
                                    @if ($item->pic_name)
                                        <div class="font-bold" :class="isDark ? 'text-white' : 'text-slate-800'">{{ $item->pic_name }}</div>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                            <span>{{ $item->pic_phone }}</span>
                                            @php
                                                $waClean = preg_replace('/[^0-9]/', '', $item->pic_phone);
                                                if (str_starts_with($waClean, '0')) {
                                                    $waClean = '62' . substr($waClean, 1);
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $waClean }}" target="_blank" class="text-emerald-500 hover:text-emerald-400 font-bold" title="Chat via WhatsApp">
                                                [WA]
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-slate-500 italic">Belum mengisi</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-3 text-center">
                                    @if (!$item->is_first_login)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border"
                                              :class="isDark ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-emerald-50 text-emerald-700 border-emerald-200'">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border"
                                              :class="isDark ? 'bg-amber-500/15 text-amber-400 border-amber-500/30' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Belum
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Edit -->
                                        <button type="button" 
                                                @click="openEdit({{ json_encode([
                                                    'id' => (string) $item->id,
                                                    'nama_dinas' => (string) $item->nama_dinas,
                                                    'spreadsheet_url' => (string) ($item->spreadsheet_url ?? ''),
                                                    'shortlink' => (string) ($item->shortlink ?? ''),
                                                    'petugas_bps' => (string) ($item->petugas_bps ?? ''),
                                                    'evaluasi' => (string) ($item->evaluasi ?? ''),
                                                    'pic_name' => (string) ($item->pic_name ?? ''),
                                                    'pic_phone' => (string) ($item->pic_phone ?? '')
                                                ]) }})"
                                                title="Edit Data Dinas"
                                                class="p-1.5 rounded-xl transition"
                                                :class="isDark ? 'text-slate-400 hover:text-sky-400 hover:bg-slate-800' : 'text-slate-500 hover:text-blue-600 hover:bg-blue-50'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>

                                        <!-- Reset PIN -->
                                        <button type="button" 
                                                @click="openReset({{ json_encode([
                                                    'id' => (string) $item->id,
                                                    'nama_dinas' => (string) $item->nama_dinas
                                                ]) }})"
                                                title="Reset PIN"
                                                class="p-1.5 rounded-xl transition"
                                                :class="isDark ? 'text-slate-400 hover:text-amber-400 hover:bg-slate-800' : 'text-slate-500 hover:text-amber-600 hover:bg-amber-50'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        </button>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.dinas.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data dinas ini beserta rincian tabelnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Hapus Dinas"
                                                    class="p-1.5 rounded-xl transition"
                                                    :class="isDark ? 'text-slate-400 hover:text-rose-400 hover:bg-slate-800' : 'text-slate-500 hover:text-rose-600 hover:bg-rose-50'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">
                                    Tidak ada data dinas yang sesuai dengan pencarian atau filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if ($dinasList->hasPages())
                <div class="px-4 py-3 border-t transition-colors"
                     :class="isDark ? 'border-slate-800 bg-[#15203b]/40' : 'border-slate-100 bg-slate-50/50'">
                    {{ $dinasList->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- ==================== MODAL RINCIAN TABEL DINAS ==================== -->
    <div x-show="modalTables" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.outside="modalTables = false" 
             class="rounded-[2rem] shadow-2xl max-w-4xl w-full p-6 sm:p-7 border max-h-[85vh] flex flex-col transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-800'">
            <div class="flex items-center justify-between pb-3 border-b shrink-0"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <div>
                    <h3 class="text-base font-extrabold" x-text="viewingDinas.nama_dinas"></h3>
                    <p class="text-xs text-slate-400">Daftar rincian tabel statistik sektoral & status verifikasi BPS</p>
                </div>
                <button @click="modalTables = false" class="text-slate-400 hover:text-white text-lg">✕</button>
            </div>

            <div class="overflow-y-auto flex-1 mt-4">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b uppercase font-bold sticky top-0"
                            :class="isDark ? 'bg-[#15203b] border-slate-800 text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-600'">
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <th class="py-2.5 px-3 w-24">No Tabel</th>
                            <th class="py-2.5 px-4">Nama Tabel & Subjek</th>
                            <th class="py-2.5 px-3">Catatan BPS</th>
                            <th class="py-2.5 px-3 text-center w-28">Status Dinas</th>
                            <th class="py-2.5 px-3 text-center w-36">Data Masuk BPS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y transition-colors"
                           :class="isDark ? 'divide-slate-800 text-slate-200' : 'divide-slate-100 text-slate-700'">
                        <template x-for="(tabel, tIdx) in viewingDinas.tables" :key="tabel.id">
                            <tr :class="isDark ? 'hover:bg-slate-800/40' : 'hover:bg-slate-50'">
                                <td class="py-2.5 px-3 text-center text-slate-400" x-text="tIdx + 1"></td>
                                <td class="py-2.5 px-3 font-mono font-bold" :class="isDark ? 'text-white' : 'text-slate-800'" x-text="tabel.no_tabel || '-'"></td>
                                <td class="py-2.5 px-4">
                                    <div class="font-bold" :class="isDark ? 'text-white' : 'text-slate-900'" x-text="tabel.nama_tabel"></div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span x-show="tabel.subjek_web" class="text-[10px] font-semibold text-blue-500" x-text="tabel.subjek_web"></span>
                                        <span x-show="tabel.catatan_dinas" class="text-[10px] text-slate-400 italic" x-text="'Catatan dinas: ' + tabel.catatan_dinas"></span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span x-show="tabel.catatan_bps" class="text-[11px] px-2 py-0.5 rounded-lg border font-medium"
                                          :class="isDark ? 'bg-amber-950/30 text-amber-300 border-amber-500/30' : 'bg-amber-50 text-amber-800 border-amber-200'"
                                          x-text="tabel.catatan_bps"></span>
                                    <span x-show="!tabel.catatan_bps" class="text-slate-500">-</span>
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <span x-show="tabel.status_pengisian === 'sudah_diisi'" class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                          :class="isDark ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-emerald-50 text-emerald-700 border-emerald-200'">
                                        ✓ Sudah Diisi
                                    </span>
                                    <span x-show="tabel.status_pengisian !== 'sudah_diisi'" class="px-2 py-0.5 rounded-full text-[10px] font-medium border"
                                          :class="isDark ? 'bg-slate-800 text-slate-400 border-slate-700' : 'bg-slate-100 text-slate-500 border-slate-200'">
                                        Belum
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <form :action="'{{ url('admin/tabel') }}/' + tabel.id + '/toggle-data-masuk'" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="px-2.5 py-1 rounded-xl text-xs font-bold transition shadow-xs"
                                                :class="tabel.data_masuk 
                                                    ? 'bg-emerald-600 text-white hover:bg-emerald-700' 
                                                    : (isDark ? 'bg-[#15203b] text-slate-300 hover:bg-sky-500 hover:text-slate-950 border border-slate-700' : 'bg-slate-200 text-slate-700 hover:bg-blue-600 hover:text-white')">
                                            <span x-text="tabel.data_masuk ? '✓ Diterima BPS' : 'Tandai Diterima'"></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!viewingDinas.tables || viewingDinas.tables.length === 0">
                            <td colspan="6" class="py-6 text-center text-slate-400">
                                Belum ada rincian tabel untuk instansi ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pt-3 border-t mt-2 flex justify-end"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <button type="button" @click="modalTables = false" 
                        class="py-2 px-4 rounded-xl text-xs font-bold transition border"
                        :class="isDark ? 'bg-slate-800 hover:bg-slate-700 text-white border-slate-700' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL TAMBAH DINAS ==================== -->
    <div x-show="modalAdd" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.outside="modalAdd = false" 
             class="rounded-[2rem] shadow-2xl max-w-lg w-full p-6 sm:p-7 border max-h-[90vh] overflow-y-auto transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-800'">
            <div class="flex items-center justify-between pb-3 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <h3 class="text-base font-extrabold">Tambah Dinas Baru</h3>
                <button @click="modalAdd = false" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form action="{{ route('admin.dinas.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nama Dinas / Instansi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_dinas" required placeholder="Contoh: Dinas Pariwisata Dan Kebudayaan" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                           :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Tautan Google Spreadsheet
                    </label>
                    <input type="url" name="spreadsheet_url" placeholder="https://docs.google.com/spreadsheets/d/..." 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                           :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Shortlink (WORD)
                        </label>
                        <input type="text" name="shortlink" placeholder="s.bps.go.id/LSDA_..." 
                               class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                               :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Pembina BPS
                        </label>
                        <input type="text" name="petugas_bps" placeholder="Contoh: INDRI / TIM IPDS" 
                               class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                               :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Catatan Evaluasi BPS
                    </label>
                    <textarea name="evaluasi" rows="2" placeholder="Catatan evaluasi untuk dinas..." 
                              class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                              :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        PIN Default
                    </label>
                    <input type="text" name="pin" value="123456" placeholder="Default: 123456" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border font-mono"
                           :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                </div>
                <div class="pt-3 flex gap-2">
                    <button type="button" @click="modalAdd = false" 
                            class="w-1/2 py-2.5 px-3 border text-xs font-semibold rounded-xl"
                            :class="isDark ? 'border-slate-700 text-slate-300 hover:bg-slate-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 px-3 font-bold text-xs rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Simpan Dinas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL EDIT DINAS ==================== -->
    <div x-show="modalEdit" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.outside="modalEdit = false" 
             class="rounded-[2rem] shadow-2xl max-w-lg w-full p-6 sm:p-7 border max-h-[90vh] overflow-y-auto transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-800'">
            <div class="flex items-center justify-between pb-3 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <h3 class="text-base font-extrabold">Edit Data Dinas</h3>
                <button @click="modalEdit = false" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form :action="'{{ url('admin/dinas') }}/' + editData.id" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nama Dinas <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_dinas" x-model="editData.nama_dinas" required 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                           :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Link Google Spreadsheet
                    </label>
                    <input type="url" name="spreadsheet_url" x-model="editData.spreadsheet_url" placeholder="https://docs.google.com/spreadsheets/d/..." 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                           :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Shortlink (WORD)
                        </label>
                        <input type="text" name="shortlink" x-model="editData.shortlink" 
                               class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                               :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Pembina BPS
                        </label>
                        <input type="text" name="petugas_bps" x-model="editData.petugas_bps" 
                               class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                               :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Catatan Evaluasi
                    </label>
                    <textarea name="evaluasi" rows="2" x-model="editData.evaluasi" 
                              class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                              :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Nama PIC
                        </label>
                        <input type="text" name="pic_name" x-model="editData.pic_name" 
                               class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                               :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            No. WhatsApp PIC
                        </label>
                        <input type="text" name="pic_phone" x-model="editData.pic_phone" 
                               class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                               :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                    </div>
                </div>
                <div class="pt-3 flex gap-2">
                    <button type="button" @click="modalEdit = false" 
                            class="w-1/2 py-2.5 px-3 border text-xs font-semibold rounded-xl"
                            :class="isDark ? 'border-slate-700 text-slate-300 hover:bg-slate-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 px-3 font-bold text-xs rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL RESET PIN ==================== -->
    <div x-show="modalReset" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.outside="modalReset = false" 
             class="rounded-[2rem] shadow-2xl max-w-sm w-full p-6 sm:p-7 border transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-800'">
            <div class="flex items-center justify-between pb-3 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <h3 class="text-base font-extrabold">Reset PIN Dinas</h3>
                <button @click="modalReset = false" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form :action="'{{ url('admin/dinas') }}/' + resetData.id + '/reset-pin'" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <div class="text-xs text-slate-400 mb-2">Instansi: <strong :class="isDark ? 'text-white' : 'text-slate-800'" x-text="resetData.nama_dinas"></strong></div>
                    <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        PIN Baru <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="pin" value="123456" required 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs font-mono focus:outline-none border"
                           :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-slate-50 border-slate-300 text-slate-800 focus:border-blue-500'">
                    <p class="text-[11px] text-slate-400 mt-1">PIN baru ini akan langsung aktif dan dapat dilihat di tabel dashboard.</p>
                </div>
                <div class="pt-3 flex gap-2">
                    <button type="button" @click="modalReset = false" 
                            class="w-1/2 py-2 px-3 border text-xs font-semibold rounded-xl"
                            :class="isDark ? 'border-slate-700 text-slate-300 hover:bg-slate-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2 px-3 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-sm">
                        Reset PIN
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL IMPORT MASSAL LSDA 2026 ==================== -->
    <div x-show="modalImport" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.outside="modalImport = false" 
             class="rounded-[2rem] shadow-2xl max-w-lg w-full p-6 sm:p-7 border transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-800'">
            <div class="flex items-center justify-between pb-3 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <h3 class="text-base font-extrabold">Import Massal Template LSDA 2026</h3>
                <button @click="modalImport = false" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form action="{{ route('admin.dinas.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div class="p-4 rounded-2xl border text-xs space-y-1.5 transition-colors"
                     :class="isDark ? 'bg-blue-950/30 border-blue-900/50 text-blue-200' : 'bg-blue-50 border-blue-100 text-blue-950'">
                    <div class="font-bold flex items-center gap-1.5" :class="isDark ? 'text-sky-300' : 'text-blue-800'">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Mendukung Format Resmi Sheet LSDA2026</span>
                    </div>
                    <p class="leading-relaxed text-[11px]">
                        Anda dapat langsung mengunggah berkas Excel (<strong>.xlsx</strong>) atau <strong>.csv</strong> dari sheet <strong>LSDA2026</strong>. Sistem secara otomatis membaca:
                    </p>
                    <ul class="list-disc list-inside text-[11px] space-y-0.5">
                        <li>Nama Dinas & Link Spreadsheet Instansi</li>
                        <li>Kontak PIC dari <strong>CONTACT PERSON 2026</strong> (Otomatis diekstrak)</li>
                        <li>Petugas Pembina BPS, Shortlink & Catatan Evaluasi</li>
                        <li>Seluruh rincian tabel statistik sektoral instansi</li>
                    </ul>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Pilih Berkas Excel / CSV <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="file" accept=".csv,.xlsx,.xls,.txt" required 
                           class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="pt-3 flex gap-2">
                    <button type="button" @click="modalImport = false" 
                            class="w-1/2 py-2.5 px-3 border text-xs font-semibold rounded-xl"
                            :class="isDark ? 'border-slate-700 text-slate-300 hover:bg-slate-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm">
                        Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
