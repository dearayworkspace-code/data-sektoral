@extends('layouts.app')

@section('title', 'Portal Data Dinas - BPS Kabupaten Lampung Selatan')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center px-4 py-6 sm:py-10 relative overflow-hidden font-sans transition-colors duration-200"
     :class="isDark ? 'bg-[#090d16]' : 'bg-[#f8fafc]'"
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
        // State Carousel
        currentSlide: 0,
        carouselTimer: null,
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
        startCarousel() {
            this.stopCarousel();
            this.carouselTimer = setInterval(() => {
                this.nextSlide(false);
            }, 7500); // Waktu 7.5 detik per slide agar pengguna dapat membaca secara nyaman
        },
        stopCarousel() {
            if (this.carouselTimer) {
                clearInterval(this.carouselTimer);
                this.carouselTimer = null;
            }
        },
        nextSlide(manual = true) {
            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
            if (manual) this.startCarousel();
        },
        prevSlide(manual = true) {
            this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
            if (manual) this.startCarousel();
        },
        setSlide(index) {
            this.currentSlide = index;
            this.startCarousel();
        },
        init() {
            if (this.selectedId) {
                const found = this.dinasList.find(d => d.id == this.selectedId);
                if (found) {
                    this.selectedName = found.nama_dinas;
                }
            }
            // Mulai auto slide carousel
            this.startCarousel();
        },
        select(item) {
            this.selectedId = item.id;
            this.selectedName = item.nama_dinas;
            this.search = '';
            this.open = false;
        },
        // State PIN
        showPin: false
     }">

    <!-- Animated Abstract & Bubble Background -->
    @include('partials.animated-background')

    <!-- Main Container Card (Split 2 Kolom) -->
    <div class="w-full max-w-4xl lg:max-w-5xl rounded-2xl border overflow-hidden grid grid-cols-1 md:grid-cols-2 relative z-10 transition-all duration-200 shadow-xl"
         :class="isDark 
            ? 'bg-[#0d1424] border-slate-800 text-white shadow-black/40' 
            : 'bg-white border-slate-200 text-slate-900 shadow-slate-900/5'">

        <!-- ==================== KOLOM KIRI: FORM LOGIN ==================== -->
        <div class="p-6 sm:p-8 lg:p-10 flex flex-col justify-between">
            
            <!-- Logo & Brand Header -->
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center p-1 shrink-0 transition-colors border"
                         :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-slate-50 border-slate-200'">
                        <img src="{{ asset('images/bps-logo.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-sm font-bold tracking-tight leading-none"
                            :class="isDark ? 'text-white' : 'text-slate-900'">
                            Data Sektoral BPS
                        </h1>
                        <p class="text-[11px] font-normal mt-1 leading-none text-slate-500 dark:text-slate-400">
                            Badan Pusat Statistik Kabupaten Lampung Selatan
                        </p>
                    </div>
                </div>

                <!-- Greeting Title -->
                <div class="mt-8 sm:mt-10">
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight"
                        :class="isDark ? 'text-white' : 'text-slate-900'">
                        Selamat Datang
                    </h2>
                    <p class="text-xs mt-1.5 leading-relaxed"
                       :class="isDark ? 'text-slate-400' : 'text-slate-600'">
                        Silakan pilih dinas Anda dan masukkan PIN untuk mengakses portal data sektoral.
                    </p>
                </div>

                <!-- Form Login -->
                <form action="{{ route('dinas.login.submit') }}" method="POST" class="mt-6 sm:mt-7 space-y-4">
                    @csrf

                    <!-- Dropdown Pencarian Dinas (Searchable Combobox) -->
                    <div class="relative" @click.outside="open = false">
                        <label class="block text-xs font-semibold mb-1.5"
                               :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            Nama Instansi / Dinas <span class="text-rose-500">*</span>
                        </label>

                        <input type="hidden" name="dinas_id" :value="selectedId" required>

                        <!-- Trigger Box -->
                        <div @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()); }" 
                             class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer transition border text-xs"
                             :class="isDark 
                                ? 'bg-slate-900/90 border-slate-700 text-white hover:bg-slate-900 focus-within:border-blue-500' 
                                : 'bg-slate-50 border-slate-200 text-slate-800 hover:bg-slate-100/80 focus-within:border-blue-600'">
                            <span x-text="selectedName ? selectedName : 'Ketik atau cari nama dinas...'" 
                                  :class="selectedName ? (isDark ? 'text-white font-medium' : 'text-slate-900 font-medium') : 'text-slate-400'"
                                  class="truncate">
                            </span>
                            <div class="flex items-center gap-1.5 shrink-0 ml-2">
                                <button type="button" 
                                        x-show="selectedId" 
                                        @click.stop="selectedId = ''; selectedName = ''; search = ''; open = true; $nextTick(() => $refs.searchInput.focus());" 
                                        title="Hapus pilihan dinas"
                                        class="text-slate-400 hover:text-rose-500 p-0.5 rounded transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" 
                                     :class="open ? 'rotate-180' : ''" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown Panel dengan Live Search -->
                        <div x-show="open" 
                             @click.stop
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute z-30 mt-1.5 w-full rounded-lg shadow-xl overflow-hidden border max-h-60 flex flex-col"
                             :class="isDark ? 'bg-slate-900 border-slate-700 text-white' : 'bg-white border-slate-200 text-slate-800'">
                            <div class="p-2 border-b shrink-0 relative"
                                 :class="isDark ? 'bg-slate-950 border-slate-800' : 'bg-slate-50 border-slate-100'">
                                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-4.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input x-ref="searchInput"
                                       x-model="search" 
                                       @click.stop
                                       @keydown.escape="open = false"
                                       type="text" 
                                       placeholder="Ketik nama dinas untuk memfilter..." 
                                       class="w-full text-xs pl-8 pr-3 py-1.5 rounded-md border focus:outline-none"
                                       :class="isDark ? 'bg-slate-900 border-slate-700 text-white placeholder-slate-500 focus:border-blue-500' : 'bg-white border-slate-200 text-slate-800 placeholder-slate-400 focus:border-blue-600'">
                            </div>
                            <ul class="overflow-y-auto divide-y text-xs flex-1 max-h-48"
                                :class="isDark ? 'divide-slate-800' : 'divide-slate-100'">
                                <template x-for="item in filteredDinas" :key="item.id">
                                    <li @click="select(item)" 
                                        class="px-3.5 py-2.5 cursor-pointer transition flex items-center justify-between"
                                        :class="selectedId == item.id 
                                            ? (isDark ? 'bg-blue-950/60 font-semibold text-blue-400' : 'bg-blue-50 font-semibold text-blue-700') 
                                            : (isDark ? 'hover:bg-slate-800/60 text-slate-200' : 'hover:bg-slate-50 text-slate-700')">
                                        <span x-text="item.nama_dinas"></span>
                                        <span x-show="selectedId == item.id" class="text-xs font-bold" :class="isDark ? 'text-blue-400' : 'text-blue-600'">✓</span>
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
                        <label class="block text-xs font-semibold mb-1.5"
                               :class="isDark ? 'text-slate-300' : 'text-slate-700'">
                            PIN Akses Masuk <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPin ? 'text' : 'password'" 
                                   name="pin" 
                                   required 
                                   placeholder="Masukkan PIN (default: 123456)" 
                                   class="w-full px-3 py-2.5 rounded-lg text-xs tracking-wider transition focus:outline-none border pr-11"
                                   :class="isDark 
                                      ? 'bg-slate-900/90 border-slate-700 text-white placeholder-slate-500 focus:border-blue-500' 
                                      : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400 focus:border-blue-600'">
                            <button type="button" 
                                    @click="showPin = !showPin" 
                                    :title="showPin ? 'Sembunyikan PIN' : 'Lihat PIN'"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1.5 rounded-md transition text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <!-- Eye Open Icon (saat PIN tersembunyi) -->
                                <svg x-show="!showPin" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <!-- Eye Slash Icon (saat PIN terlihat) -->
                                <svg x-show="showPin" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-[11px] mt-1.5 leading-snug text-slate-400">
                            Jika belum mengetahui PIN, hubungi penanggung jawab BPS terkait.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-1">
                        <button type="submit" 
                                class="w-full py-2.5 px-4 font-semibold rounded-lg text-xs tracking-wide transition btn-tactile flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white shadow-xs">
                            <span>Masuk ke Portal Dinas</span>
                            <span class="text-sm leading-none">→</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Link: Admin Login -->
            <div class="mt-8 pt-4 text-center border-t"
                 :class="isDark ? 'border-slate-800' : 'border-slate-100'">
                <a href="{{ route('admin.login') }}" 
                   class="inline-flex items-center gap-1.5 text-xs transition"
                   :class="isDark ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-slate-900'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Login Pegawai BPS</span>
                </a>
            </div>
        </div>

        <!-- ==================== KOLOM KANAN: SHOWCASE BANNER MENARA SIGER ==================== -->
        <div class="p-2.5 sm:p-3 flex flex-col">
            <div @mouseenter="stopCarousel()" 
                 @mouseleave="startCarousel()" 
                 class="relative flex-1 rounded-xl overflow-hidden min-h-[460px] sm:min-h-[520px] flex flex-col justify-between p-6 shadow-xs border border-white/10 transition-all duration-300"
                 :style="'background-image: url(' + (isDark ? '{{ asset('images/siger-dark.jpg') }}' : '{{ asset('images/siger-light.jpg') }}') + '); background-size: cover; background-position: center;'">
                
                <!-- Ambient Dark Overlay for High Legibility -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/40 to-slate-950/30 pointer-events-none"></div>

                <!-- Top Pill Switcher: Mode Gelap / Terang (Clean SVG Icons, Anti-Slop) -->
                <div class="relative z-10 flex justify-end">
                    <button type="button" 
                            @click="toggleTheme()" 
                            class="px-3 py-1.5 rounded-lg bg-slate-950/60 hover:bg-slate-950/80 backdrop-blur-md border border-white/20 text-slate-200 text-xs font-medium flex items-center gap-2 shadow-xs transition btn-tactile"
                            title="Beralih antara Mode Terang dan Mode Gelap">
                        <span x-show="!isDark" class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Mode Terang</span>
                        </span>
                        <span x-show="isDark" class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <span>Mode Gelap</span>
                        </span>
                    </button>
                </div>

                <!-- Bottom Showcase Content (Carousel Teks BPS Lampung Selatan) -->
                <div class="relative z-10 space-y-4">
                    <!-- Text Slide with Smooth Transition -->
                    <div class="min-h-[70px]">
                        <template x-for="(slide, idx) in slides" :key="idx">
                            <div x-show="currentSlide === idx" 
                                 x-transition:enter="transition ease-out duration-250"
                                 x-transition:enter-start="opacity-0 translate-y-1.5"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="space-y-1">
                                <h3 class="text-xl sm:text-2xl font-bold text-white leading-tight tracking-tight drop-shadow-sm" 
                                    x-text="slide.title">
                                </h3>
                                <p class="text-xs text-slate-300 leading-relaxed drop-shadow-xs" 
                                   x-text="slide.desc">
                                </p>
                            </div>
                        </template>
                    </div>

                    <!-- Carousel Controls: Vector SVG Arrows & Precision Dots -->
                    <div class="flex items-center gap-2 pt-1">
                        <!-- Tombol Kiri -->
                        <button type="button" 
                                @click="prevSlide()" 
                                class="w-7 h-7 rounded-lg border border-white/20 hover:border-white/50 bg-black/40 hover:bg-black/60 flex items-center justify-center text-white transition backdrop-blur-xs btn-tactile"
                                title="Slide Sebelumnya"
                                aria-label="Slide Sebelumnya">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <!-- Tombol Kanan -->
                        <button type="button" 
                                @click="nextSlide()" 
                                class="w-7 h-7 rounded-lg border border-white/20 hover:border-white/50 bg-black/40 hover:bg-black/60 flex items-center justify-center text-white transition backdrop-blur-xs btn-tactile"
                                title="Slide Berikutnya"
                                aria-label="Slide Berikutnya">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        <!-- Dots Pagination -->
                        <div class="flex items-center gap-1.5 ml-2">
                            <template x-for="(slide, idx) in slides" :key="idx">
                                <button type="button" 
                                        @click="setSlide(idx)" 
                                        class="transition-all duration-200"
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
