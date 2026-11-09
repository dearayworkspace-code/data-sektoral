@extends('layouts.app')

@section('title', 'Aktivasi Akun - ' . $dinas->nama_dinas)

@section('content')
<div class="min-h-screen bg-[#070d1d] flex flex-col justify-center items-center px-4 py-8 sm:py-12 relative overflow-hidden font-sans">
    
    <!-- Background Ambient Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-xl relative z-10" x-data="{ gantiPin: false }">

        <!-- Header Bar with BPS Logo & Pill Switcher -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center p-1 shadow-sm shrink-0 transition-colors"
                     :class="isDark ? 'bg-slate-800/80 border border-slate-700/80' : 'bg-slate-50 border border-slate-200/90'">
                    <img src="{{ asset('images/bps-logo.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-tight leading-none"
                        :class="isDark ? 'text-white' : 'text-slate-100'">
                        Portal Data Dinas
                    </h1>
                    <p class="text-[11px] font-medium mt-1 leading-none text-slate-400">
                        Badan Pusat Statistik · Kab. Lampung Selatan
                    </p>
                </div>
            </div>

            <!-- Pill Switcher: Mode Gelap / Terang -->
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

        <!-- Main Card -->
        <div class="rounded-[2.25rem] border p-6 sm:p-8 lg:p-9 shadow-2xl transition-all duration-300"
             :class="isDark 
                ? 'bg-[#0c1427] border-slate-800 text-white shadow-sky-950/30' 
                : 'bg-white border-slate-200/90 text-slate-800 shadow-slate-900/10'">
            
            <div class="mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2.5"
                      :class="isDark ? 'bg-sky-500/15 text-sky-400 border border-sky-500/30' : 'bg-blue-50 text-blue-700 border border-blue-200'">
                    Aktivasi Akun Pertama Kali
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight"
                    :class="isDark ? 'text-white' : 'text-slate-900'">
                    {{ $dinas->nama_dinas }}
                </h2>
                <p class="text-xs mt-2 leading-relaxed"
                   :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                    Mohon lengkapi identitas penanggung jawab data untuk kelancaran koordinasi teknis pengumpulan data BPS Kabupaten Lampung Selatan.
                </p>
            </div>

            <form action="{{ route('dinas.first_login.save') }}" method="POST" class="space-y-4 sm:space-y-5">
                @csrf

                <!-- Nama Lengkap PIC -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider mb-1.5"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nama Lengkap Penanggung Jawab (PIC) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="pic_name" 
                           value="{{ old('pic_name', $dinas->pic_name) }}" 
                           required 
                           placeholder="Contoh: Budi Santoso, S.Kom" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs transition focus:outline-none border"
                           :class="isDark 
                              ? 'bg-[#15203b] border-slate-700/80 text-white placeholder-slate-500 focus:border-sky-400' 
                              : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400 focus:border-blue-500'">
                    <p class="text-[11px] mt-1 text-slate-400">
                        Pejabat atau staf teknis yang bertugas mengelola atau menginput data dinas.
                    </p>
                </div>

                <!-- Nomor WhatsApp / HP -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider mb-1.5"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="pic_phone" 
                           value="{{ old('pic_phone', $dinas->pic_phone) }}" 
                           required 
                           placeholder="Contoh: 081234567890" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs transition focus:outline-none border"
                           :class="isDark 
                              ? 'bg-[#15203b] border-slate-700/80 text-white placeholder-slate-500 focus:border-sky-400' 
                              : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400 focus:border-blue-500'">
                    <p class="text-[11px] mt-1 text-slate-400">
                        Digunakan tim BPS untuk koordinasi cepat jika terdapat anomali atau verifikasi isian data.
                    </p>
                </div>

                <!-- Opsi Ganti PIN -->
                <div class="pt-3 border-t"
                     :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" 
                               name="ganti_pin" 
                               value="1" 
                               x-model="gantiPin"
                               class="mt-1 w-4 h-4 rounded text-blue-600 focus:ring-blue-500"
                               :class="isDark ? 'bg-slate-900 border-slate-700' : 'bg-white border-slate-300'">
                        <div>
                            <span class="text-xs sm:text-sm font-bold"
                                  :class="isDark ? 'text-white' : 'text-slate-800'">
                                Ubah PIN Sekarang (Opsional)
                            </span>
                            <p class="text-xs" :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                                Centang jika Anda ingin mengubah PIN default dari BPS ke PIN baru pilihan Anda.
                            </p>
                        </div>
                    </label>

                    <!-- Form Input PIN Baru (Expandable) -->
                    <div x-show="gantiPin" x-transition class="mt-4 p-4 rounded-2xl border space-y-3"
                         :class="isDark ? 'bg-[#162039]/70 border-slate-700' : 'bg-blue-50/50 border-blue-100'">
                        <div>
                            <label class="block text-[11px] font-semibold mb-1"
                                   :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                                PIN Baru (Minimal 4 Digit)
                            </label>
                            <input type="password" 
                                   name="new_pin" 
                                   placeholder="Masukkan PIN baru Anda" 
                                   class="w-full px-3 py-2 rounded-xl text-xs focus:outline-none border"
                                   :class="isDark ? 'bg-[#0f172a] border-slate-700 text-white focus:border-sky-400' : 'bg-white border-slate-300 text-slate-800 focus:border-blue-500'">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold mb-1"
                                   :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                                Konfirmasi PIN Baru
                            </label>
                            <input type="password" 
                                   name="new_pin_confirmation" 
                                   placeholder="Ketik ulang PIN baru Anda" 
                                   class="w-full px-3 py-2 rounded-xl text-xs focus:outline-none border"
                                   :class="isDark ? 'bg-[#0f172a] border-slate-700 text-white focus:border-sky-400' : 'bg-white border-slate-300 text-slate-800 focus:border-blue-500'">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-3">
                    <button type="submit" 
                            class="w-full py-3 px-4 font-bold rounded-xl text-xs sm:text-sm tracking-wide transition transform active:scale-[0.99] flex items-center justify-center gap-2"
                            :class="isDark 
                                ? 'bg-sky-500 hover:bg-sky-400 text-slate-950 shadow-lg shadow-sky-500/20' 
                                : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-lg shadow-blue-500/25'">
                        <span>Simpan & Masuk ke Portal Dinas</span>
                        <span class="text-base leading-none">→</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Logout Link -->
        <div class="text-center mt-6">
            <form action="{{ route('dinas.logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-slate-400 hover:text-white transition underline">
                    Bukan instansi Anda? Keluar sesi login
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
