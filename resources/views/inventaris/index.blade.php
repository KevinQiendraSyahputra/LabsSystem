@extends('layouts.app')

@section('title', 'Inventaris Laboratorium')
@section('page_title', 'Inventaris Laboratorium')
@section('page_subtitle', 'Kelola daftar inventaris setiap laboratorium secara terpisah')

@section('content')
<div class="space-y-5 sm:space-y-6" x-data="inventarisPage()">

    {{-- ===== HEADER + STATS OVERVIEW CARDS PER LAB ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 0: Semua Laboratorium --}}
        @php
            $isAllActive = ($activeLab === 'Semua' || empty($activeLab));
            $totalJenisSemua = array_sum(array_column($rekapLab, 'jumlah_jenis'));
            $totalUnitSemua  = array_sum(array_column($rekapLab, 'total_unit'));
        @endphp
        <a href="{{ route('inventaris.index', ['lab' => 'Semua']) }}"
           class="group relative block p-5 rounded-2xl border-2 transition-all duration-200 shadow-sm
                  {{ $isAllActive
                     ? 'bg-slate-800 border-slate-800 text-white shadow-slate-900/30 shadow-lg'
                     : 'bg-white border-slate-200/80 hover:border-slate-400 hover:shadow-md' }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest {{ $isAllActive ? 'text-slate-300' : 'text-slate-400' }}">Overview</p>
                    <h3 class="text-lg sm:text-xl font-extrabold mt-0.5 {{ $isAllActive ? 'text-white' : 'text-slate-800' }}">Semua Lab</h3>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $isAllActive ? 'bg-white/20' : 'bg-slate-100' }}">
                    <svg class="w-5 h-5 {{ $isAllActive ? 'text-white' : 'text-slate-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <div class="rounded-xl p-2 {{ $isAllActive ? 'bg-white/15' : 'bg-slate-50' }}">
                    <p class="text-[10px] font-semibold {{ $isAllActive ? 'text-slate-300' : 'text-slate-400' }}">Jenis Barang</p>
                    <p class="text-base font-extrabold {{ $isAllActive ? 'text-white' : 'text-slate-800' }}">{{ $totalJenisSemua }}</p>
                </div>
                <div class="rounded-xl p-2 {{ $isAllActive ? 'bg-white/15' : 'bg-slate-50' }}">
                    <p class="text-[10px] font-semibold {{ $isAllActive ? 'text-slate-300' : 'text-slate-400' }}">Total Unit</p>
                    <p class="text-base font-extrabold {{ $isAllActive ? 'text-white' : 'text-slate-800' }}">{{ $totalUnitSemua }}</p>
                </div>
            </div>
            @if($isAllActive)
            <div class="absolute top-3 right-3">
                <span class="bg-white/25 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">AKTIF</span>
            </div>
            @endif
        </a>

        @foreach($laboratoriumList as $lab)
        @php
            $info     = $rekapLab[$lab] ?? ['jumlah_jenis'=>0,'total_unit'=>0,'total_nilai'=>0];
            $isActive = ($activeLab === $lab);
            $labShort = $lab;
            $colors   = ['Laboratorium TKJ'=>'indigo','Laboratorium AKL'=>'emerald','Laboratorium Pemasaran'=>'violet'];
            $c        = $colors[$lab] ?? 'indigo';
        @endphp
        <a href="{{ route('inventaris.index', ['lab' => $lab]) }}"
           class="group relative block p-5 rounded-2xl border-2 transition-all duration-200 shadow-sm
                  {{ $isActive
                     ? 'bg-'.$c.'-600 border-'.$c.'-600 text-white shadow-'.$c.'-200/60 shadow-lg'
                     : 'bg-white border-slate-200/80 hover:border-'.$c.'-400 hover:shadow-md' }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest {{ $isActive ? 'text-'.$c.'-100' : 'text-slate-400' }}">Laboratorium</p>
                    <h3 class="text-lg sm:text-xl font-extrabold mt-0.5 {{ $isActive ? 'text-white' : 'text-slate-800' }}">{{ $labShort }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $isActive ? 'bg-white/20' : 'bg-'.$c.'-50' }}">
                    <svg class="w-5 h-5 {{ $isActive ? 'text-white' : 'text-'.$c.'-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <div class="rounded-xl p-2 {{ $isActive ? 'bg-white/15' : 'bg-slate-50' }}">
                    <p class="text-[10px] font-semibold {{ $isActive ? 'text-'.$c.'-100' : 'text-slate-400' }}">Jenis Barang</p>
                    <p class="text-base font-extrabold {{ $isActive ? 'text-white' : 'text-slate-800' }}">{{ $info['jumlah_jenis'] }}</p>
                </div>
                <div class="rounded-xl p-2 {{ $isActive ? 'bg-white/15' : 'bg-slate-50' }}">
                    <p class="text-[10px] font-semibold {{ $isActive ? 'text-'.$c.'-100' : 'text-slate-400' }}">Total Unit</p>
                    <p class="text-base font-extrabold {{ $isActive ? 'text-white' : 'text-slate-800' }}">{{ $info['total_unit'] }}</p>
                </div>
            </div>
            @if($isActive)
            <div class="absolute top-3 right-3">
                <span class="bg-white/25 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">AKTIF</span>
            </div>
            @endif
        </a>
        @endforeach
    </div>

    {{-- ===== STATS CARDS ROW ===== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Jenis Barang</p>
                    <p class="text-xl font-extrabold text-slate-900">{{ $barangs->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Total Unit</p>
                    <p class="text-xl font-extrabold text-slate-900">{{ $totalUnit }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Nilai Aset</p>
                    <p class="text-sm font-extrabold text-slate-900">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Unit Baik</p>
                    <p class="text-xl font-extrabold text-slate-900">{{ $rekapKondisi['Baik'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FILTER + ACTIONS ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-4 sm:p-5">
        <form method="GET" action="{{ route('inventaris.index') }}" class="flex flex-col sm:flex-row gap-3 flex-wrap">
            {{-- Filter Laboratorium Dropdown --}}
            <div class="sm:w-52 relative" x-data="{ open: false, selected: '{{ request('lab', 'Semua') }}' }">
                <input type="hidden" name="lab" :value="selected">
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <span x-text="selected === 'Semua' ? 'Semua Laboratorium' : selected" :class="{'text-slate-400': selected === 'Semua', 'font-semibold text-indigo-600': selected !== 'Semua'}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                     style="display: none;">
                    <button type="button" @click="selected = 'Semua'; open = false"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === 'Semua'}">
                        <span>Semua Laboratorium</span>
                        <svg x-show="selected === 'Semua'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    @foreach(['Laboratorium TKJ', 'Laboratorium AKL', 'Laboratorium Pemasaran'] as $labItem)
                        <button type="button" @click="selected = '{{ $labItem }}'; open = false"
                                class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === '{{ $labItem }}'}">
                            <span>{{ $labItem }}</span>
                            <svg x-show="selected === '{{ $labItem }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Search --}}
            <div class="flex-1 min-w-0">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none block w-full pl-9 pr-3 py-2.5"
                           placeholder="Cari nama, kode, atau merk barang...">
                </div>
            </div>

            {{-- Kategori --}}
            <div class="sm:w-44 relative" x-data="{ open: false, selected: '{{ request('kategori') }}' }">
                <input type="hidden" name="kategori" :value="selected">
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <span x-text="selected || 'Semua Kategori'" :class="{'text-slate-400': !selected, 'font-semibold text-indigo-600': selected}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                     style="display: none;">
                    <button type="button" @click="selected = ''; open = false"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === ''}">
                        <span>Semua Kategori</span>
                        <svg x-show="selected === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    @foreach(['Jaringan','Komputer','Perangkat Keras','Alat Praktik','Furniture','Lainnya'] as $kat)
                        <button type="button" @click="selected = '{{ $kat }}'; open = false"
                                class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === '{{ $kat }}'}">
                            <span>{{ $kat }}</span>
                            <svg x-show="selected === '{{ $kat }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Kondisi --}}
            <div class="sm:w-40 relative" x-data="{ open: false, selected: '{{ request('kondisi') }}' }">
                <input type="hidden" name="kondisi" :value="selected">
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="bg-slate-50/70 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl px-3 py-2.5 flex items-center justify-between w-full text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <span x-text="selected || 'Semua Kondisi'" :class="{'text-slate-400': !selected, 'font-semibold text-indigo-600': selected}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                     style="display: none;">
                    <button type="button" @click="selected = ''; open = false"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                            :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === ''}">
                        <span>Semua Kondisi</span>
                        <svg x-show="selected === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    @foreach(['Baik','Perawatan','Perbaikan','Rusak Berat','Hilang'] as $kon)
                        <button type="button" @click="selected = '{{ $kon }}'; open = false"
                                class="w-full text-left px-3.5 py-2 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': selected === '{{ $kon }}'}">
                            <span>{{ $kon }}</span>
                            <svg x-show="selected === '{{ $kon }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold rounded-xl transition shadow-sm active:scale-95">
                    Filter
                </button>
                <a href="{{ route('inventaris.index', ['lab' => $activeLab]) }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs sm:text-sm font-medium rounded-xl transition">
                    Reset
                </a>
            </div>

            {{-- Print PDF button --}}
            <a href="{{ route('inventaris.pdf', array_merge(['lab' => $activeLab], request()->only('kategori','kondisi'))) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold rounded-xl transition shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak PDF
            </a>
        </form>
    </div>

    {{-- ===== TABEL INVENTARIS ===== --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Daftar Inventaris — {{ $activeLab }}</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ $barangs->count() }} jenis barang ditemukan</p>
            </div>
            {{-- Rekap kondisi mini --}}
            <div class="hidden sm:flex items-center gap-1.5 text-xs">
                @foreach(['Baik'=>'emerald','Perawatan'=>'yellow','Perbaikan'=>'orange','Rusak Berat'=>'red','Hilang'=>'slate'] as $k=>$c)
                @if($rekapKondisi[$k] > 0)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 font-semibold text-[10px]">
                    {{ $rekapKondisi[$k] }} {{ $k }}
                </span>
                @endif
                @endforeach
            </div>
        </div>

        @if($barangs->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-700 mb-1">Belum ada inventaris</h3>
                <p class="text-xs text-slate-400 max-w-xs">
                    Belum ada barang yang dikaitkan ke <strong>{{ $activeLab }}</strong>.<br>
                    Tambah barang baru atau edit barang yang ada dan pilih laboratorium ini.
                </p>
                <a href="{{ route('barang.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Barang
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="px-4 py-3 text-left w-8">No</th>
                            <th class="px-4 py-3 text-left">Kode</th>
                            <th class="px-4 py-3 text-left">Nama Barang</th>
                            <th class="px-4 py-3 text-left">Merk</th>
                            <th class="px-4 py-3 text-center">Jumlah</th>
                            <th class="px-4 py-3 text-center">Kondisi</th>
                            <th class="px-4 py-3 text-left">Lokasi</th>
                            <th class="px-4 py-3 text-right">Harga Satuan</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $no = 1; @endphp
                        @foreach($byKategori as $kategori => $items)
                            {{-- Kategori Header Row --}}
                            <tr class="bg-indigo-50/60">
                                <td colspan="9" class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider">{{ $kategori }}</span>
                                        <span class="text-[10px] text-indigo-400">({{ $items->count() }} item)</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach($items as $barang)
                            @php
                                $kondisiColors = ['Baik'=>'emerald','Perawatan'=>'yellow','Perbaikan'=>'orange','Rusak Berat'=>'red','Hilang'=>'slate'];
                                $kColor = $kondisiColors[$barang->kondisi] ?? 'slate';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3 text-slate-400 text-xs">{{ $no++ }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-mono text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-lg">{{ $barang->kode_barang }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-800 text-xs sm:text-sm leading-tight">{{ $barang->nama_barang }}</div>
                                    @if($barang->deskripsi)
                                        <div class="text-[10px] text-slate-400 mt-0.5 line-clamp-1">{{ Str::limit($barang->deskripsi, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs">{{ $barang->merk ?? '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-[11px]">
                                        {{ $barang->jumlah }} <span class="font-normal text-slate-400">{{ $barang->satuan }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $kColor }}-100 text-{{ $kColor }}-700">
                                        {{ $barang->kondisi }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs max-w-[130px] truncate">{{ $barang->lokasi ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-xs font-medium text-slate-700">
                                    @if($barang->harga)
                                        Rp {{ number_format($barang->harga, 0, ',', '.') }}
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('barang.show', $barang) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-[11px] font-semibold transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-100/90 text-slate-800 border-t-2 border-slate-200">
                            <td colspan="4" class="px-4 py-3 text-xs font-bold">TOTAL {{ $activeLab }}</td>
                            <td class="px-4 py-3 text-center text-xs font-bold">{{ $totalUnit }} unit</td>
                            <td colspan="2" class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-right text-xs font-bold">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function inventarisPage() {
    return {};
}
</script>
@endpush
@endsection