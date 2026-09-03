@extends('layouts.app')

@section('title', 'Laporan Inventaris')
@section('page_title', 'Laporan Inventaris Aset')
@section('page_subtitle', 'Rekap data barang & nilai aset semua laboratorium')

@section('content')

<div class="space-y-4 sm:space-y-6" x-data="laporanFilter()" x-init="initPage()">

    {{-- Filter & Actions Bar (Staggered Entrance 1) --}}
    <div x-show="isLoaded"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/60 p-4 sm:p-5 no-print relative z-30"
         style="display: none;">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 flex-1">
                
                {{-- 1. Animated Dropdown: Laboratorium Filter --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Laboratorium</label>
                    
                    <button type="button" @click="open = !open"
                            class="w-full bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition shadow-xs"
                            :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': open}">
                        <span x-text="filterLaboratorium ? filterLaboratorium : 'Semua Laboratorium'" :class="{'font-bold text-indigo-700': filterLaboratorium}"></span>
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
                         class="absolute z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="selectLaboratorium(''); open = false"
                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterLaboratorium === ''}">
                            <span>Semua Laboratorium</span>
                            <svg x-show="filterLaboratorium === ''" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @foreach(['Laboratorium TKJ', 'Laboratorium AKL', 'Laboratorium Pemasaran'] as $lab)
                            <button type="button" @click="selectLaboratorium('{{ $lab }}'); open = false"
                                    class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterLaboratorium === '{{ $lab }}'}">
                                <span>{{ $lab }}</span>
                                <svg x-show="filterLaboratorium === '{{ $lab }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- 2. Animated Dropdown: Kategori Filter --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kategori</label>
                    
                    <button type="button" @click="open = !open"
                            class="w-full bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition shadow-xs"
                            :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': open}">
                        <span x-text="filterKategori ? filterKategori : 'Semua Kategori'" :class="{'font-bold text-indigo-700': filterKategori}"></span>
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
                         class="absolute z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="selectKategori(''); open = false"
                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterKategori === ''}">
                            <span>Semua Kategori</span>
                            <svg x-show="filterKategori === ''" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @foreach(['Jaringan', 'Komputer', 'Perangkat Keras', 'Alat Praktik', 'Furniture', 'Lainnya'] as $kat)
                            <button type="button" @click="selectKategori('{{ $kat }}'); open = false"
                                    class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterKategori === '{{ $kat }}'}">
                                <span>{{ $kat }}</span>
                                <svg x-show="filterKategori === '{{ $kat }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Animated Dropdown: Kondisi Filter --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kondisi</label>
                    
                    <button type="button" @click="open = !open"
                            class="w-full bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition shadow-xs"
                            :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': open}">
                        <span x-text="filterKondisi ? filterKondisi : 'Semua Kondisi'" :class="{'font-bold text-indigo-700': filterKondisi}"></span>
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
                         class="absolute z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="selectKondisi(''); open = false"
                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterKondisi === ''}">
                            <span>Semua Kondisi</span>
                            <svg x-show="filterKondisi === ''" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @foreach(['Baik' => 'emerald', 'Perawatan' => 'yellow', 'Perbaikan' => 'orange', 'Rusak Berat' => 'red', 'Hilang' => 'slate'] as $kon => $color)
                            <button type="button" @click="selectKondisi('{{ $kon }}'); open = false"
                                    class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterKondisi === '{{ $kon }}'}">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-{{ $color }}-500"></span>
                                    <span>{{ $kon }}</span>
                                </span>
                                <svg x-show="filterKondisi === '{{ $kon }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- 4. Animated Dropdown: Sumber Dana Filter --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sumber Dana</label>
                    
                    <button type="button" @click="open = !open"
                            class="w-full bg-slate-50/70 border border-slate-300 text-slate-800 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between text-left focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition shadow-xs"
                            :class="{'border-indigo-500 ring-2 ring-indigo-500/20 bg-white': open}">
                        <span x-text="filterSumberDana ? filterSumberDana : 'Semua Sumber Dana'" :class="{'font-bold text-indigo-700': filterSumberDana}"></span>
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
                         class="absolute z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl py-1 overflow-hidden"
                         style="display: none;">
                        <button type="button" @click="selectSumberDana(''); open = false"
                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterSumberDana === ''}">
                            <span>Semua Sumber Dana</span>
                            <svg x-show="filterSumberDana === ''" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @foreach(['BOS', 'Sekolah', 'Hibah', 'Donasi', 'APBN', 'Lainnya'] as $sd)
                            <button type="button" @click="selectSumberDana('{{ $sd }}'); open = false"
                                    class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between"
                                    :class="{'font-bold text-indigo-600 bg-indigo-50/50': filterSumberDana === '{{ $sd }}'}">
                                <span>{{ $sd }}</span>
                                <svg x-show="filterSumberDana === '{{ $sd }}'" class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2 self-end sm:self-auto">
                <button type="button" @click="resetFilter()" x-show="filterLaboratorium || filterKategori || filterKondisi || filterSumberDana" x-transition
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-xs">
                    Reset
                </button>
                <button type="button"
                        @click="
                            const params = new URLSearchParams();
                            if (filterLaboratorium) params.set('laboratorium', filterLaboratorium);
                            if (filterKategori) params.set('kategori', filterKategori);
                            if (filterKondisi) params.set('kondisi', filterKondisi);
                            if (filterSumberDana) params.set('sumber_dana', filterSumberDana);
                            window.open('{{ route('laporan.inventaris.pdf') }}?' + params.toString(), '_blank');
                        "
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold px-4 sm:px-5 py-2.5 rounded-xl transition flex items-center gap-2 shadow-xs active:scale-95">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak PDF</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Header Cetak Resmi --}}
    <div class="hidden print:block mb-8 text-center border-b-2 border-slate-900 pb-4">
        <h1 class="text-xl font-bold uppercase tracking-wider text-slate-900">Sistem Manajemen Laboratorium TKJ</h1>
        <h2 class="text-lg font-semibold text-slate-700">Laporan Inventaris & Rekapitulasi Aset</h2>
        <p class="text-xs text-slate-500 mt-1">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    {{-- Stat Summaries (Staggered Entrance 2 + Smooth Number Counter) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <div x-show="isLoaded"
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-700 delay-100"
             x-transition:enter-start="opacity-0 translate-y-6 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200/60 shadow-xs hover:shadow-md transition-shadow"
             style="display: none;">
            <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider">Total Jenis Barang Terfilter</p>
            <p class="text-2xl sm:text-3xl font-black text-slate-800 mt-1 tracking-tight font-mono" x-text="displayStats.jenis">0</p>
        </div>
        <div x-show="isLoaded"
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-700 delay-150"
             x-transition:enter-start="opacity-0 translate-y-6 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200/60 shadow-xs hover:shadow-md transition-shadow"
             style="display: none;">
            <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider">Total Unit Barang Terfilter</p>
            <p class="text-2xl sm:text-3xl font-black text-indigo-600 mt-1 tracking-tight font-mono" x-text="displayStats.unit">0</p>
        </div>
        <div x-show="isLoaded"
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-700 delay-200"
             x-transition:enter-start="opacity-0 translate-y-6 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200/60 shadow-xs hover:shadow-md transition-shadow"
             style="display: none;">
            <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider">Total Nilai Aset Terfilter</p>
            <p class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1 tracking-tight font-mono" x-text="'Rp ' + displayStats.nilai.toLocaleString('id-ID')">Rp 0</p>
        </div>
    </div>

    {{-- Rekap Per Kategori & Rekap Kondisi (Staggered Entrance 3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Rekap per Kategori --}}
        <div x-show="isLoaded"
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-700 delay-250"
             x-transition:enter-start="opacity-0 translate-y-6"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="lg:col-span-2 bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/60 overflow-hidden"
             style="display: none;">
            <div class="px-5 py-4 border-b border-slate-800 bg-slate-900 text-white">
                <h3 class="font-bold text-white text-xs sm:text-sm uppercase tracking-wider">Rekapitulasi Per Kategori</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/90 text-slate-600 border-b border-slate-200">
                        <tr class="text-[11px] uppercase tracking-wider font-bold text-slate-600">
                            <th class="px-5 py-3.5">Kategori</th>
                            <th class="px-5 py-3.5 text-center">Jumlah Jenis</th>
                            <th class="px-5 py-3.5 text-center">Total Unit</th>
                            <th class="px-5 py-3.5 text-right">Nilai Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs sm:text-sm bg-white">
                        @forelse($byKategori as $kategori => $items)
                            <tr class="rekap-kategori-row hover:bg-slate-50/50 transition-colors" data-kategori="{{ $kategori }}">
                                <td class="px-5 py-3.5 font-bold text-slate-800">{{ $kategori }}</td>
                                <td class="px-5 py-3.5 text-center text-slate-600 font-semibold row-kat-jenis">{{ $items->count() }}</td>
                                <td class="px-5 py-3.5 text-center text-slate-600 font-semibold row-kat-unit">{{ $items->sum('jumlah') }}</td>
                                <td class="px-5 py-3.5 text-right font-extrabold text-slate-800 font-mono row-kat-nilai">
                                    Rp {{ number_format($items->sum(fn($b) => ($b->harga ?? 0) * $b->jumlah), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-kat-row">
                                <td colspan="4" class="px-5 py-8 text-center bg-slate-50/50">
                                    <div class="py-2">
                                        <p class="text-xs sm:text-sm font-bold text-slate-700">Tidak ada rekapitulasi kategori</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Belum ada barang terdaftar pada laboratorium ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="empty-kat-row-js" style="display: none;">
                            <td colspan="4" class="px-5 py-8 text-center bg-slate-50/50">
                                <div class="py-2">
                                    <p class="text-xs sm:text-sm font-bold text-slate-700">Tidak ada rekapitulasi kategori</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Tidak ditemukan data rekapitulasi untuk filter yang Anda pilih.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Rekap per Kondisi dengan Animasi Garis Bar --}}
        <div x-show="isLoaded"
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-700 delay-300"
             x-transition:enter-start="opacity-0 translate-y-6"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/60 overflow-hidden"
             style="display: none;">
            <div class="px-5 py-4 border-b border-slate-800 bg-slate-900 text-white">
                <h3 class="font-bold text-white text-xs sm:text-sm uppercase tracking-wider">Status Kondisi Barang</h3>
            </div>
            <div class="p-5 space-y-4">
                @php $totalUnitSemua = $barangs->sum('jumlah') ?: 1; @endphp
                @foreach(['Baik' => 'emerald', 'Perawatan' => 'yellow', 'Perbaikan' => 'orange', 'Rusak Berat' => 'red', 'Hilang' => 'slate'] as $status => $color)
                    @php
                        $unitKondisi = $rekapKondisi[$status] ?? 0;
                        $persen = round(($unitKondisi / $totalUnitSemua) * 100);
                    @endphp
                    <div class="rekap-kondisi-block" data-status="{{ $status }}" data-percent="{{ $persen }}">
                        <div class="flex justify-between items-center text-xs font-semibold text-slate-600 mb-1.5">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-{{ $color }}-500"></span>
                                <span>{{ $status }}</span>
                            </span>
                            <span class="kondisi-text-unit font-bold text-slate-800 font-mono">{{ $unitKondisi }} unit ({{ $persen }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            {{-- Smooth Expansion Progress Line --}}
                            <div class="kondisi-bar-fill bg-{{ $color }}-500 h-2 rounded-full transition-all duration-1000 cubic-bezier(0.16, 1, 0.3, 1)" 
                                 style="width: 0%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tabel Inventaris Lengkap (Staggered Entrance 4) --}}
    <div x-show="isLoaded"
         x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-700 delay-350"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/60 overflow-hidden"
         style="display: none;">
        <div class="px-5 py-4 border-b border-slate-800 bg-slate-900 text-white flex items-center justify-between">
            <h3 class="font-bold text-white text-xs sm:text-sm uppercase tracking-wider">Rincian Seluruh Barang</h3>
            <span class="text-xs sm:text-sm text-slate-300 font-bold" id="count-terfilter">{{ $barangs->count() }} barang ditampilkan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="table-laporan">
                <thead class="bg-slate-50/90 text-slate-600 border-b border-slate-200">
                    <tr class="text-xs sm:text-sm uppercase tracking-wider font-extrabold text-slate-700">
                        <th class="px-4 py-3.5 font-extrabold">No</th>
                        <th class="px-4 py-3.5 font-extrabold">Kode</th>
                        <th class="px-4 py-3.5 font-extrabold">Nama Barang</th>
                        <th class="px-4 py-3.5 font-extrabold">Laboratorium</th>
                        <th class="px-4 py-3.5 font-extrabold">Kategori</th>
                        <th class="px-4 py-3.5 text-center font-extrabold">Kondisi</th>
                        <th class="px-4 py-3.5 text-center font-extrabold">Jumlah</th>
                        <th class="px-4 py-3.5 font-extrabold">Lokasi</th>
                        <th class="px-4 py-3.5 text-right font-extrabold">Harga Satuan</th>
                        <th class="px-4 py-3.5 text-right font-extrabold">Nilai Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm bg-white">
                    @forelse($barangs as $index => $b)
                        <tr class="barang-row hover:bg-slate-50/50 transition-colors"
                            data-laboratorium="{{ $b->laboratorium }}"
                            data-kategori="{{ $b->kategori }}"
                            data-kondisi="{{ $b->kondisi }}"
                            data-kondisi-per-unit="{{ $b->kondisi_per_unit }}"
                            data-sumber-dana="{{ $b->sumber_dana }}"
                            data-jumlah="{{ $b->jumlah }}"
                            data-harga="{{ $b->harga ?? 0 }}"
                            data-nama="{{ $b->nama_barang }}"
                            data-merk="{{ $b->merk }}"
                            data-satuan="{{ $b->satuan }}">
                            <td class="px-4 py-4 text-slate-500 font-bold row-number">{{ $index + 1 }}</td>
                            <td class="px-4 py-4 font-mono font-extrabold text-indigo-700 text-sm sm:text-base">{{ $b->kode_barang }}</td>
                            <td class="px-4 py-4 row-nama">
                                <p class="font-extrabold text-slate-900 text-sm sm:text-base">{{ $b->nama_barang }}</p>
                                @if($b->merk)<p class="text-xs text-slate-400 font-medium mt-0.5">{{ $b->merk }}</p>@endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($b->laboratorium)
                                    <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ $b->laboratorium }}</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-700 font-bold text-sm">{{ $b->kategori }}</td>
                            <td class="px-4 py-4 text-center row-kondisi whitespace-nowrap">
                                <span class="font-extrabold text-xs sm:text-sm text-slate-900 inline-block">
                                    {{ $b->kondisi }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center font-extrabold text-slate-900 text-sm sm:text-base row-jumlah">
                                {{ $b->jumlah }} <span class="text-xs font-medium text-slate-500">{{ $b->satuan }}</span>
                            </td>
                            <td class="px-4 py-4 text-slate-700 font-medium text-sm">{{ $b->lokasi ?? '—' }}</td>
                            <td class="px-4 py-4 text-right text-slate-700 font-mono font-bold text-sm">
                                {{ $b->harga ? 'Rp '.number_format($b->harga, 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-4 py-4 text-right font-black text-slate-900 font-mono text-sm sm:text-base row-nilai">
                                {{ $b->harga ? 'Rp '.number_format($b->harga * $b->jumlah, 0, ',', '.') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="10" class="px-5 py-12 text-center bg-slate-50/50">
                                <div class="py-4 max-w-md mx-auto">
                                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-indigo-100/80 shadow-xs">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <p class="font-extrabold text-slate-800 text-sm sm:text-base">Tidak ada data inventaris barang</p>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Belum ada perangkat atau alat laboratorium yang terdaftar untuk kategori ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <tr id="empty-row-js" style="display: none;">
                        <td colspan="10" class="px-5 py-12 text-center bg-slate-50/50">
                            <div class="py-4 max-w-md mx-auto">
                                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-amber-100/80 shadow-xs">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <p class="font-extrabold text-slate-800 text-sm sm:text-base">Tidak ada barang yang cocok dengan filter</p>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Silakan sesuaikan pilihan laboratorium, kategori, kondisi barang, atau tekan tombol Reset.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    function createLaporanFilter() {
        return {
            isLoaded: false,
            filterLaboratorium: '',
            filterKategori: '',
            filterKondisi: '',
            filterSumberDana: '',
            animationFrameId: null,

            rawStats: {
                jenis: {{ $barangs->count() }},
                unit: {{ $barangs->sum('jumlah') }},
                nilai: {{ (float) $totalNilai }}
            },

            displayStats: {
                jenis: 0,
                unit: 0,
                nilai: 0
            },

            initPage() {
                setTimeout(() => {
                    this.isLoaded = true;
                    this.animateCounters(this.rawStats.jenis, this.rawStats.unit, this.rawStats.nilai, 1000);
                    setTimeout(() => {
                        this.animateProgressBars();
                    }, 250);
                }, 50);
            },

            animateCounters(targetJenis, targetUnit, targetNilai, duration = 750) {
                if (this.animationFrameId) {
                    cancelAnimationFrame(this.animationFrameId);
                }

                const startTime = performance.now();
                const startJenis = this.displayStats.jenis;
                const startUnit = this.displayStats.unit;
                const startNilai = this.displayStats.nilai;

                const step = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const easeOutProgress = 1 - Math.pow(1 - progress, 3);

                    this.displayStats.jenis = Math.round(startJenis + (targetJenis - startJenis) * easeOutProgress);
                    this.displayStats.unit = Math.round(startUnit + (targetUnit - startUnit) * easeOutProgress);
                    this.displayStats.nilai = Math.round(startNilai + (targetNilai - startNilai) * easeOutProgress);

                    if (progress < 1) {
                        this.animationFrameId = requestAnimationFrame(step);
                    } else {
                        this.displayStats.jenis = targetJenis;
                        this.displayStats.unit = targetUnit;
                        this.displayStats.nilai = targetNilai;
                        this.animationFrameId = null;
                    }
                };

                this.animationFrameId = requestAnimationFrame(step);
            },

            animateProgressBars() {
                document.querySelectorAll('.rekap-kondisi-block').forEach(cBlock => {
                    const percent = cBlock.getAttribute('data-percent') || 0;
                    const bar = cBlock.querySelector('.kondisi-bar-fill');
                    if (bar) {
                        bar.style.width = percent + '%';
                    }
                });
            },

            selectLaboratorium(val) {
                this.filterLaboratorium = val;
                this.applyFilter();
            },

            selectKategori(val) {
                this.filterKategori = val;
                this.applyFilter();
            },

            selectKondisi(val) {
                this.filterKondisi = val;
                this.applyFilter();
            },

            selectSumberDana(val) {
                this.filterSumberDana = val;
                this.applyFilter();
            },

            resetFilter() {
                this.filterLaboratorium = '';
                this.filterKategori = '';
                this.filterKondisi = '';
                this.filterSumberDana = '';
                this.applyFilter();
            },

            applyFilter() {
                const lab = this.filterLaboratorium;
                const kat = this.filterKategori;
                const kon = this.filterKondisi;
                const sd = this.filterSumberDana;

                const rows = document.querySelectorAll('.barang-row');
                let visibleCount = 0;
                let visibleUnits = 0;
                let visibleNilai = 0;

                const katStats = {};
                const konStats = { 'Baik': 0, 'Perawatan': 0, 'Perbaikan': 0, 'Rusak Berat': 0, 'Hilang': 0 };

                rows.forEach(row => {
                    const rLab = row.getAttribute('data-laboratorium');
                    const rKat = row.getAttribute('data-kategori');
                    const rKon = row.getAttribute('data-kondisi');
                    const rSd = row.getAttribute('data-sumber-dana');
                    const rJumlah = parseInt(row.getAttribute('data-jumlah')) || 0;
                    const rHarga = parseFloat(row.getAttribute('data-harga')) || 0;
                    const rNama = row.getAttribute('data-nama') || '';
                    const rMerk = row.getAttribute('data-merk') || '';
                    const rSatuan = row.getAttribute('data-satuan') || 'Unit';

                    const rKondisiPerUnitStr = row.getAttribute('data-kondisi-per-unit');
                    let rKondisiPerUnit = {};
                    try {
                        rKondisiPerUnit = JSON.parse(rKondisiPerUnitStr) || {};
                    } catch(e) {}

                    // Calculate matching units
                    let matchingUnits = [];
                    for (let i = 1; i <= rJumlah; i++) {
                        const unitKon = rKondisiPerUnit[i] || rKon;
                        if (!kon || unitKon === kon) {
                            matchingUnits.push({ index: i, kondisi: unitKon });
                        }
                    }

                    const matchLab = !lab || rLab === lab;
                    const matchKat = !kat || rKat === kat;
                    const matchKon = matchingUnits.length > 0;
                    const matchSd = !sd || rSd === sd;

                    if (matchLab && matchKat && matchKon && matchSd) {
                        row.style.display = '';
                        visibleCount++;
                        const countForThisRow = kon ? matchingUnits.length : rJumlah;
                        visibleUnits += countForThisRow;
                        visibleNilai += (rHarga * countForThisRow);

                        if (!katStats[rKat]) {
                            katStats[rKat] = { jenis: 0, unit: 0, nilai: 0 };
                        }
                        katStats[rKat].jenis++;
                        katStats[rKat].unit += countForThisRow;
                        katStats[rKat].nilai += (rHarga * countForThisRow);

                        // Update DOM cell contents for unit level clarity
                        const namaCell = row.querySelector('.row-nama');
                        if (namaCell) {
                            let unitBadge = '';
                            if (kon && countForThisRow < rJumlah) {
                                const unitListStr = matchingUnits.map(u => 'Unit ' + u.index).join(', ');
                                unitBadge = `<span class="inline-block mt-1 px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100">${unitListStr}</span>`;
                            }
                            namaCell.innerHTML = `<p class="font-extrabold text-slate-900 text-sm sm:text-base">${rNama}</p>${rMerk ? `<p class="text-xs text-slate-400 font-medium">${rMerk}</p>` : ''}${unitBadge}`;
                        }

                        const kondisiCell = row.querySelector('.row-kondisi');
                        if (kondisiCell) {
                            const activeKondisi = kon || rKon;
                            kondisiCell.innerHTML = `<span class="font-extrabold text-xs sm:text-sm text-slate-900 inline-block">${activeKondisi}</span>`;
                        }

                        const jumlahCell = row.querySelector('.row-jumlah');
                        if (jumlahCell) {
                            jumlahCell.innerHTML = `<span class="font-extrabold text-slate-900 text-sm sm:text-base">${countForThisRow}</span> <span class="text-xs font-medium text-slate-500">${rSatuan}</span>`;
                        }

                        const nilaiCell = row.querySelector('.row-nilai');
                        if (nilaiCell) {
                            nilaiCell.textContent = rHarga ? 'Rp ' + (rHarga * countForThisRow).toLocaleString('id-ID') : '—';
                        }

                        matchingUnits.forEach(u => {
                            if (konStats.hasOwnProperty(u.kondisi)) {
                                konStats[u.kondisi]++;
                            }
                        });

                        const numCell = row.querySelector('.row-number');
                        if (numCell) numCell.textContent = visibleCount;
                    } else {
                        row.style.display = 'none';
                    }
                });

                this.animateCounters(visibleCount, visibleUnits, visibleNilai, 550);

                const countEl = document.getElementById('count-terfilter');
                if (countEl) countEl.textContent = visibleCount + ' barang ditampilkan';

                document.querySelectorAll('.rekap-kategori-row').forEach(kRow => {
                    const kName = kRow.getAttribute('data-kategori');
                    if (katStats[kName]) {
                        kRow.style.display = '';
                        kRow.querySelector('.row-kat-jenis').textContent = katStats[kName].jenis;
                        kRow.querySelector('.row-kat-unit').textContent = katStats[kName].unit;
                        kRow.querySelector('.row-kat-nilai').textContent = 'Rp ' + katStats[kName].nilai.toLocaleString('id-ID');
                    } else {
                        kRow.style.display = 'none';
                    }
                });

                const emptyKatJs = document.getElementById('empty-kat-row-js');
                if (emptyKatJs) {
                    emptyKatJs.style.display = (Object.keys(katStats).length === 0 && visibleCount === 0) ? '' : 'none';
                }

                const emptyRowJs = document.getElementById('empty-row-js');
                if (emptyRowJs) {
                    emptyRowJs.style.display = (visibleCount === 0) ? '' : 'none';
                }

                const totalForProgress = visibleUnits || 1;
                document.querySelectorAll('.rekap-kondisi-block').forEach(cBlock => {
                    const cStatus = cBlock.getAttribute('data-status');
                    const cUnit = konStats[cStatus] || 0;
                    const cPercent = Math.round((cUnit / totalForProgress) * 100);

                    const txt = cBlock.querySelector('.kondisi-text-unit');
                    if (txt) txt.textContent = cUnit + ' unit (' + cPercent + '%)';

                    const bar = cBlock.querySelector('.kondisi-bar-fill');
                    if (bar) bar.style.width = cPercent + '%';
                });
            }
        };
    }

    window.laporanFilter = createLaporanFilter;

    if (window.Alpine) {
        Alpine.data('laporanFilter', createLaporanFilter);
    } else {
        document.addEventListener('alpine:init', () => {
            Alpine.data('laporanFilter', createLaporanFilter);
        });
    }
})();
</script>

@endsection