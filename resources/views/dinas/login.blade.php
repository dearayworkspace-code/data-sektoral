@extends('layouts.app')

@section('title', 'Portal Data Dinas - BPS Kabupaten Lampung Selatan')

@section('content')
<div class="min-h-screen bg-[#070d1d] flex flex-col justify-center items-center px-4 py-6 sm:py-10 relative overflow-hidden font-sans"
     x-data="{
        isDark: (localStorage.getItem('theme_mode') === 'dark' || localStorage.getItem('theme_dinas') === 'dark'),
        toggleTheme() {
            this.isDark = !this.isDark;
            localStorage.setItem('theme_mode', this.isDark ? 'dark' : 'light');
            localStorage.setItem('theme_dinas', this.isDark ? 'dark' : 'light');
        },
        // State Dropdown Pencarian Dinas
        open: false,
        search: '',
        selectedId: '{{ old('dinas_id') }}',
        selectedName: '',
        dinasList: {{ Js::from($dinasList) }},
        get filteredDinas() {
            if (!this.search) return this.dinasList;
            return this.dinasList.filter(item => 
                item.nama_dinas.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        init() {
            if (this.selectedId) {
                const found = this.dinasList.find(d => d.id == this.selectedId);
                if (found) {
                    this.selectedName = found.nama_dinas;
                }
            }
            // Auto slide carousel setiap 6 detik
            setInterval(() => {
                this.nextSlide();
            }, 6000);
        },
        select(item) {
            this.selectedId = item.id;
            this.selectedName = item.nama_dinas;
            this.search = '';
            this.open = false;
        },
        // State PIN
        showPin: false,
        // Carousel Slogan BPS Lampung Selatan (6 Slide)
        currentSlide: 0,
        slides: [
            {
                title: 'Terintegrasi, Cepat, dan Akurat.',
                desc: 'Portal sinkronisasi pengumpulan data sektoral daerah Kabupaten Lampung Selatan.'
            },
            {
                title: 'Satu Data Indonesia (SDI).',
                desc: 'Mewujudkan keterpaduan tata kelola data statistik yang mutakhir, akurat, dan akuntabel.'
            },
            {
                title: 'Lampung Selatan Dalam Angka 2026.',
                desc: 'Kolaborasi lintas sektor menyusun rujukan data utama perencanaan pembangunan daerah.'
            },
            {
                title: 'Statistik Sektoral Berkualitas.',
                desc: 'Fondasi tepercaya dalam perumusan kebijakan publik yang tepat sasaran.'
            },
            {
                title: 'BPS Melayani dengan Integritas.',
                desc: 'Menjunjung nilai profesionalisme, transparansi, amanah, dan pelayanan prima.'
            },
            {
                title: 'Sinergi Pembangunan Daerah.',
                desc: 'Pengumpulan data terpadu yang aman, terjadwal, dan berkesinambungan.'
            }
        ],
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
        },
        prevSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        },
        setSlide(index) {
            this.currentSlide = index;
        }
     }">

    <!-- Background Ambient Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Container Card (Split 2 Kolom) -->
    <div class="w-full max-w-4xl lg:max-w-5xl rounded-[2.25rem] border overflow-hidden grid grid-cols-1 md:grid-cols-2 relative z-10 transition-all duration-300 shadow-2xl"
         :class="isDark 
            ? 'bg-[#0c1427] border-slate-800 text-white shadow-sky-950/30' 
            : 'bg-white border-slate-200/90 text-slate-800 shadow-slate-900/10'">

        <!-- ==================== KOLOM KIRI: FORM LOGIN ==================== -->
        <div class="p-6 sm:p-8 lg:p-10 flex flex-col justify-between">
            
            <!-- Logo & Brand Header -->
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center p-1 shadow-sm shrink-0 transition-colors"
                         :class="isDark ? 'bg-slate-800/80 border border-slate-700/80' : 'bg-slate-50 border border-slate-200/90'">
                        <img src="{{ asset('images/bps-logo.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-base font-extrabold tracking-tight leading-none"
                            :class="isDark ? 'text-white' : 'text-slate-900'">
                            Data Sektoral BPS
                        </h1>
                        <p class="text-[11px] font-medium mt-1 leading-none text-slate-400">
                            Badan Pusat Statistik Kabupaten Lampung Selatan
                        </p>
                    </div>
                </div>

                <!-- Greeting Title -->
                <div class="mt-8 sm:mt-10">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight"
                        :class="isDark ? 'text-white' : 'text-slate-900'">
                        Selamat Datang
                    </h2>
                    <p class="text-xs mt-2 leading-relaxed"
                       :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                        Silakan pilih dinas Anda dan masukkan PIN untuk mengakses portal data sektoral.
                    </p>
                </div>

                <!-- Form Login -->
                <form action="{{ route('dinas.login.submit') }}" method="POST" class="mt-6 sm:mt-8 space-y-5">
                    @csrf

                    <!-- Dropdown Pencarian Dinas (Searchable Combobox) -->
                    <div class="relative">
                        <label class="block text-[11px] font-bold uppercase tracking-wider mb-1.5"
                               :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            NAMA DINAS / INSTANSI <span class="text-rose-500">*</span>
                        </label>

                        <input type="hidden" name="dinas_id" :value="selectedId" required>

                        <!-- Trigger Box -->
                        <div @click="open = !open" 
                             @click.outside="open = false"
                             class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl cursor-pointer transition border text-xs"
                             :class="isDark 
                                ? 'bg-[#15203b] border-slate-700/80 text-white hover:bg-[#1a284a] focus-within:border-sky-400' 
                                : 'bg-slate-50 border-slate-200 text-slate-800 hover:bg-slate-100 focus-within:border-blue-500'">
                            <span x-text="selectedName ? selectedName : 'Ketik atau cari nama dinas...'" 
                                  :class="selectedName ? (isDark ? 'text-white font-semibold' : 'text-slate-800 font-semibold') : 'text-slate-400'"
                                  class="truncate">
                            </span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform duration-200" 
                                 :class="open ? 'rotate-180' : ''" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        <!-- Dropdown Panel dengan Live Search -->
                        <div x-show="open" 
                             x-transition 
                             class="absolute z-30 mt-1.5 w-full rounded-xl shadow-2xl overflow-hidden border max-h-56 flex flex-col"
                             :class="isDark ? 'bg-[#0f172a] border-slate-700 text-white' : 'bg-white border-slate-200 text-slate-700'">
                            <div class="p-2 border-b shrink-0"
                                 :class="isDark ? 'bg-[#162039] border-slate-800' : 'bg-slate-50 border-slate-100'">
                                <input x-model="search" 
                                       type="text" 
                                       placeholder="Ketik nama dinas untuk memfilter..." 
                                       class="w-full text-xs px-3 py-1.5 rounded-lg border focus:outline-none"
                                       :class="isDark ? 'bg-[#0f172a] border-slate-700 text-white placeholder-slate-500 focus:border-sky-400' : 'bg-white border-slate-200 text-slate-800 placeholder-slate-400 focus:border-blue-500'">
                            </div>
                            <ul class="overflow-y-auto divide-y text-xs flex-1"
                                :class="isDark ? 'divide-slate-800' : 'divide-slate-100'">
                                <template x-for="item in filteredDinas" :key="item.id">
                                    <li @click="select(item)" 
                                        class="px-3.5 py-2.5 cursor-pointer transition flex items-center justify-between"
                                        :class="selectedId == item.id 
                                            ? (isDark ? 'bg-sky-950/60 font-bold text-sky-400' : 'bg-blue-50 font-bold text-blue-700') 
                                            : (isDark ? 'hover:bg-slate-800/60 text-slate-200' : 'hover:bg-slate-50 text-slate-700')">
                                        <span x-text="item.nama_dinas"></span>
                                        <span x-show="selectedId == item.id" class="text-xs font-bold" :class="isDark ? 'text-sky-400' : 'text-blue-600'">✓</span>
                                    </li>
                                </template>
                                <li x-show="filteredDinas.length === 0" class="px-4 py-3 text-xs text-slate-400 text-center italic">
                                    Dinas tidak ditemukan
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Input PIN Akses -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider mb-1.5"
                               :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            PIN AKSES DINAS <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPin ? 'text' : 'password'" 
                                   name="pin" 
                                   required 
                                   placeholder="Masukkan PIN (default: 123456)" 
                                   class="w-full px-3.5 py-2.5 rounded-xl text-xs tracking-wider transition focus:outline-none border pr-14"
                                   :class="isDark 
                                      ? 'bg-[#15203b] border-slate-700/80 text-white placeholder-slate-500 focus:border-sky-400' 
                                      : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400 focus:border-blue-500'">
                            <button type="button" 
                                    @click="showPin = !showPin" 
                                    class="absolute right-3 top-2.5 text-xs font-semibold transition"
                                    :class="isDark ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-slate-800'">
                                <span x-text="showPin ? 'Sembunyi' : 'Lihat'"></span>
                            </button>
                        </div>
                        <p class="text-[11px] mt-1.5 leading-snug text-slate-400">
                            Jika belum mengetahui PIN, hubungi penanggung jawab BPS terkait.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full py-3 px-4 font-bold rounded-xl text-xs sm:text-sm tracking-wide transition transform active:scale-[0.99] flex items-center justify-center gap-2"
                            :class="isDark 
                                ? 'bg-sky-500 hover:bg-sky-400 text-slate-950 shadow-lg shadow-sky-500/20' 
                                : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-lg shadow-blue-500/25'">
                        <span>Masuk ke Portal Dinas</span>
                        <span class="text-base leading-none">→</span>
                    </button>
                </form>
            </div>

            <!-- Footer Link: Admin Login -->
            <div class="mt-8 pt-4 text-center border-t"
                 :class="isDark ? 'border-slate-800/60' : 'border-slate-100'">
                <a href="{{ route('admin.login') }}" 
                   class="inline-flex items-center gap-1.5 text-xs transition"
                   :class="isDark ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-slate-900'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Login Khusus Administrator BPS</span>
                </a>
            </div>
        </div>

        <!-- ==================== KOLOM KANAN: SHOWCASE BANNER MENARA SIGER ==================== -->
        <div class="p-3 sm:p-3.5 flex flex-col">
            <div class="relative flex-1 rounded-[1.75rem] overflow-hidden min-h-[460px] sm:min-h-[520px] flex flex-col justify-between p-6 shadow-inner transition-all duration-500"
                 :style="'background-image: url(' + (isDark ? '{{ asset('images/siger-dark.jpg') }}' : '{{ asset('images/siger-light.jpg') }}') + '); background-size: cover; background-position: center;'">
                
                <!-- Ambient Dark Overlay for High Legibility -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/30 to-black/25 pointer-events-none"></div>

                <!-- Top Pill Switcher: Mode Gelap / Terang -->
                <div class="relative z-10 flex justify-end">
                    <button type="button" 
                            @click="toggleTheme()" 
                            class="px-3.5 py-1.5 rounded-full bg-slate-950/50 hover:bg-slate-950/70 backdrop-blur-md border border-white/25 text-white text-[11px] font-semibold flex items-center gap-1.5 shadow-md transition transform active:scale-95"
                            title="Beralih antara Mode Terang dan Mode Gelap">
                        <span>Mode Gelap/Terang</span>
                        <span class="text-xs" x-text="isDark ? '🌙' : '☀️'"></span>
                    </button>
                </div>

                <!-- Bottom Showcase Content (Carousel Teks BPS Lampung Selatan) -->
                <div class="relative z-10 space-y-4">
                    <!-- Text Slide with Smooth Transition -->
                    <div class="min-h-[70px]">
                        <template x-for="(slide, idx) in slides" :key="idx">
                            <div x-show="currentSlide === idx" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="space-y-1">
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md" 
                                    x-text="slide.title">
                                </h3>
                                <p class="text-xs text-white/85 leading-relaxed drop-shadow" 
                                   x-text="slide.desc">
                                </p>
                            </div>
                        </template>
                    </div>

                    <!-- Carousel Controls: Panah (←) (→) & 6 Dots -->
                    <div class="flex items-center gap-3 pt-2">
                        <!-- Tombol Kiri -->
                        <button type="button" 
                                @click="prevSlide()" 
                                class="w-7 h-7 rounded-full border border-white/40 hover:border-white hover:bg-white/20 flex items-center justify-center text-white text-xs transition backdrop-blur-xs shadow-xs"
                                title="Slide Sebelumnya">
                            ←
                        </button>

                        <!-- Tombol Kanan -->
                        <button type="button" 
                                @click="nextSlide()" 
                                class="w-7 h-7 rounded-full border border-white/40 hover:border-white hover:bg-white/20 flex items-center justify-center text-white text-xs transition backdrop-blur-xs shadow-xs"
                                title="Slide Berikutnya">
                            →
                        </button>

                        <!-- Dots Pagination (6 Dots) -->
                        <div class="flex items-center gap-1.5 ml-1">
                            <template x-for="(slide, idx) in slides" :key="idx">
                                <button type="button" 
                                        @click="setSlide(idx)" 
                                        class="transition-all duration-300"
                                        :class="currentSlide === idx ? 'w-5 h-1.5 rounded-full bg-white shadow-xs' : 'w-1.5 h-1.5 rounded-full bg-white/40 hover:bg-white/70'"
                                        :title="'Slide ' + (idx + 1)">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection
