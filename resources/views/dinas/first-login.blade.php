@extends('layouts.app')

@section('title', 'Aktivasi Akun - ' . $dinas->nama_dinas)

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 sm:py-12 relative overflow-hidden font-sans transition-colors duration-200"
     :class="isDark ? 'bg-[#090d16] text-slate-100' : 'bg-[#f8fafc] text-slate-900'">
    
    <!-- Precision Background Grid -->
    @include('partials.animated-background')

    <div class="w-full max-w-xl relative z-10" x-data="{ gantiPin: false }">

        <!-- Header Bar with BPS Logo & Theme Switcher -->
        <div class="flex items-center justify-between mb-6">
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
                    <p class="text-[11px] font-medium mt-1 leading-none"
                       :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                        BPS Kab. Lampung Selatan
                    </p>
                </div>
            </div>

            <!-- Theme Switcher (Vector SVG) -->
            <button type="button" 
                    @click="toggleTheme()" 
                    class="px-2.5 py-1.5 rounded-lg border text-xs font-medium flex items-center gap-1.5 shadow-xs transition btn-tactile"
                    :class="isDark 
                        ? 'bg-[#0f172a] hover:bg-slate-800 text-slate-200 border-slate-800' 
                        : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200'"
                    title="Beralih tema">
                <span x-show="!isDark" class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Terang</span>
                </span>
                <span x-show="isDark" class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <span>Gelap</span>
                </span>
            </button>
        </div>

        <!-- Main Activation Card -->
        <div class="rounded-2xl border p-6 sm:p-7 shadow-lg transition-all duration-200"
             :class="isDark 
                ? 'bg-[#0d1424] border-slate-800 text-white shadow-black/40' 
                : 'bg-white border-slate-200/90 text-slate-900 shadow-slate-200/50'">
            
            <div class="mb-5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-medium mb-2"
                      :class="isDark ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-blue-50 text-blue-700 border border-blue-200'">
                    Aktivasi Akun Pertama Kali
                </span>
                <h2 class="text-lg sm:text-xl font-bold tracking-tight"
                    :class="isDark ? 'text-white' : 'text-slate-900'">
                    {{ $dinas->nama_dinas }}
                </h2>
                <p class="text-xs mt-1 leading-relaxed"
                   :class="isDark ? 'text-slate-400' : 'text-slate-600'">
                    Mohon lengkapi identitas penanggung jawab data untuk kelancaran koordinasi teknis pengumpulan data BPS Kabupaten Lampung Selatan.
                </p>
            </div>

            <form action="{{ route('dinas.first_login.save') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Lengkap PIC -->
                <div>
                    <label class="block text-xs font-semibold mb-1"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nama lengkap penanggung jawab (PIC) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="pic_name" 
                           value="{{ old('pic_name', $dinas->pic_name) }}" 
                           required 
                           placeholder="Contoh: Budi Santoso, S.Kom" 
                           class="w-full px-3 py-2 rounded-lg text-xs transition border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                           :class="isDark 
                              ? 'bg-[#131b2e] border-slate-700 text-white placeholder-slate-500 focus:border-blue-500' 
                              : 'bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-blue-600'">
                    <p class="text-[11px] mt-1" :class="isDark ? 'text-slate-500' : 'text-slate-500'">
                        Pejabat atau staf teknis yang bertugas mengelola atau menginput data dinas.
                    </p>
                </div>

                <!-- Nomor WhatsApp / HP -->
                <div>
                    <label class="block text-xs font-semibold mb-1"
                           :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                        Nomor WhatsApp / HP aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="pic_phone" 
                           value="{{ old('pic_phone', $dinas->pic_phone) }}" 
                           required 
                           placeholder="Contoh: 081234567890" 
                           class="w-full px-3 py-2 rounded-lg text-xs transition border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                           :class="isDark 
                              ? 'bg-[#131b2e] border-slate-700 text-white placeholder-slate-500 focus:border-blue-500' 
                              : 'bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-blue-600'">
                    <p class="text-[11px] mt-1" :class="isDark ? 'text-slate-500' : 'text-slate-500'">
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
                               class="mt-0.5 w-4 h-4 rounded text-blue-600 focus:ring-blue-500"
                               :class="isDark ? 'bg-slate-900 border-slate-700' : 'bg-white border-slate-300'">
                        <div>
                            <span class="text-xs font-semibold"
                                  :class="isDark ? 'text-white' : 'text-slate-900'">
                                Ubah PIN Sekarang (Opsional)
                            </span>
                            <p class="text-xs" :class="isDark ? 'text-slate-400' : 'text-slate-600'">
                                Centang jika Anda ingin mengubah PIN default dari BPS ke PIN baru pilihan Anda.
                            </p>
                        </div>
                    </label>

                    <!-- Form Input PIN Baru (Expandable) -->
                    <div x-show="gantiPin" x-transition class="mt-3 p-3.5 rounded-xl border space-y-3"
                         :class="isDark ? 'bg-[#131b2e] border-slate-800' : 'bg-slate-50 border-slate-200'">
                        <div>
                            <label class="block text-xs font-semibold mb-1"
                                   :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                                PIN baru (minimal 4 digit)
                            </label>
                            <input type="password" 
                                   name="new_pin" 
                                   placeholder="Masukkan PIN baru Anda" 
                                   class="w-full px-3 py-2 rounded-lg text-xs border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                                   :class="isDark ? 'bg-[#0f172a] border-slate-700 text-white focus:border-blue-500' : 'bg-white border-slate-300 text-slate-900 focus:border-blue-600'">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1"
                                   :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                                Konfirmasi PIN baru
                            </label>
                            <input type="password" 
                                   name="new_pin_confirmation" 
                                   placeholder="Ketik ulang PIN baru Anda" 
                                   class="w-full px-3 py-2 rounded-lg text-xs border focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                                   :class="isDark ? 'bg-[#0f172a] border-slate-700 text-white focus:border-blue-500' : 'bg-white border-slate-300 text-slate-900 focus:border-blue-600'">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 font-semibold rounded-lg text-xs transition btn-tactile flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white shadow-xs focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none">
                        <span>Simpan & Masuk ke Portal Dinas</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Logout Link -->
        <div class="text-center mt-6">
            <form action="{{ route('dinas.logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="text-xs transition underline"
                        :class="isDark ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-slate-900'">
                    Bukan instansi Anda? Keluar sesi login
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
