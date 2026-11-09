@extends('layouts.app')

@section('title', 'Portal Data Dinas - ' . $dinas->nama_dinas)

@section('content')
<div class="min-h-screen flex flex-col font-sans transition-colors duration-300"
     :class="isDark ? 'bg-[#070d1d] text-slate-100' : 'bg-slate-100/90 text-slate-800'"
     x-data="{ 
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
    <header class="border-b sticky top-0 z-30 shadow-xs transition-colors duration-300"
            :class="isDark ? 'bg-[#0c1427] border-slate-800 text-white' : 'bg-white border-slate-200/80 text-slate-800'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center p-1 shadow-sm shrink-0 transition-colors"
                     :class="isDark ? 'bg-slate-800/80 border border-slate-700/80' : 'bg-slate-50 border border-slate-200/90'">
                    <img src="{{ asset('images/bps-logo.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-tight leading-none"
                        :class="isDark ? 'text-white' : 'text-slate-900'">
                        Portal Data Dinas
                    </h1>
                    <span class="text-[11px] font-medium text-slate-400">Badan Pusat Statistik · Kab. Lampung Selatan</span>
                </div>
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Pill Switcher Mode Gelap / Terang -->
                <button type="button" 
                        @click="toggleTheme()" 
                        class="px-3 py-1.5 rounded-full border text-[11px] font-semibold flex items-center gap-1.5 shadow-sm transition transform active:scale-95"
                        :class="isDark 
                            ? 'bg-slate-800/90 hover:bg-slate-700 text-white border-slate-700' 
                            : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300/80'">
                    <span>Mode Gelap/Terang</span>
                    <span class="text-xs" x-text="isDark ? '🌙' : '☀️'"></span>
                </button>

                <div class="hidden md:block text-right">
                    <div class="text-xs font-bold max-w-[240px] truncate"
                         :class="isDark ? 'text-white' : 'text-slate-800'">
                        {{ $dinas->nama_dinas }}
                    </div>
                    <div class="text-[11px] text-emerald-500 font-semibold flex items-center justify-end gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Akun Terhubung
                    </div>
                </div>

                <form action="{{ route('dinas.logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            title="Keluar dari sesi"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl transition flex items-center gap-1.5 border"
                            :class="isDark 
                                ? 'bg-slate-800/80 hover:bg-rose-950/60 text-slate-300 hover:text-rose-300 border-slate-700' 
                                : 'bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border-slate-200'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- Welcome Banner with BPS Metadata -->
        <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-[2rem] p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-white/10">
            <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-radial from-blue-500/10 to-transparent pointer-events-none"></div>
            
            <div class="relative z-10 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-blue-500/20 text-blue-200 text-xs font-semibold">
                        Instansi / OPD Lampung Selatan
                    </span>
                    @if ($dinas->shortlink)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-white/10 text-slate-200 text-xs font-mono">
                            {{ $dinas->shortlink }}
                        </span>
                    @endif
                    @if ($dinas->petugas_bps)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-xs font-semibold">
                            Pembina BPS: <strong>{{ $dinas->petugas_bps }}</strong>
                        </span>
                    @endif
                </div>

                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $dinas->nama_dinas }}</h2>
                <p class="text-blue-100/80 text-xs sm:text-sm mt-2 leading-relaxed">
                    Portal pengumpulan dan sinkronisasi data publikasi <strong>Lampung Selatan Dalam Angka (LSDA 2026)</strong>. Silakan periksa daftar tabel di bawah, isi melalui Google Spreadsheet, dan tandai jika pengisian selesai.
                </p>
            </div>
        </div>

        <!-- Catatan Evaluasi dari BPS (Jika Ada) -->
        @if ($dinas->evaluasi)
            <div class="p-4 sm:p-5 rounded-2xl border flex items-start gap-3.5 transition-colors"
                 :class="isDark 
                    ? 'bg-amber-950/30 border-amber-500/40 text-amber-200' 
                    : 'bg-amber-50 border-amber-300 text-amber-900'">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                     :class="isDark ? 'bg-amber-500/20 text-amber-400' : 'bg-amber-100 text-amber-700'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xs font-bold uppercase tracking-wider"
                        :class="isDark ? 'text-amber-300' : 'text-amber-900'">
                        Catatan Evaluasi / Arahan dari Pembina BPS
                    </h3>
                    <p class="text-xs sm:text-sm mt-1 font-medium leading-relaxed"
                       :class="isDark ? 'text-amber-200' : 'text-amber-800'">
                        {{ $dinas->evaluasi }}
                    </p>
                </div>
            </div>
        @endif

        <!-- 2 Column Content: PIC Info & Spreadsheet Access -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left: PIC & Profile Card (1 Col) -->
            <div class="space-y-6">
                <div class="rounded-[2rem] p-6 border shadow-xs transition-colors"
                     :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-white border-slate-200/80'">
                    <div class="flex items-center justify-between pb-4 border-b"
                         :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                        <h3 class="text-xs font-bold uppercase tracking-wider"
                            :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Penanggung Jawab (PIC)
                        </h3>
                        <button @click="modalProfile = true" class="text-xs font-bold hover:underline"
                                :class="isDark ? 'text-sky-400 hover:text-sky-300' : 'text-blue-600 hover:text-blue-700'">
                            Ubah
                        </button>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                 :class="isDark ? 'bg-blue-500/15 text-blue-400' : 'bg-blue-50 text-blue-600'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-400">Nama PIC</div>
                                <div class="text-xs sm:text-sm font-bold"
                                     :class="isDark ? 'text-white' : 'text-slate-800'">
                                    {{ $dinas->pic_name ?? 'Belum diisi' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                 :class="isDark ? 'bg-emerald-500/15 text-emerald-400' : 'bg-emerald-50 text-emerald-600'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-400">WhatsApp / Telepon</div>
                                <div class="text-xs sm:text-sm font-bold"
                                     :class="isDark ? 'text-white' : 'text-slate-800'">
                                    {{ $dinas->pic_phone ?? 'Belum diisi' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t"
                         :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                        <button @click="modalProfile = true" 
                                class="w-full py-2 px-3 text-xs font-semibold rounded-xl transition flex items-center justify-center gap-1.5 border"
                                :class="isDark 
                                    ? 'bg-[#15203b] hover:bg-[#1a284a] text-slate-200 border-slate-700' 
                                    : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Perbarui Profil & Ganti PIN</span>
                        </button>
                    </div>
                </div>

                <!-- Petunjuk Alur Pengisian -->
                <div class="rounded-[2rem] p-5 border transition-colors"
                     :class="isDark ? 'bg-blue-950/20 border-blue-900/40 text-blue-200' : 'bg-blue-50/70 border-blue-100 text-blue-950/80'">
                    <h4 class="text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5"
                        :class="isDark ? 'text-sky-300' : 'text-blue-900'">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Petunjuk Koordinasi BPS</span>
                    </h4>
                    <ol class="list-decimal list-inside text-xs space-y-1.5 leading-relaxed">
                        <li>Buka spreadsheet menggunakan tombol <strong>Buka Spreadsheet</strong>.</li>
                        <li>Isi setiap sheet yang sesuai dengan rincian tabel di bawah.</li>
                        <li>Setelah mengisi, tandai tombol <strong>Tandai Selesai Diisi</strong> pada tabel terkait.</li>
                        <li>Tim BPS akan memverifikasi dan mengubah status menjadi <strong>Diterima BPS</strong>.</li>
                    </ol>
                </div>
            </div>

            <!-- Right: Action CTA & Spreadsheet Access (2 Cols) -->
            <div class="lg:col-span-2">
                <div class="rounded-[2rem] p-6 sm:p-8 border shadow-xs flex flex-col justify-between h-full transition-colors"
                     :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-white border-slate-200/80'">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Lembar Kerja Terintegrasi
                            </span>
                            @if (!empty($dinas->spreadsheet_url))
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                      :class="isDark ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-emerald-50 text-emerald-700 border-emerald-200'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Siap Diakses
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                      :class="isDark ? 'bg-amber-500/15 text-amber-400 border-amber-500/30' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Mapping BPS
                                </span>
                            @endif
                        </div>

                        <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight"
                            :class="isDark ? 'text-white' : 'text-slate-800'">
                            Google Spreadsheet Instansi
                        </h3>
                        <p class="text-xs sm:text-sm mt-2 leading-relaxed"
                           :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                            Lembar kerja ini memuat seluruh tabel data sektoral instansi <strong>{{ $dinas->nama_dinas }}</strong>. Ketika Anda menekan tombol di bawah, lembar kerja akan langsung dimuat secara aman di dalam halaman portal ini.
                        </p>

                        <!-- Preview Info & Summary Box -->
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3.5 rounded-2xl border transition-colors"
                                 :class="isDark ? 'bg-[#15203b]/80 border-slate-700' : 'bg-slate-50 border-slate-200/80'">
                                <span class="text-[11px] font-semibold text-slate-400 uppercase">Tabel Ditugaskan</span>
                                <div class="text-xl font-extrabold mt-1"
                                     :class="isDark ? 'text-white' : 'text-slate-800'">
                                    {{ $stats['total_tabel'] }} Tabel
                                </div>
                            </div>
                            <div class="p-3.5 rounded-2xl border transition-colors"
                                 :class="isDark ? 'bg-emerald-950/25 border-emerald-500/30' : 'bg-emerald-50/70 border-emerald-200/80'">
                                <span class="text-[11px] font-semibold text-emerald-500 uppercase">Telah Anda Tandai</span>
                                <div class="text-xl font-extrabold mt-1 text-emerald-500">
                                    {{ $stats['sudah_diisi'] }} Selesai
                                </div>
                            </div>
                            <div class="p-3.5 rounded-2xl border transition-colors"
                                 :class="isDark ? 'bg-blue-950/25 border-blue-500/30' : 'bg-blue-50/70 border-blue-200/80'">
                                <span class="text-[11px] font-semibold text-blue-400 uppercase">Verifikasi BPS</span>
                                <div class="text-xl font-extrabold mt-1"
                                     :class="isDark ? 'text-sky-400' : 'text-blue-700'">
                                    {{ $stats['data_masuk'] }} Diterima
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Action Button -->
                    <div class="mt-8 pt-6 border-t"
                         :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                        @if (!empty($dinas->spreadsheet_url))
                            <a href="{{ route('dinas.embed') }}" 
                               class="w-full inline-flex items-center justify-center gap-2 py-4 px-6 font-extrabold text-sm sm:text-base rounded-2xl shadow-xl transition transform active:scale-[0.99]"
                               :class="isDark 
                                  ? 'bg-sky-500 hover:bg-sky-400 text-slate-950 shadow-sky-500/20' 
                                  : 'bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 text-white shadow-blue-500/25'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Buka Google Spreadsheet</span>
                            </a>
                            <p class="text-center text-[11px] text-slate-400 mt-2">
                                Lembar kerja terbuka langsung di browser tanpa perlu berpindah tab atau aplikasi.
                            </p>
                        @else
                            <button disabled 
                                    class="w-full py-3.5 px-6 font-bold text-sm rounded-2xl cursor-not-allowed"
                                    :class="isDark ? 'bg-slate-800 text-slate-500' : 'bg-slate-200 text-slate-400'">
                                Spreadsheet Belum Tersedia
                            </button>
                            <p class="text-center text-xs text-amber-500 mt-2">
                                Mohon tunggu atau hubungi Admin BPS untuk memasang tautan lembar kerja instansi Anda.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== DAFTAR RINCIAN TABEL DATA LSDA 2026 ==================== -->
        <div class="rounded-[2rem] border shadow-xs p-6 space-y-6 transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800' : 'bg-white border-slate-200/80'">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <div>
                    <h3 class="text-lg font-extrabold flex items-center gap-2"
                        :class="isDark ? 'text-white' : 'text-slate-900'">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Daftar Rincian Tabel Data (LSDA 2026)</span>
                    </h3>
                    <p class="text-xs mt-1"
                       :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                        Daftar tabel statistik sektoral yang ditugaskan kepada instansi Anda. Silakan tandai tombol status setelah Anda selesai menginput data ke Google Spreadsheet.
                    </p>
                </div>

                <!-- Search & Status Filter Controls -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" 
                               x-model="searchTabel" 
                               placeholder="Cari nama atau nomor tabel..." 
                               class="text-xs rounded-xl pl-8 pr-3 py-2 w-48 sm:w-64 focus:outline-none border transition"
                               :class="isDark 
                                  ? 'bg-[#15203b] border-slate-700 text-white placeholder-slate-500 focus:border-sky-400' 
                                  : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400 focus:border-blue-500'">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <select x-model="filterStatus" 
                            class="text-xs rounded-xl px-3 py-2 focus:outline-none border transition"
                            :class="isDark 
                               ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' 
                               : 'bg-slate-50 border-slate-200 text-slate-700 focus:border-blue-500'">
                        <option value="all">Semua Status</option>
                        <option value="sudah">Telah Ditandai Selesai</option>
                        <option value="belum">Belum Selesai Diisi</option>
                        <option value="diterima">Telah Diterima BPS</option>
                    </select>
                </div>
            </div>

            <!-- Tabel Data List -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b uppercase font-bold tracking-wider"
                            :class="isDark ? 'bg-[#15203b] border-slate-800 text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-600'">
                            <th class="py-3 px-3 text-center w-12">No</th>
                            <th class="py-3 px-3 w-28">No. Tabel</th>
                            <th class="py-3 px-4">Nama Tabel & Subjek</th>
                            <th class="py-3 px-4">Catatan / Revisi BPS</th>
                            <th class="py-3 px-4 text-center w-36">Verifikasi BPS</th>
                            <th class="py-3 px-4 text-center w-44">Aksi Pengisian Dinas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y transition-colors"
                           :class="isDark ? 'divide-slate-800 text-slate-200' : 'divide-slate-100 text-slate-700'">
                        @forelse ($tabels as $idx => $tabel)
                            <tr class="transition"
                                :class="isDark ? 'hover:bg-slate-800/40' : 'hover:bg-slate-50/70'"
                                x-show="(
                                    (filterStatus === 'all') ||
                                    (filterStatus === 'sudah' && '{{ $tabel->status_pengisian }}' === 'sudah_diisi') ||
                                    (filterStatus === 'belum' && '{{ $tabel->status_pengisian }}' !== 'sudah_diisi') ||
                                    (filterStatus === 'diterima' && {{ $tabel->data_masuk ? 'true' : 'false' }})
                                ) && (
                                    !searchTabel || 
                                    '{{ strtolower(addslashes($tabel->nama_tabel)) }}'.includes(searchTabel.toLowerCase()) || 
                                    '{{ strtolower(addslashes($tabel->no_tabel ?? '')) }}'.includes(searchTabel.toLowerCase()) ||
                                    '{{ strtolower(addslashes($tabel->subjek_web ?? '')) }}'.includes(searchTabel.toLowerCase())
                                )">
                                
                                <td class="py-3.5 px-3 text-center text-slate-400 font-medium">
                                    {{ $idx + 1 }}
                                </td>

                                <td class="py-3.5 px-3">
                                    <div class="font-bold font-mono" :class="isDark ? 'text-white' : 'text-slate-900'">
                                        {{ $tabel->no_tabel ?? '-' }}
                                    </div>
                                    @if ($tabel->no_tabel_lsda2025)
                                        <div class="text-[10px] text-slate-400">2025: {{ $tabel->no_tabel_lsda2025 }}</div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 max-w-md">
                                    <div class="font-semibold leading-snug" :class="isDark ? 'text-white' : 'text-slate-800'">
                                        {{ $tabel->nama_tabel }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if ($tabel->subjek_web)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border"
                                                  :class="isDark ? 'bg-sky-500/15 text-sky-400 border-sky-500/30' : 'bg-blue-50 text-blue-700 border-blue-200'">
                                                {{ $tabel->subjek_web }}
                                            </span>
                                        @endif
                                        @if ($tabel->catatan_dinas)
                                            <span class="text-[11px] text-slate-400 italic truncate max-w-xs" title="{{ $tabel->catatan_dinas }}">
                                                Catatan Anda: "{{ $tabel->catatan_dinas }}"
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Catatan/Revisi BPS -->
                                <td class="py-3.5 px-4">
                                    @if ($tabel->catatan_bps)
                                        <div class="p-2 rounded-xl text-[11px] font-medium border"
                                             :class="isDark ? 'bg-amber-950/30 border-amber-500/30 text-amber-200' : 'bg-amber-50 border-amber-200 text-amber-900'">
                                            {{ $tabel->catatan_bps }}
                                        </div>
                                    @else
                                        <span class="text-slate-500 text-[11px]">-</span>
                                    @endif
                                </td>

                                <!-- Status Verifikasi BPS (Data Masuk) -->
                                <td class="py-3.5 px-4 text-center">
                                    @if ($tabel->data_masuk)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                              :class="isDark ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-emerald-50 text-emerald-700 border-emerald-200'">
                                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Diterima BPS</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium border"
                                              :class="isDark ? 'bg-slate-800 text-slate-400 border-slate-700' : 'bg-slate-100 text-slate-500 border-slate-200'">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Belum Diterima</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Tombol Aksi Pengisian Dinas (Interaktif) -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Form Toggle Status -->
                                        <form action="{{ route('dinas.tabel.toggle_status', $tabel->id) }}" method="POST">
                                            @csrf
                                            @if ($tabel->status_pengisian === 'sudah_diisi')
                                                <button type="submit" 
                                                        title="Klik untuk membatalkan tanda selesai"
                                                        class="px-2.5 py-1.5 bg-emerald-600 hover:bg-rose-600 text-white rounded-xl text-xs font-semibold shadow-xs transition flex items-center gap-1 group">
                                                    <svg class="w-3.5 h-3.5 group-hover:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span class="group-hover:hidden">Selesai Diisi</span>
                                                    <span class="hidden group-hover:inline">Batalkan</span>
                                                </button>
                                            @else
                                                <button type="submit" 
                                                        title="Klik untuk menandai tabel ini telah Anda isi di spreadsheet"
                                                        class="px-2.5 py-1.5 rounded-xl text-xs font-semibold border transition flex items-center gap-1"
                                                        :class="isDark 
                                                           ? 'bg-[#15203b] hover:bg-sky-500 hover:text-slate-950 text-slate-300 border-slate-700' 
                                                           : 'bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 border-slate-200'">
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
                                                title="Tambah catatan mandiri untuk tabel ini"
                                                class="p-1.5 rounded-lg transition"
                                                :class="isDark ? 'text-slate-400 hover:text-sky-400 hover:bg-slate-800' : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada daftar rincian tabel yang ditugaskan ke instansi Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Modal Catatan Mandiri Tabel -->
    <div x-show="modalCatatan" 
         x-transition 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.outside="modalCatatan = false" 
             class="rounded-[2rem] shadow-2xl max-w-md w-full p-6 sm:p-7 border transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-800'">
            <div class="flex items-center justify-between pb-3 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <h3 class="text-base font-extrabold">Catatan Konfirmasi Tabel</h3>
                <button @click="modalCatatan = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form :action="'{{ url('portal/tabel') }}/' + activeTabel.id + '/catatan'" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <div class="text-xs text-slate-400 mb-1">Tabel:</div>
                    <div class="text-xs font-semibold p-3 rounded-xl border leading-relaxed"
                         :class="isDark ? 'bg-[#15203b] border-slate-700 text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                         x-text="activeTabel.nama"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase mb-1"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Catatan dari Instansi Anda
                    </label>
                    <textarea name="catatan_dinas" 
                              rows="3" 
                              x-model="activeTabel.catatan" 
                              placeholder="Misal: Data tahun 2025 sudah diinput lengkap di sheet 3, atau menunggu data bidang X..." 
                              class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                              :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-white border-slate-300 text-slate-800 focus:border-blue-500'"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Catatan ini akan tersimpan dan dapat dibaca oleh tim BPS.</p>
                </div>

                <div class="pt-2 flex gap-2">
                    <button type="button" @click="modalCatatan = false" 
                            class="w-1/2 py-2 px-3 border text-xs font-semibold rounded-xl"
                            :class="isDark ? 'border-slate-700 text-slate-300 hover:bg-slate-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2 px-3 font-bold text-xs rounded-xl shadow-sm"
                            :class="isDark ? 'bg-sky-500 hover:bg-sky-400 text-slate-950' : 'bg-blue-600 hover:bg-blue-700 text-white'">
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Update Profil & Ganti PIN -->
    <div x-show="modalProfile" 
         x-transition 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.outside="modalProfile = false" 
             class="rounded-[2rem] shadow-2xl max-w-md w-full p-6 sm:p-7 border transition-colors"
             :class="isDark ? 'bg-[#0c1427] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-800'">
            <div class="flex items-center justify-between pb-3 border-b"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <h3 class="text-base font-extrabold">Perbarui Profil & PIN</h3>
                <button @click="modalProfile = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form action="{{ route('dinas.profile.update') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase mb-1"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nama PIC <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="pic_name" value="{{ $dinas->pic_name }}" required 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                           :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-white border-slate-300 text-slate-800 focus:border-blue-500'">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase mb-1"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nomor WhatsApp / HP <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="pic_phone" value="{{ $dinas->pic_phone }}" required 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs focus:outline-none border"
                           :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-white border-slate-300 text-slate-800 focus:border-blue-500'">
                </div>

                <div class="pt-3 border-t"
                     :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                    <div class="text-xs font-bold mb-2"
                         :class="isDark ? 'text-slate-300' : 'text-slate-600'">
                        Ingin Ganti PIN? (Kosongkan jika tidak ingin mengubah)
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-medium text-slate-400">PIN Lama</label>
                            <input type="password" name="current_pin" placeholder="Masukkan PIN saat ini" 
                                   class="w-full px-3.5 py-2 rounded-xl text-xs focus:outline-none border"
                                   :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-white border-slate-300 text-slate-800 focus:border-blue-500'">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-400">PIN Baru</label>
                            <input type="password" name="new_pin" placeholder="Minimal 4 digit" 
                                   class="w-full px-3.5 py-2 rounded-xl text-xs focus:outline-none border"
                                   :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-white border-slate-300 text-slate-800 focus:border-blue-500'">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-400">Konfirmasi PIN Baru</label>
                            <input type="password" name="new_pin_confirmation" placeholder="Ketik ulang PIN baru" 
                                   class="w-full px-3.5 py-2 rounded-xl text-xs focus:outline-none border"
                                   :class="isDark ? 'bg-[#15203b] border-slate-700 text-white focus:border-sky-400' : 'bg-white border-slate-300 text-slate-800 focus:border-blue-500'">
                        </div>
                    </div>
                </div>

                <div class="pt-3 flex gap-2">
                    <button type="button" @click="modalProfile = false" 
                            class="w-1/2 py-2 px-3 border text-xs font-semibold rounded-xl"
                            :class="isDark ? 'border-slate-700 text-slate-300 hover:bg-slate-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2 px-3 font-bold text-xs rounded-xl shadow-sm"
                            :class="isDark ? 'bg-sky-500 hover:bg-sky-400 text-slate-950' : 'bg-blue-600 hover:bg-blue-700 text-white'">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
