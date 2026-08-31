@extends('layouts.app')

@section('title', 'Katalog Alat Laboratorium')
@section('page_title', 'Katalog Alat & Peminjaman Mandiri')
@section('page_subtitle', 'Pilih dan ajukan peminjaman peralatan praktikum laboratorium TKJ via QR Code')

@section('content')

@php
    $kategoriList = $kategoriList ?? [
        'Komputer & Laptop',
        'Jaringan & Server',
        'Kabel & Konektor',
        'Tools & Crimping',
        'Alat Ukur & Tester',
        'Elektronika & Mikrokontroler',
        'Aksesoris & Lainnya'
    ];
@endphp

{{-- ALERT NOTIFIKASI FLASH MESSAGE --}}
@if(session('success'))
    <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
@endif

{{-- QUICK SCAN HERO --}}
<div class="relative overflow-hidden bg-gradient-to-br from-indigo-950 via-indigo-900 to-indigo-700 rounded-2xl sm:rounded-[28px] px-5 py-5 lg:px-8 lg:py-7 mb-7 border border-indigo-700/60 shadow-lg shadow-indigo-950/20">
    <div class="absolute -top-24 -right-24 w-72 h-72 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 left-1/3 w-64 h-64 bg-cyan-400/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md flex items-center justify-center shadow-inner flex-shrink-0">
                <svg class="w-6 h-6 lg:w-8 lg:h-8 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] lg:text-[11px] font-bold uppercase tracking-widest text-cyan-300 block mb-0.5">Quick Access</span>
                <h3 class="text-base lg:text-xl font-extrabold text-white tracking-tight leading-tight">Peminjaman Alat Lebih Cepat</h3>
                <p class="text-xs lg:text-sm text-indigo-200 mt-1 max-w-xl leading-relaxed">Pindai QR Code pada perangkat fisik untuk memeriksa spesifikasi dan melakukan peminjaman mandiri.</p>
            </div>
        </div>

        <a href="{{ route('scan.qr') }}" class="group inline-flex items-center justify-center gap-2.5 bg-white sm:hover:bg-cyan-50 text-indigo-950 font-bold text-xs lg:text-sm px-5 lg:px-6 py-3 lg:py-3.5 rounded-2xl transition shadow-lg shadow-indigo-950/20 sm:hover:-translate-y-0.5 active:scale-95 whitespace-nowrap">
            <div class="w-7 h-7 rounded-xl bg-indigo-100 flex items-center justify-center sm:group-hover:bg-indigo-600 sm:group-hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            Buka Scanner QR
            <svg class="w-4 h-4 transition-transform sm:group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

{{-- SEARCH & FILTER CONTAINER --}}
<div class="bg-white rounded-2xl sm:rounded-[24px] border border-slate-200/80 shadow-sm p-4 lg:p-5 mb-7 relative z-30 overflow-visible">
    <form id="katalogFilterForm" action="{{ route('katalog.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center gap-3">
        
        {{-- Search Input --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat, merk, lokasi rak, atau kode barang..." class="w-full pl-11 pr-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-slate-700 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition">
        </div>

        {{-- Dropdown Kategori dengan Animasi --}}
        <div class="w-full lg:w-[240px] relative" x-data="{ openKat: false, currentKat: '{{ request('kategori', '') }}' }" @click.outside="openKat = false">
            <input type="hidden" name="kategori" :value="currentKat">

            <button type="button" @click="openKat = !openKat"
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm rounded-xl sm:rounded-2xl px-4 py-2.5 sm:py-3 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                    :class="{'border-indigo-500 bg-white ring-2 ring-indigo-500/20': openKat}">
                <span class="truncate pr-2 font-medium" :class="currentKat ? 'text-indigo-600 font-bold' : 'text-slate-700'" x-text="currentKat ? currentKat : 'Semua Kategori'">
                    {{ request('kategori') ?: 'Semua Kategori' }}
                </span>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180 text-indigo-600': openKat}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="openKat"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="absolute left-0 right-0 z-50 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl py-1.5 max-h-60 overflow-y-auto"
                 style="display: none;">
                
                <button type="button" @click="currentKat = ''; openKat = false; $nextTick(() => document.getElementById('katalogFilterForm').submit())"
                        class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                        :class="{'font-bold text-indigo-600 bg-indigo-50/60': currentKat === ''}">
                    <span>Semua Kategori</span>
                    <svg x-show="currentKat === ''" class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>

                @foreach($kategoriList as $kat)
                    <button type="button" @click="currentKat = '{{ $kat }}'; openKat = false; $nextTick(() => document.getElementById('katalogFilterForm').submit())"
                            class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                            :class="{'font-bold text-indigo-600 bg-indigo-50/60': currentKat === '{{ $kat }}'}">
                        <span>{{ $kat }}</span>
                        <svg x-show="currentKat === '{{ $kat }}'" class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 bg-indigo-600 sm:hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm px-5 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl transition active:scale-95 shadow-sm sm:hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Cari
            </button>

            @if(request()->anyFilled(['search', 'kategori']))
                <a href="{{ route('katalog.index') }}" class="inline-flex items-center justify-center bg-slate-100 sm:hover:bg-slate-200 text-slate-600 font-semibold text-xs sm:text-sm px-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl transition">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- SECTION HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-5">
    <div>
        <div class="flex items-center gap-2.5">
            <div class="w-1.5 h-5 sm:h-6 rounded-full bg-indigo-600"></div>
            <h2 class="font-extrabold text-base lg:text-xl text-slate-900 tracking-tight">Peralatan Laboratorium</h2>
        </div>
        <p class="text-xs lg:text-sm text-slate-500 mt-1 ml-4">Pilih alat yang tersedia dalam kondisi baik untuk melihat detail atau meminjam.</p>
    </div>

    <div class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl px-3 py-1.5 self-start sm:self-auto shadow-sm">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        {{ $barangs->total() }} total alat tersedia
    </div>
</div>

{{-- CARD GRID --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-6 mb-10">
    @forelse($barangs as $barang)
        @php
            $stokTersedia = (int) ($barang->stok_tersedia ?? $barang->jumlah ?? 0);
            $stokTotal = max((int) ($barang->jumlah ?? 0), 1);
            $stokPersen = min(100, max(0, ($stokTersedia / $stokTotal) * 100));
        @endphp

        <article class="group relative bg-white rounded-2xl sm:rounded-[24px] border border-slate-200/80 overflow-hidden flex flex-col min-h-full shadow-sm sm:hover:shadow-xl sm:hover:border-indigo-200 sm:hover:-translate-y-1 transition-all duration-300">
            {{-- Image / Media Frame --}}
            <div class="relative h-36 sm:h-48 overflow-hidden bg-gradient-to-br from-slate-100 via-slate-50 to-indigo-50/40 flex items-center justify-center border-b border-slate-100">
                @if(!empty($barang->foto))
                    <img src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}" loading="lazy" class="w-full h-full object-cover sm:transition-transform sm:duration-500 sm:group-hover:scale-105">
                @else
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-center text-indigo-600 sm:group-hover:scale-110 sm:transition-transform sm:duration-300">
                        @if(stripos($barang->kategori, 'Jaringan') !== false)
                            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                            </svg>
                        @elseif(stripos($barang->kategori, 'Komputer') !== false)
                            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        @endif
                    </div>
                @endif

                {{-- Badges Header --}}
                <div class="absolute top-2 inset-x-2 sm:top-3 sm:inset-x-3 z-10 flex items-center justify-between gap-1">
                    <span class="inline-flex items-center px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-md sm:rounded-lg bg-white/95 backdrop-blur-md border border-slate-200/80 shadow-sm text-[8px] sm:text-[10px] font-extrabold uppercase tracking-wide text-indigo-700 truncate max-w-[55%]">
                        {{ $barang->kategori ?? 'Umum' }}
                    </span>

                    @if($stokTersedia > 0)
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-md sm:rounded-lg bg-emerald-500 text-white shadow-sm text-[8px] sm:text-[10px] font-bold flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            <span>Tersedia</span>
                        </span>
                    @else
                        <span class="inline-flex items-center px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-md sm:rounded-lg bg-rose-600 text-white shadow-sm text-[8px] sm:text-[10px] font-bold flex-shrink-0">
                            Habis
                        </span>
                    @endif
                </div>
            </div>

            {{-- Card Body --}}
            <div class="flex flex-col flex-1 p-3.5 sm:p-5">
                <div class="flex items-center justify-between gap-1.5 mb-1.5 sm:mb-2">
                    <span class="text-[9px] sm:text-xs font-mono font-semibold text-slate-400 truncate">{{ $barang->kode_barang }}</span>
                    @if($barang->merk)
                        <span class="text-[9px] sm:text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200/70 px-1.5 sm:px-2 py-0.5 rounded-md truncate max-w-[90px] sm:max-w-[120px]">
                            {{ $barang->merk }}
                        </span>
                    @endif
                </div>

                <a href="{{ route('katalog.show', $barang->id) }}" class="group/title">
                    <h3 class="text-xs sm:text-[15px] font-extrabold leading-snug text-slate-900 tracking-tight line-clamp-2 min-h-[32px] sm:min-h-[44px] group-hover/title:text-indigo-600 transition-colors">
                        {{ $barang->nama_barang }}
                    </h3>
                </a>

                <p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed mt-1 sm:mt-1.5 line-clamp-2 min-h-[30px] sm:min-h-[36px] hidden sm:block">
                    {{ $barang->deskripsi ?? 'Lokasi: ' . ($barang->lokasi ?? 'Lab TKJ') . '. Peralatan praktikum siap digunakan.' }}
                </p>

                {{-- Stock Indicator Container --}}
                <div class="mt-3 sm:mt-4 bg-slate-50 border border-slate-100 rounded-xl sm:rounded-2xl p-2.5 sm:p-3">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <span class="block text-[8px] sm:text-[9px] uppercase tracking-wider font-extrabold text-slate-400">Stok Siap Pakai</span>
                            <div class="flex items-baseline gap-1 mt-0.5">
                                <span class="text-sm sm:text-lg font-black {{ $stokTersedia > 0 ? 'text-slate-900' : 'text-rose-500' }}">{{ $stokTersedia }}</span>
                                <span class="text-[10px] sm:text-[11px] font-semibold text-slate-500">/ {{ $barang->jumlah ?? 0 }} {{ $barang->satuan ?? 'Unit' }}</span>
                            </div>
                        </div>

                        <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl flex items-center justify-center {{ $stokTersedia > 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-500' }}">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>

                    <div class="w-full h-1 sm:h-1.5 bg-slate-200 rounded-full overflow-hidden mt-2">
                        <div class="h-full rounded-full transition-all duration-500 {{ $stokTersedia > 0 ? 'bg-emerald-500' : 'bg-rose-400' }}" style="width: {{ $stokPersen }}%"></div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="mt-auto pt-3 sm:pt-4">
                    @if($stokTersedia > 0)
                        <a href="{{ route('scan.qr') }}" class="w-full inline-flex items-center justify-center gap-1.5 bg-indigo-600 sm:hover:bg-indigo-700 text-white font-bold text-[11px] sm:text-xs px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl transition shadow-sm sm:hover:shadow-md sm:hover:shadow-indigo-600/20 active:scale-95">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <span class="truncate">Scan Kamera & Pinjam</span>
                        </a>
                    @else
                        <button disabled class="w-full inline-flex items-center justify-center gap-1 bg-slate-100 border border-slate-200 text-slate-400 font-bold text-[11px] sm:text-xs px-3 py-2 sm:py-2.5 rounded-xl cursor-not-allowed">
                            Tidak Tersedia
                        </button>
                    @endif
                </div>
            </div>
        </article>
    @empty
        <div class="col-span-full bg-white border border-slate-200 rounded-[28px] px-6 py-16 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg font-extrabold text-slate-800">Alat Tidak Ditemukan</h3>
            <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-md mx-auto">Tidak ada peralatan berkondisi baik yang cocok dengan kata kunci atau filter yang Anda pilih.</p>
            @if(request()->anyFilled(['search', 'kategori']))
                <a href="{{ route('katalog.index') }}" class="inline-flex items-center justify-center mt-4 bg-indigo-600 sm:hover:bg-indigo-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition">
                    Tampilkan Semua Alat
                </a>
            @endif
        </div>
    @endforelse
</div>

{{-- PAGINATION --}}
@if($barangs->hasPages())
    <div class="mt-8 mb-4 bg-white border border-slate-200 rounded-2xl px-5 py-4 shadow-sm flex justify-center">
        {{ $barangs->links() }}
    </div>
@endif

@endsection