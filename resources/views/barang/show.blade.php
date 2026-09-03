@extends('layouts.app')

@section('title', 'Detail Barang — ' . $barang->nama_barang)
@section('page_title', 'Detail Inventaris Laboratorium')
@section('page_subtitle', 'Informasi lengkap spesifikasi, lokasi, riwayat, dan stiker QR Code alat')

@section('content')

@php
    $kondisiPerUnit = [];
    if ($barang->kondisi_per_unit) {
        $kondisiPerUnit = is_array($barang->kondisi_per_unit) 
            ? $barang->kondisi_per_unit 
            : (json_decode($barang->kondisi_per_unit, true) ?: []);
    }
@endphp

<div class="w-full max-w-9xl mx-auto space-y-4 sm:space-y-6" x-data="detailBarangHandler()">

    {{-- FLOATING TOAST NOTIFIKASI AJAX (TANPA RELOAD) --}}
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-[99999] max-w-md w-full px-4"
         style="display: none;">
        <div class="bg-white border border-emerald-200/80 rounded-2xl shadow-2xl p-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600">LABSYSTEM • NOTIFIKASI</span>
                    <span class="text-[10px] text-slate-400">Baru saja</span>
                </div>
                <p class="text-xs font-bold text-slate-800 mt-0.5" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Breadcrumb & Navigation Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <nav class="flex items-center space-x-2 text-xs text-slate-500">
            <a href="{{ route('barang.index') }}" class="hover:text-indigo-600 transition">Inventaris Barang</a>
            <span class="text-slate-300">/</span>
            <span class="font-bold text-slate-800 font-mono">{{ $barang->kode_barang }}</span>
            <span class="text-slate-300">/</span>
            <span class="text-slate-400 truncate max-w-[200px] sm:max-w-xs">{{ $barang->nama_barang }}</span>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('barang.index') }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 transition shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>
            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'kepala_lab'))
            <a href="{{ route('barang.edit', $barang->id) }}" title="Edit Barang" class="w-8 h-8 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200/80 flex items-center justify-center transition active:scale-95 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </a>
            <button type="button" @click="deleteModal = true" title="Hapus Barang" class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center transition active:scale-95 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
            @endif
        </div>
    </div>

    {{-- Main Grid: 12-Column Responsive Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
        
        <!-- Left Column: Preview Foto Alat + Deskripsi Card Saja (4 Kolom) -->
        <div class="lg:col-span-4 space-y-4 sm:space-y-6">
            
            <!-- PREVIEW FOTO ALAT -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs text-center relative overflow-hidden">
                <div class="flex items-center justify-center gap-2 mb-3">
                    <img src="{{ asset('uploads/Logo/Logo_winshark.webp') }}" class="w-6 h-6 rounded-md object-cover" onerror="this.style.display='none'">
                    <span class="text-[11px] font-extrabold text-indigo-950 uppercase tracking-wider">Winshark • Lab TKJ</span>
                </div>

                {{-- Preview Foto Frame --}}
                <div class="w-full aspect-square sm:max-w-[280px] mx-auto bg-slate-50 p-2 rounded-2xl border border-slate-200/90 shadow-inner flex items-center justify-center mb-3 overflow-hidden">
                    @if($barang->foto)
                        <img src="{{ asset('storage/'.$barang->foto) }}" alt="{{ $barang->nama_barang }}" class="w-full h-full object-cover rounded-xl">
                    @else
                        <div class="p-6 text-center text-slate-400">
                            <svg class="w-14 h-14 mx-auto mb-2 opacity-40 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-semibold text-slate-500">Foto unit belum diunggah</span>
                        </div>
                    @endif
                </div>

                <p class="font-mono font-extrabold text-sm sm:text-base text-indigo-900 tracking-wider mb-1">{{ $barang->kode_barang }}</p>
                <p class="font-bold text-xs sm:text-sm text-slate-800 leading-snug px-2">{{ $barang->nama_barang }}</p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $barang->lokasi ?? 'Laboratorium TKJ' }}</p>

                <div class="mt-4 pt-4 border-t border-slate-100 no-print">
                    <span class="text-[10px] text-slate-400 block mb-3">Cetak stiker identitas resmi barcode unit alat</span>
                    <button onclick="window.print()" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2 shadow-xs active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Stiker QR</span>
                    </button>
                </div>
            </div>

            <!-- DESKRIPSI ALAT -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 mb-3 uppercase tracking-wider">Deskripsi Alat</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed bg-slate-50/80 p-4 rounded-xl sm:rounded-2xl border border-slate-100">{{ $barang->deskripsi ?: 'Tidak ada catatan tambahan untuk alat ini.' }}</p>
            </div>
        </div>

        <!-- Right Column: Informasi Spesifikasi & Finansial (8 Kolom) -->
        <div class="lg:col-span-8 space-y-4 sm:space-y-6">
            
            <!-- Header Card -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-lg border border-indigo-100">
                        {{ $barang->kode_barang }}
                    </span>
                    <span class="text-xs font-bold bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded-lg">
                        Kategori: {{ $barang->kategori }}
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-900 leading-snug">{{ $barang->nama_barang }}</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                    Merk: <strong class="text-slate-800">{{ $barang->merk ?? '-' }}</strong> &bull; Nomor Seri (S/N): <strong class="font-mono text-slate-800">{{ $barang->nomor_seri ?? '-' }}</strong>
                </p>
            </div>

            <!-- Spesifikasi & Lokasi Info Grid -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 mb-4 pb-3 border-b border-slate-100 uppercase tracking-wider">Spesifikasi & Lokasi Alat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="bg-slate-50/80 p-4 rounded-xl sm:rounded-2xl border border-slate-200/80">
                        <p class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider">Stok Tersedia / Total</p>
                        <p class="font-extrabold text-slate-900 text-base sm:text-lg mt-1">{{ $barang->stok_tersedia ?? $barang->jumlah }} / {{ $barang->jumlah }} <span class="text-xs font-normal text-slate-500">{{ $barang->satuan }}</span></p>
                    </div>

                    <div class="bg-slate-50/80 p-4 rounded-xl sm:rounded-2xl border border-slate-200/80">
                        <p class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider">Lokasi Penyimpanan</p>
                        <p class="font-bold text-slate-800 text-xs sm:text-sm mt-1 leading-snug">{{ $barang->lokasi ?? 'Laboratorium TKJ' }}</p>
                    </div>

                    <div class="bg-slate-50/80 p-4 rounded-xl sm:rounded-2xl border border-slate-200/80">
                        <p class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider">Penanggung Jawab</p>
                        <p class="font-bold text-slate-800 text-xs sm:text-sm mt-1 leading-snug">{{ $barang->penanggung_jawab ?? 'Admin Laboratorium TKJ' }}</p>
                    </div>

                    <div class="bg-slate-50/80 p-4 rounded-xl sm:rounded-2xl border border-slate-200/80">
                        <p class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider">Sumber Dana & Tahun</p>
                        <p class="font-bold text-slate-800 text-xs sm:text-sm mt-1 leading-snug">{{ $barang->sumber_dana ?? 'BOS' }} ({{ $barang->tahun_pembelian ?? date('Y') }})</p>
                    </div>
                </div>
            </div>

            <!-- Finansial Card dengan Simbol "X" Saja di Tengah -->
            <div class="bg-indigo-50/90 p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-indigo-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="w-full sm:w-auto text-center sm:text-left">
                    <p class="text-[10px] sm:text-xs font-bold text-indigo-600 uppercase tracking-wider">Harga Satuan</p>
                    <p class="font-extrabold text-indigo-950 text-base sm:text-2xl mt-1">Rp {{ number_format($barang->harga ?? 0, 0, ',', '.') }}</p>
                </div>

                {{-- Simbol X Saja Tanpa Border atau Card Tambahan --}}
                <div class="select-none py-1 flex items-center justify-center">
                    <span class="text-indigo-400 font-extrabold text-2xl sm:text-3xl leading-none">✕</span>
                </div>

                <div class="w-full sm:w-auto text-center sm:text-right">
                    <p class="text-[10px] sm:text-xs font-bold text-indigo-600 uppercase tracking-wider">Total Nilai Aset</p>
                    <p class="font-black text-indigo-950 text-lg sm:text-3xl mt-1">Rp {{ number_format(($barang->harga ?? 0) * ($barang->jumlah ?? 0), 0, ',', '.') }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Semua Unit & QR Code Section (FULL WIDTH) --}}
    @if((int)$barang->jumlah > 1)
    <div class="bg-white p-5 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80 no-print">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 uppercase tracking-wider">Daftar Semua Unit & QR Code</h3>
                <p class="text-xs text-slate-400 mt-1">Kelola status kondisi dan cetak stiker QR untuk masing-masing dari {{ $barang->jumlah }} unit</p>
            </div>

            {{-- Search & Animated Dropdown Filter --}}
            <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap">
                <div class="relative flex-1 sm:w-56">
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari unit (misal: Unit 1)..." 
                           class="w-full text-xs bg-slate-50/80 border border-slate-200 rounded-xl pl-8 pr-3 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                {{-- Animated Filter Status Dropdown --}}
                <div class="relative min-w-[150px]" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" 
                            class="w-full bg-slate-50/80 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 flex items-center justify-between transition focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none shadow-xs">
                        <span x-text="filterKondisi || 'Semua Status'" :class="{'text-indigo-700 font-bold': filterKondisi}"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-2" :class="{'rotate-180 text-indigo-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute right-0 z-50 mt-1.5 w-full min-w-[160px] bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="filterKondisi = ''; open = false;"
                                class="w-full text-left px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-700 bg-indigo-50/50': filterKondisi === ''}">
                            <span>Semua Status</span>
                            <svg x-show="filterKondisi === ''" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @foreach(['Baik', 'Perawatan', 'Perbaikan', 'Rusak Berat', 'Hilang'] as $kon)
                        <button type="button" @click="filterKondisi = '{{ $kon }}'; open = false;"
                                class="w-full text-left px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-700 bg-indigo-50/50': filterKondisi === '{{ $kon }}'}">
                            <span>{{ $kon }}</span>
                            <svg x-show="filterKondisi === '{{ $kon }}'" class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Scrolling Container dengan Grid Semua Unit --}}
        <div class="max-h-[640px] overflow-y-auto pr-1 sm:pr-2 custom-scroll">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @for($i = 1; $i <= (int)$barang->jumlah; $i++)
                @php
                    $unitCode = $barang->kode_barang . '-' . $i;
                    $unitName = $barang->nama_barang . ' (Unit ' . $i . ')';
                @endphp
                <div x-show="matches('{{ $unitCode }}', '{{ addslashes($unitName) }}', {{ $i }})"
                     class="bg-white p-4 rounded-2xl border border-slate-200/80 text-center flex flex-col justify-between items-center transition hover:shadow-md hover:border-indigo-200 relative">
                    
                    {{-- QR Image Terkunci Aman Tanpa Perubahan --}}
                    <div class="w-24 h-24 sm:w-28 sm:h-28 bg-slate-50 p-2 rounded-xl border border-slate-200/80 flex items-center justify-center mb-2 shadow-inner">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($unitCode) }}"
                             alt="QR Code {{ $unitCode }}"
                             class="w-full h-full object-contain"
                             loading="lazy">
                    </div>

                    <span class="font-mono font-bold text-[10px] text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md mb-1 truncate w-full">{{ $unitCode }}</span>
                    <p class="font-extrabold text-xs text-slate-800 truncate w-full" title="{{ $unitName }}">Unit {{ $i }}</p>
                    
                    {{-- Custom Animated Dropdown Pengaturan Kondisi per Unit --}}
                    <div class="w-full my-2 relative" @click.outside="closeUnitDropdown({{ $i }})">
                        @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'kepala_lab'))
                            <button type="button" 
                                    @click="toggleUnitDropdown({{ $i }})"
                                    class="w-full text-xs font-bold rounded-xl px-2.5 py-1.5 border border-slate-300 bg-slate-50 hover:bg-white text-slate-800 flex items-center justify-between focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none cursor-pointer shadow-xs transition"
                                    :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': openUnitDropdown === {{ $i }}}">
                                <span class="truncate" x-text="getUnitKondisi({{ $i }})"></span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0 ml-1" 
                                     :class="{'rotate-180 text-indigo-600': openUnitDropdown === {{ $i }}}" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openUnitDropdown === {{ $i }}"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl py-1 overflow-hidden text-left"
                                 style="display: none;">
                                @foreach(['Baik', 'Perawatan', 'Perbaikan', 'Rusak Berat', 'Hilang'] as $kon)
                                <button type="button" 
                                        @click="updateUnitStatus({{ $i }}, '{{ $kon }}'); closeUnitDropdown({{ $i }})"
                                        class="w-full text-left px-3 py-1.5 text-xs flex items-center justify-between transition hover:bg-slate-50"
                                        :class="getUnitKondisi({{ $i }}) === '{{ $kon }}' ? 'font-bold text-indigo-700 bg-indigo-50/50' : 'text-slate-700'">
                                    <span>{{ $kon }}</span>
                                    <svg x-show="getUnitKondisi({{ $i }}) === '{{ $kon }}'" class="w-3 h-3 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                @endforeach
                            </div>
                        @else
                            <span class="text-[10px] font-bold px-2 py-1 rounded-lg inline-block truncate max-w-full"
                                  :class="{
                                      'bg-emerald-50 text-emerald-700 border border-emerald-200': getUnitKondisi({{ $i }}) === 'Baik',
                                      'bg-amber-50 text-amber-700 border border-amber-200': getUnitKondisi({{ $i }}) === 'Perawatan',
                                      'bg-orange-50 text-orange-700 border border-orange-200': getUnitKondisi({{ $i }}) === 'Perbaikan',
                                      'bg-rose-50 text-rose-700 border border-rose-200': getUnitKondisi({{ $i }}) === 'Rusak Berat',
                                      'bg-slate-50 text-slate-700 border border-slate-200': getUnitKondisi({{ $i }}) === 'Hilang'
                                  }"
                                  x-text="getUnitKondisi({{ $i }})">
                            </span>
                        @endif
                    </div>

                    <div class="w-full mt-1">
                        <button type="button" onclick="printSingleQR(@js($unitCode), @js($unitName), @js($barang->lokasi ?? 'Lab TKJ'))" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-1.5 px-2 rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-xs active:scale-95">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak QR</span>
                        </button>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

{{-- MODAL KONFIRMASI HAPUS BARANG TENGAH LAYAR --}}
<template x-teleport="body">
    <div x-show="deleteModal" class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-xs" style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="deleteModal = false" class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 text-center"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-90 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-3">
            
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-200/60">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Hapus Barang Ini?</h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                Anda akan menghapus data <span class="font-bold text-slate-900">{{ $barang->nama_barang }}</span> ({{ $barang->kode_barang }}) secara permanen. Tindakan ini tidak dapat dibatalkan.
            </p>

            <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="mt-6 flex items-center justify-center gap-3">
                @csrf
                @method('DELETE')
                
                <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</template>

{{-- Script Lifecycle --}}
<script>
(function() {
    function createDetailBarangHandler() {
        return {
            deleteModal: false,
            searchQuery: '',
            filterKondisi: '',
            openUnitDropdown: null,
            globalKondisi: @json($barang->kondisi),
            unitKondisi: {
                @for($i = 1; $i <= (int)$barang->jumlah; $i++)
                    {{ $i }}: @json($kondisiPerUnit[$i] ?? $barang->kondisi),
                @endfor
            },
            toast: {
                show: false,
                message: '',
                timeout: null
            },

            getUnitKondisi(index) {
                return this.unitKondisi[index] || this.globalKondisi || 'Baik';
            },

            toggleUnitDropdown(index) {
                this.openUnitDropdown = (this.openUnitDropdown === index) ? null : index;
            },

            closeUnitDropdown(index) {
                if (this.openUnitDropdown === index) {
                    this.openUnitDropdown = null;
                }
            },

            matches(code, name, unitIndex) {
                const q = (this.searchQuery || '').toLowerCase().trim();
                const matchText = !q || code.toLowerCase().includes(q) || name.toLowerCase().includes(q);
                
                const kondisi = this.getUnitKondisi(unitIndex);
                const matchKondisi = !this.filterKondisi || (kondisi === this.filterKondisi);
                
                return Boolean(matchText && matchKondisi);
            },

            async updateUnitStatus(unitIndex, newKondisi) {
                try {
                    const response = await fetch("{{ route('barang.update-kondisi', $barang->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            unit_index: unitIndex,
                            kondisi: newKondisi
                        })
                    });

                    const res = await response.json();

                    if (res.status === 'success') {
                        this.unitKondisi[unitIndex] = newKondisi;
                        if (res.global_kondisi) {
                            this.globalKondisi = res.global_kondisi;
                        }

                        this.toast.message = res.message || 'Status unit berhasil diperbarui.';
                        this.toast.show = true;

                        clearTimeout(this.toast.timeout);
                        this.toast.timeout = setTimeout(() => {
                            this.toast.show = false;
                        }, 4000);
                    } else {
                        alert('Gagal memperbarui status unit: ' + (res.message || 'Terjadi kesalahan'));
                    }
                } catch (err) {
                    console.error("Error updating unit condition:", err);
                    alert('Terjadi kendala jaringan saat memperbarui status kondisi.');
                }
            }
        };
    }

    window.detailBarangHandler = createDetailBarangHandler;

    if (window.Alpine) {
        Alpine.data('detailBarangHandler', createDetailBarangHandler);
    } else {
        document.addEventListener('alpine:init', () => {
            Alpine.data('detailBarangHandler', createDetailBarangHandler);
        });
    }
})();

function printSingleQR(code, name, location) {
    const printWindow = window.open('', '_blank', 'width=500,height=600');
    if (!printWindow) {
        alert('Pop-up terblokir! Harap izinkan pop-up pada peramban Anda untuk mencetak stiker QR Code.');
        return;
    }

    const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(code);

    printWindow.document.write(`
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Cetak Stiker QR - ${code}</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: #fff;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    padding: 20px;
                }
                .sticker {
                    border: 2px solid #0f172a;
                    padding: 16px;
                    border-radius: 14px;
                    text-align: center;
                    width: 220px;
                    background: #fff;
                }
                .header {
                    font-size: 11px;
                    font-weight: 800;
                    color: #0f172a;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 10px;
                }
                .qr-box {
                    border: 1.5px solid #0f172a;
                    padding: 8px;
                    border-radius: 10px;
                    display: inline-block;
                    background: #fff;
                    margin-bottom: 8px;
                }
                .qr-box img {
                    width: 140px;
                    height: 140px;
                    display: block;
                    object-fit: contain;
                }
                .code {
                    font-family: monospace;
                    font-size: 13px;
                    font-weight: 800;
                    color: #1e1b4b;
                    letter-spacing: 0.5px;
                    margin-bottom: 2px;
                }
                .name {
                    font-size: 11px;
                    font-weight: 700;
                    color: #1e293b;
                    line-height: 1.2;
                    margin-bottom: 2px;
                    word-break: break-word;
                }
                .loc {
                    font-size: 9.5px;
                    color: #64748b;
                }
                @media print {
                    body { padding: 0; min-height: auto; }
                    .sticker { border-color: #000; }
                }
            </style>
        </head>
        <body>
            <div class="sticker">
                <div class="header">Winshark • Lab TKJ</div>
                <div class="qr-box">
                    <img id="qrImage" src="${qrUrl}" alt="QR ${code}">
                </div>
                <div class="code">${code}</div>
                <div class="name">${name}</div>
                <div class="loc">${location}</div>
            </div>
            <script>
                const img = document.getElementById('qrImage');
                function doPrint() {
                    setTimeout(() => {
                        window.focus();
                        window.print();
                    }, 200);
                }

                if (img.complete) {
                    doPrint();
                } else {
                    img.onload = doPrint;
                    img.onerror = doPrint;
                }

                window.onafterprint = function() {
                    window.close();
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endsection