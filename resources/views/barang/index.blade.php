@extends('layouts.app')

@section('title', 'Data Barang - Inventaris Lab')
@section('page_title', 'Data Barang')
@section('page_subtitle', 'Kelola peralatan, aset laboratorium, dan stiker QR Code')

@section('content')
<div class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6 w-full max-w-9xl mx-auto space-y-4 sm:space-y-6" x-data="barangIndexPage()" @open-delete-barang.window="confirmDelete($event.detail.url, $event.detail.name)">

    {{-- Header Page (Clean Text & Action - No heavy card) --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Data Barang</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola data peralatan, aset, dan stiker QR Code</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            {{-- Tombol Sampah / Hapus Data Terpilih --}}
            <button type="button" 
                    x-show="selectedItems.length > 0"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @click="confirmBulkDelete()"
                    style="display: none;"
                    title="Hapus data barang yang dipilih"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-rose-600 bg-white border border-rose-300 hover:border-rose-400 hover:bg-rose-50 hover:text-rose-700 shadow-xs transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-rose-500">
                <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Hapus (<span x-text="selectedItems.length"></span>)</span>
            </button>

            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'kepala_lab'))
            <a href="{{ route('barang.create') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>Tambah Barang</span>
            </a>
            @endif
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="bg-white p-4 sm:p-5 border border-slate-200/80 rounded-2xl sm:rounded-3xl shadow-xs relative z-11">
        <form id="filterBarangForm" @submit.prevent="fetchData()" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-center">

            {{-- Input Pencarian --}}
            <div class="lg:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       id="barangSearchInput" 
                       x-model="search" 
                       @input.debounce.400ms="fetchData()" 
                       aria-label="Cari nama barang, kode, atau merk"
                       class="block w-full min-h-[42px] pl-10 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-800 bg-slate-50/80 border border-slate-300/80 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none transition shadow-xs" 
                       placeholder="Cari nama barang, kode, merk...">
            </div>

            {{-- Custom Dropdown Laboratorium --}}
            <div class="lg:col-span-3 relative" @click.outside="closeIf('laboratorium')">
                <button type="button" @click="toggle('laboratorium')" 
                        class="w-full min-h-[42px] flex items-center justify-between bg-slate-50/80 border border-slate-300/80 text-slate-800 text-xs sm:text-sm font-semibold rounded-xl sm:rounded-2xl px-3.5 py-2.5 transition shadow-xs hover:bg-slate-100/70 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none">
                    <span x-text="{
                        '': 'Semua Laboratorium',
                        'Laboratorium TKJ': 'Laboratorium TKJ',
                        'Laboratorium AKL': 'Laboratorium AKL',
                        'Laboratorium Pemasaran': 'Laboratorium Pemasaran'
                    }[selectedLaboratorium] || 'Semua Laboratorium'" class="truncate" :class="{'text-indigo-700 font-bold': selectedLaboratorium}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-indigo-600': openDropdown === 'laboratorium' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openDropdown === 'laboratorium'" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1.5 w-full bg-white rounded-xl sm:rounded-2xl shadow-xl border border-slate-100 py-1.5 max-h-60 overflow-y-auto focus:outline-none"
                     style="display: none;">
                    @foreach(['' => 'Semua Laboratorium', 'Laboratorium TKJ' => 'Laboratorium TKJ', 'Laboratorium AKL' => 'Laboratorium AKL', 'Laboratorium Pemasaran' => 'Laboratorium Pemasaran'] as $val => $lbl)
                    <button type="button" 
                            @click="selectLaboratorium('{{ $val }}')"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm flex items-center justify-between transition"
                            :class="selectedLaboratorium === '{{ $val }}' ? 'bg-indigo-50 text-indigo-900 font-bold border-l-2 border-indigo-600' : 'text-slate-800 hover:bg-slate-100 font-medium'">
                        <span>{{ $lbl }}</span>
                        <svg x-show="selectedLaboratorium === '{{ $val }}'" class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Custom Dropdown Kategori --}}
            <div class="lg:col-span-2 relative" @click.outside="closeIf('kategori')">
                <button type="button" @click="toggle('kategori')" 
                        class="w-full min-h-[42px] flex items-center justify-between bg-slate-50/80 border border-slate-300/80 text-slate-800 text-xs sm:text-sm font-semibold rounded-xl sm:rounded-2xl px-3.5 py-2.5 transition shadow-xs hover:bg-slate-100/70 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none">
                    <span x-text="{
                        '': 'Semua Kategori',
                        'Jaringan': 'Jaringan',
                        'Komputer': 'Komputer',
                        'Perangkat Keras': 'Perangkat Keras',
                        'Alat Praktik': 'Alat Praktik',
                        'Furniture': 'Furniture',
                        'Lainnya': 'Lainnya'
                    }[selectedKategori] || 'Semua Kategori'" class="truncate" :class="{'text-indigo-700 font-bold': selectedKategori}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-indigo-600': openDropdown === 'kategori' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openDropdown === 'kategori'" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1.5 w-full bg-white rounded-xl sm:rounded-2xl shadow-xl border border-slate-100 py-1.5 max-h-60 overflow-y-auto focus:outline-none"
                     style="display: none;">
                    @foreach(['' => 'Semua Kategori', 'Jaringan' => 'Jaringan', 'Komputer' => 'Komputer', 'Perangkat Keras' => 'Perangkat Keras', 'Alat Praktik' => 'Alat Praktik', 'Furniture' => 'Furniture', 'Lainnya' => 'Lainnya'] as $val => $lbl)
                    <button type="button" 
                            @click="selectKategori('{{ $val }}')"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm flex items-center justify-between transition"
                            :class="selectedKategori === '{{ $val }}' ? 'bg-indigo-50 text-indigo-900 font-bold border-l-2 border-indigo-600' : 'text-slate-800 hover:bg-slate-100 font-medium'">
                        <span>{{ $lbl }}</span>
                        <svg x-show="selectedKategori === '{{ $val }}'" class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Custom Dropdown Kondisi --}}
            <div class="lg:col-span-2 relative" @click.outside="closeIf('kondisi')">
                <button type="button" @click="toggle('kondisi')" 
                        class="w-full min-h-[42px] flex items-center justify-between bg-slate-50/80 border border-slate-300/80 text-slate-800 text-xs sm:text-sm font-semibold rounded-xl sm:rounded-2xl px-3.5 py-2.5 transition shadow-xs hover:bg-slate-100/70 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white focus:outline-none">
                    <span x-text="{
                        '': 'Semua Kondisi',
                        'Baik': 'Baik',
                        'Perawatan': 'Perawatan',
                        'Perbaikan': 'Perbaikan',
                        'Rusak Berat': 'Rusak Berat',
                        'Hilang': 'Hilang'
                    }[selectedKondisi] || 'Semua Kondisi'" class="truncate" :class="{'text-indigo-700 font-bold': selectedKondisi}"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-indigo-600': openDropdown === 'kondisi' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openDropdown === 'kondisi'" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute z-50 mt-1.5 w-full bg-white rounded-xl sm:rounded-2xl shadow-xl border border-slate-100 py-1.5 max-h-60 overflow-y-auto focus:outline-none"
                     style="display: none;">
                    @foreach(['' => 'Semua Kondisi', 'Baik' => 'Baik', 'Perawatan' => 'Perawatan', 'Perbaikan' => 'Perbaikan', 'Rusak Berat' => 'Rusak Berat', 'Hilang' => 'Hilang'] as $val => $lbl)
                    <button type="button" 
                            @click="selectKondisi('{{ $val }}')"
                            class="w-full text-left px-3.5 py-2 text-xs sm:text-sm flex items-center justify-between transition"
                            :class="selectedKondisi === '{{ $val }}' ? 'bg-indigo-50 text-indigo-900 font-bold border-l-2 border-indigo-600' : 'text-slate-800 hover:bg-slate-100 font-medium'">
                        <span>{{ $lbl }}</span>
                        <svg x-show="selectedKondisi === '{{ $val }}'" class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Action Buttons: Reset Saja --}}
            <div class="sm:col-span-2 lg:col-span-1 flex items-center justify-end">
                <button type="button"
                        @click="resetFilters()"
                        title="Reset Filter"
                        class="w-full min-h-[42px] px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm py-2.5 rounded-xl sm:rounded-2xl border border-slate-300 transition active:scale-[0.98] text-center flex items-center justify-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-slate-400">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Data Container --}}
    <div id="barangDataContainer" class="relative z-10 transition-opacity duration-150" :class="{'opacity-50 pointer-events-none': isLoading}" @click="const a = $event.target.closest('a'); if (a && a.href && (a.closest('nav') || a.closest('.pagination'))) { $event.preventDefault(); fetchData(a.href); }">
        
        {{-- Loading Skeleton State --}}
        <template x-if="isLoading">
            <div class="p-4 sm:p-6 space-y-4 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs">
                <div class="animate-pulse flex justify-between items-center gap-4">
                    <div class="h-4 bg-slate-200 rounded w-1/3"></div>
                    <div class="h-4 bg-slate-200 rounded w-1/4"></div>
                </div>
                <div class="space-y-3 pt-2">
                    <div class="h-16 bg-slate-100 rounded-xl animate-pulse"></div>
                    <div class="h-16 bg-slate-100 rounded-xl animate-pulse"></div>
                    <div class="h-16 bg-slate-100 rounded-xl animate-pulse"></div>
                    <div class="h-16 bg-slate-100 rounded-xl animate-pulse"></div>
                </div>
            </div>
        </template>

        {{-- Table & Mobile List --}}
        <div x-show="!isLoading" class="space-y-4 sm:space-y-6">

    {{-- Mobile View: Responsive Equipment Cards (Tampil Hanya di Layar Kecil < md) --}}
    <div class="grid grid-cols-1 gap-4 md:hidden">
        {{-- Mobile Select All Bar --}}
        @if($barangs->count() > 0)
        <div class="flex items-center justify-between p-3 bg-white border border-slate-200/90 rounded-2xl shadow-xs">
            <div class="flex items-center gap-2.5">
                <label for="cbx-mob-all" class="cbx" title="Pilih Semua">
                    <div class="checkmark">
                        <input type="checkbox" id="cbx-mob-all" :checked="isAllSelected" @change="toggleSelectAll()">
                        <div class="flip">
                            <div class="front"></div>
                            <div class="back">
                                <svg viewBox="0 0 16 14" height="14" width="16">
                                    <path d="M2 8.5L6 12.5L14 1.5"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>
                <span class="text-xs font-bold text-slate-700">Pilih Semua</span>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="selectedItems.length > 0" class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100" x-text="selectedItems.length + ' dipilih'"></span>
                <button type="button" 
                        x-show="selectedItems.length > 0" 
                        @click="confirmBulkDelete()"
                        style="display: none;"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-rose-50 text-rose-600 hover:text-rose-700 border border-rose-300 rounded-xl text-xs font-bold transition active:scale-95 shadow-xs">
                    <svg class="w-3.5 h-3.5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus (<span x-text="selectedItems.length"></span>)</span>
                </button>
            </div>
        </div>
        @endif

        @forelse($barangs as $barang)
        @php
            $kBadge = match($barang->kondisi) {
                'Baik' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'Perawatan' => 'bg-amber-50 text-amber-700 border-amber-200',
                'Perbaikan' => 'bg-orange-50 text-orange-700 border-orange-200',
                'Rusak Berat' => 'bg-rose-50 text-rose-700 border-rose-200',
                'Hilang' => 'bg-slate-100 text-slate-700 border-slate-200',
                default => 'bg-slate-100 text-slate-700 border-slate-200'
            };
        @endphp
        <div class="bg-white border rounded-2xl p-4 shadow-xs space-y-3 transition"
             :class="isItemSelected({{ $barang->id }}) ? 'border-indigo-400 bg-indigo-50/20 ring-2 ring-indigo-200 shadow-sm' : 'border-slate-200/90 hover:shadow-md'">
            
            {{-- Header: Checkbox, Foto, Nama & Badge Kondisi --}}
            <div class="flex items-start justify-between gap-2.5">
                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                    {{-- 3D Flip Checkbox --}}
                    <div class="shrink-0 pt-1">
                        <label :for="'cbx-mob-' + {{ $barang->id }}" class="cbx" :title="'Pilih ' + @js($barang->nama_barang)">
                            <div class="checkmark">
                                <input type="checkbox" name="barang_checkbox_ids[]" value="{{ $barang->id }}" :id="'cbx-mob-' + {{ $barang->id }}" :checked="isItemSelected({{ $barang->id }})" @change="toggleItem({{ $barang->id }})">
                                <div class="flip">
                                    <div class="front"></div>
                                    <div class="back">
                                        <svg viewBox="0 0 16 14" height="14" width="16">
                                            <path d="M2 8.5L6 12.5L14 1.5"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl border border-slate-200/80 bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                        @if($barang->foto)
                            <img class="w-full h-full object-cover rounded-2xl" src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}" loading="lazy">
                        @else
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="font-extrabold text-slate-900 text-xs sm:text-sm leading-snug break-words">{{ $barang->nama_barang }}</h2>
                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                            <span class="text-[11px] sm:text-xs font-mono font-bold text-indigo-600">{{ $barang->kode_barang }}</span>
                            @if($barang->merk)
                                <span class="text-slate-300">•</span>
                                <span class="text-[11px] sm:text-xs text-slate-500 font-medium truncate max-w-[120px]">{{ $barang->merk }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <span class="{{ $kBadge }} border text-[10px] sm:text-[11px] font-bold px-2 py-0.5 rounded-full shrink-0 shadow-xs">
                    {{ $barang->kondisi }}
                </span>
            </div>

            {{-- Metadata Box: Kategori, Lokasi & Stok --}}
            <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                <div class="min-w-0 pr-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Kategori & Lokasi</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center rounded-md bg-indigo-50 px-1.5 py-0.5 text-[10px] font-bold text-indigo-700 border border-indigo-100">
                            {{ $barang->kategori }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 font-medium mt-1 truncate" title="{{ $barang->lokasi ?? 'Lab TKJ 1' }}">
                        {{ $barang->lokasi ?? 'Lab TKJ 1' }}
                    </p>
                </div>
                <div class="text-right min-w-0 flex flex-col justify-between pl-1">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Ketersediaan Stok</span>
                        <div class="mt-1">
                            <span class="font-extrabold text-slate-900 text-sm tabular-nums">
                                {{ $barang->stok_tersedia ?? $barang->jumlah }}
                            </span>
                            <span class="text-[11px] text-slate-500 font-medium">/ {{ $barang->jumlah }} {{ $barang->satuan }}</span>
                        </div>
                    </div>
                    <div class="mt-1">
                        @if(($barang->stok_tersedia ?? $barang->jumlah) <= 0)
                            <span class="inline-flex items-center text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">Habis</span>
                        @else
                            <span class="inline-flex items-center text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Tersedia</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Buttons: Full Width Grid --}}
            <div class="grid {{ (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'kepala_lab')) ? 'grid-cols-3' : 'grid-cols-1' }} gap-2 pt-1 border-t border-slate-100">
                <a href="{{ route('barang.show', $barang->id) }}" title="Detail Barang" aria-label="Detail {{ $barang->nama_barang }}" 
                   class="min-h-[38px] w-full rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200/80 flex items-center justify-center gap-1.5 transition active:scale-[0.98] shadow-xs">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <span>Detail</span>
                </a>

                @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'kepala_lab'))
                <a href="{{ route('barang.edit', $barang->id) }}" title="Edit Barang" aria-label="Edit {{ $barang->nama_barang }}" 
                   class="min-h-[38px] w-full rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs border border-blue-200/80 flex items-center justify-center gap-1.5 transition active:scale-[0.98] shadow-xs">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit</span>
                </a>

                <button type="button" 
                        @click="confirmDelete('{{ route('barang.destroy', $barang->id) }}', @js($barang->nama_barang))" 
                        title="Hapus Barang" 
                        aria-label="Hapus {{ $barang->nama_barang }}"
                        class="min-h-[38px] w-full rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200/80 flex items-center justify-center gap-1.5 transition active:scale-[0.98] shadow-xs">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Hapus</span>
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white border border-slate-200/80 rounded-2xl p-8 text-center space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <p class="text-slate-700 font-bold text-sm">Tidak ada data barang ditemukan.</p>
            @if(request()->has('search') || request()->has('kondisi') || request()->has('kategori') || request()->has('laboratorium'))
            <a href="{{ route('barang.index') }}" class="text-indigo-600 hover:underline text-xs font-bold inline-block">Hapus filter pencarian</a>
            @endif
        </div>
        @endforelse

        {{-- Mobile Pagination --}}
        @if($barangs->hasPages())
        <div class="pt-2">
            {{ $barangs->withQueryString()->links() }}
        </div>
        @endif
    </div>

    {{-- Desktop View: Tabel Inventaris Barang (Tampil di Layar Sedang & Besar >= md) --}}
    <div class="hidden md:block bg-white border border-slate-300 rounded-2xl sm:rounded-3xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs sm:text-sm text-left text-slate-600">
                <thead class="text-[10px] sm:text-xs uppercase bg-slate-900 border-b border-slate-900 font-bold tracking-wider">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-4 text-center w-12 text-white font-bold">
                            <label for="cbx-all" class="cbx" title="Pilih Semua">
                                <div class="checkmark">
                                    <input type="checkbox" id="cbx-all" :checked="isAllSelected" @change="toggleSelectAll()">
                                    <div class="flip">
                                        <div class="front"></div>
                                        <div class="back">
                                            <svg viewBox="0 0 16 14" height="14" width="16">
                                                <path d="M2 8.5L6 12.5L14 1.5"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </th>
                        <th scope="col" class="px-6 py-4 text-white font-bold">Item Barang</th>
                        <th scope="col" class="px-6 py-4 text-white font-bold">Kategori & Lokasi</th>
                        <th scope="col" class="px-6 py-4 text-white font-bold">Kondisi</th>
                        <th scope="col" class="px-6 py-4 text-white font-bold">Stok</th>
                        <th scope="col" class="px-6 py-4 text-right text-white font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($barangs as $barang)
                    @php
                        $reqKondisi = request('kondisi');
                        $kondisiPerUnit = [];
                        if ($barang->kondisi_per_unit) {
                            $kondisiPerUnit = is_array($barang->kondisi_per_unit) 
                                ? $barang->kondisi_per_unit 
                                : (json_decode($barang->kondisi_per_unit, true) ?: []);
                        }

                        $matchingUnitIndices = [];
                        for ($i = 1; $i <= (int)$barang->jumlah; $i++) {
                            $unitKon = $kondisiPerUnit[$i] ?? $barang->kondisi;
                            if (!$reqKondisi || $unitKon === $reqKondisi) {
                                $matchingUnitIndices[] = $i;
                            }
                        }

                        $activeKondisi = $reqKondisi ?: $barang->kondisi;
                        $kBadgeText = match($activeKondisi) {
                            'Baik' => 'text-emerald-600',
                            'Perawatan' => 'text-amber-600',
                            'Perbaikan' => 'text-orange-600',
                            'Rusak Berat' => 'text-rose-600',
                            'Hilang' => 'text-slate-600',
                            default => 'text-slate-600'
                        };

                        $unitTagStr = '';
                        if ($reqKondisi && count($matchingUnitIndices) < (int)$barang->jumlah) {
                            $unitTagStr = 'Unit ' . implode(', ', $matchingUnitIndices);
                        }
                    @endphp
                    <tr class="transition" :class="isItemSelected({{ $barang->id }}) ? 'bg-indigo-50/50 hover:bg-indigo-50/70' : 'hover:bg-slate-50/80'">
                        <td class="px-4 sm:px-6 py-3.5 whitespace-nowrap text-center w-12">
                            <label :for="'cbx-' + {{ $barang->id }}" class="cbx" :title="'Pilih ' + @js($barang->nama_barang)">
                                <div class="checkmark">
                                    <input type="checkbox" name="barang_checkbox_ids[]" value="{{ $barang->id }}" :id="'cbx-' + {{ $barang->id }}" :checked="isItemSelected({{ $barang->id }})" @change="toggleItem({{ $barang->id }})">
                                    <div class="flip">
                                        <div class="front"></div>
                                        <div class="back">
                                            <svg viewBox="0 0 16 14" height="14" width="16">
                                                <path d="M2 8.5L6 12.5L14 1.5"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl border border-slate-200/80 bg-slate-100 flex items-center justify-center shrink-0 shadow-inner">
                                    @if($barang->foto)
                                        <img class="w-full h-full object-cover rounded-2xl" src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}" loading="lazy">
                                    @else
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="font-extrabold text-slate-900 text-xs sm:text-sm truncate">
                                        {{ $barang->nama_barang }}
                                        @if($unitTagStr)
                                             <span class="inline-block ml-1 text-xs font-extrabold text-slate-900">({{ $unitTagStr }})</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] sm:text-xs font-mono font-bold text-slate-800 mt-0.5">{{ $barang->kode_barang }}</div>
                                    @if($barang->merk)
                                    <div class="text-[10px] text-slate-400 font-medium truncate">{{ $barang->merk }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            <span class="text-slate-900 text-xs font-extrabold inline-block mb-1">{{ $barang->kategori }}</span>
                            <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>{{ $barang->lokasi ?? 'Lab TKJ 1' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            <span class="text-slate-900 text-xs sm:text-sm font-extrabold inline-block">{{ $activeKondisi }}</span>
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            @if($reqKondisi)
                                <div class="font-extrabold text-indigo-900 text-xs sm:text-sm">{{ count($matchingUnitIndices) }} <span class="text-[10px] font-normal text-slate-500">{{ $barang->satuan }} ({{ $reqKondisi }})</span></div>
                            @else
                                <div class="font-extrabold text-slate-800 text-xs sm:text-sm">{{ $barang->stok_tersedia ?? $barang->jumlah }} / {{ $barang->jumlah }} <span class="text-[10px] font-normal text-slate-400">{{ $barang->satuan }}</span></div>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap text-right font-medium">
                            <div class="flex justify-end items-center gap-2">
                                {{-- Detail Button --}}
                                <a href="{{ route('barang.show', $barang->id) }}" title="Detail Barang" aria-label="Detail {{ $barang->nama_barang }}" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200/80 flex items-center justify-center transition active:scale-95 shadow-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span class="sr-only">Detail</span>
                                </a>

                                @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'kepala_lab'))
                                {{-- Edit Button --}}
                                <a href="{{ route('barang.edit', $barang->id) }}" title="Edit Barang" aria-label="Edit {{ $barang->nama_barang }}" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200/80 flex items-center justify-center transition active:scale-95 shadow-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span class="sr-only">Edit</span>
                                </a>

                                {{-- Hapus Button --}}
                                <button type="button" 
                                        @click="confirmDelete('{{ route('barang.destroy', $barang->id) }}', @js($barang->nama_barang))" 
                                        title="Hapus Barang" 
                                        aria-label="Hapus {{ $barang->nama_barang }}"
                                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center transition active:scale-95 shadow-xs focus:outline-none focus:ring-2 focus:ring-rose-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span class="sr-only">Hapus</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <span class="text-slate-700 font-bold text-xs sm:text-sm">Tidak ada data barang ditemukan.</span>
                                @if(request()->has('search') || request()->has('kondisi') || request()->has('kategori'))
                                <a href="{{ route('barang.index') }}" class="text-indigo-600 hover:underline mt-2 text-xs font-bold">Hapus filter pencarian</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($barangs->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $barangs->withQueryString()->links() }}
        </div>
        @endif
    </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <template x-teleport="body">
        <div x-show="deleteModal" 
             role="dialog" 
             aria-modal="true" 
             aria-labelledby="modal-delete-title"
             class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-xs" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div @click.away="deleteModal = false" class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 text-center"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                {{-- Icon Alert --}}
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-200/60">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                {{-- Text Alert --}}
                <h2 id="modal-delete-title" class="text-base sm:text-lg font-extrabold text-slate-900">Hapus Barang Ini?</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                    Anda akan menghapus data <span class="font-bold text-slate-900" x-text="deleteItemName"></span> secara permanen dari database.
                </p>

                {{-- Action Buttons Form --}}
                <form :action="deleteActionUrl" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')

                    <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition focus:outline-none focus:ring-2 focus:ring-slate-400">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-rose-500">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </template>
    {{-- MODAL KONFIRMASI HAPUS BANYAK (BULK DELETE) --}}
    <template x-teleport="body">
        <div x-show="bulkDeleteModal" 
             role="dialog" 
             aria-modal="true" 
             aria-labelledby="modal-bulk-delete-title"
             class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-xs" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div @click.away="bulkDeleteModal = false" class="bg-white rounded-3xl p-6 sm:p-7 w-full max-w-sm sm:max-w-md shadow-2xl border border-slate-100 text-center"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                {{-- Icon Alert --}}
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-200/60">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                {{-- Text Alert --}}
                <h2 id="modal-bulk-delete-title" class="text-base sm:text-lg font-extrabold text-slate-900">Hapus Data Terpilih?</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                    Anda akan menghapus <span class="font-bold text-rose-600" x-text="selectedItems.length"></span> barang yang dipilih secara permanen dari database. Tindakan ini tidak dapat dibatalkan.
                </p>

                {{-- Action Buttons Form --}}
                <form action="{{ route('barang.bulk-delete') }}" method="POST" class="mt-6">
                    @csrf
                    <template x-for="id in selectedItems" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>

                    <div class="flex items-center justify-center gap-3">
                        <button type="button" @click="bulkDeleteModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition focus:outline-none focus:ring-2 focus:ring-slate-400">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-xs transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-rose-500">
                            Ya, Hapus Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
window.barangIndexPage = function() {
    return {
        search: @js(request('search', '')),
        selectedLaboratorium: @js(request('laboratorium', '')),
        selectedKategori: @js(request('kategori', '')),
        selectedKondisi: @js(request('kondisi', '')),
        openDropdown: null,
        isLoading: false,
        deleteModal: false,
        bulkDeleteModal: false,
        deleteActionUrl: '',
        deleteItemName: '',

        selectedItems: [],
        get allItemIds() {
            return Array.from(document.querySelectorAll('input[name="barang_checkbox_ids[]"]')).map(function(el) {
                return parseInt(el.value);
            });
        },
        get isAllSelected() {
            var ids = this.allItemIds;
            return ids.length > 0 && ids.every(function(id) {
                return this.selectedItems.includes(id);
            }.bind(this));
        },
        toggleSelectAll() {
            var ids = this.allItemIds;
            if (this.isAllSelected) {
                this.selectedItems = this.selectedItems.filter(function(id) {
                    return !ids.includes(id);
                });
            } else {
                this.selectedItems = Array.from(new Set(this.selectedItems.concat(ids)));
            }
        },
        toggleItem(id) {
            id = parseInt(id);
            var idx = this.selectedItems.indexOf(id);
            if (idx > -1) {
                this.selectedItems.splice(idx, 1);
            } else {
                this.selectedItems.push(id);
            }
        },
        isItemSelected(id) {
            return this.selectedItems.includes(parseInt(id));
        },

        confirmBulkDelete() {
            if (this.selectedItems.length > 0) {
                this.bulkDeleteModal = true;
            }
        },

        init() {
            if (typeof this.search !== 'string') this.search = '';
            if (typeof this.selectedLaboratorium !== 'string') this.selectedLaboratorium = '';
            if (typeof this.selectedKategori !== 'string') this.selectedKategori = '';
            if (typeof this.selectedKondisi !== 'string') this.selectedKondisi = '';
        },

        confirmDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteItemName = name;
            this.deleteModal = true;
        },

        toggle(name) {
            this.openDropdown = (this.openDropdown === name) ? null : name;
        },

        closeIf(name) {
            if (this.openDropdown === name) {
                this.openDropdown = null;
            }
        },

        selectLaboratorium(val) {
            this.selectedLaboratorium = val;
            this.openDropdown = null;
            this.fetchData();
        },

        selectKategori(val) {
            this.selectedKategori = val;
            this.openDropdown = null;
            this.fetchData();
        },

        selectKondisi(val) {
            this.selectedKondisi = val;
            this.openDropdown = null;
            this.fetchData();
        },

        resetFilters() {
            this.search = '';
            this.selectedLaboratorium = '';
            this.selectedKategori = '';
            this.selectedKondisi = '';
            this.openDropdown = null;
            this.fetchData();
        },

        async fetchData(customUrl) {
            this.isLoading = true;

            if (typeof this.search !== 'string') this.search = '';
            if (typeof this.selectedLaboratorium !== 'string') this.selectedLaboratorium = '';
            if (typeof this.selectedKategori !== 'string') this.selectedKategori = '';
            if (typeof this.selectedKondisi !== 'string') this.selectedKondisi = '';

            var url = customUrl;
            if (!url) {
                var params = new URLSearchParams();
                if (this.search) params.append('search', this.search);
                if (this.selectedLaboratorium) params.append('laboratorium', this.selectedLaboratorium);
                if (this.selectedKategori) params.append('kategori', this.selectedKategori);
                if (this.selectedKondisi) params.append('kondisi', this.selectedKondisi);
                url = '{{ route('barang.index') }}' + (params.toString() ? '?' + params.toString() : '');
            }
            window.history.pushState({}, '', url);

            try {
                var res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('Network response error');
                var htmlText = await res.text();

                var parser = new DOMParser();
                var doc = parser.parseFromString(htmlText, 'text/html');
                var newContainer = doc.getElementById('barangDataContainer');

                if (newContainer) {
                    var currentContainer = document.getElementById('barangDataContainer');
                    if (currentContainer) {
                        currentContainer.innerHTML = newContainer.innerHTML;
                        if (window.Alpine) {
                            window.Alpine.initTree(currentContainer);
                        }
                    }
                } else {
                    window.location.reload();
                }
            } catch (err) {
                console.error('Fetch filter error:', err);
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
@endsection